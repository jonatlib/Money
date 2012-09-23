<?php

namespace Application\Form;

use Zend\Form\Form,
    Zend\Form\Element;

class Protect extends Form {

    /**
     * @var \Zend\Session\Container
     */
    private $session;
    private $set = false;

    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    private $auth;

    public function setFailure() {
        if (!isset($this->session->failure)) {
            $this->session->failure = 0;
        }
        $this->session->failure = ((int) $this->session->failure) + 1;
        $this->set = true;
    }

    public function resetFailure() {
        if ($this->set)
            return;
        $this->session->failure = 0;
        $this->remove('captcha');
    }

    public function addCaptcha($force = false) {
        static $added = false;
        $added = ( (false | ( defined('DEBUG') && DEBUG )) && !$force );

        if (($added || $this->auth->hasIdentity()))
            return;

        $element = new Element\Captcha('captcha');
        $adapter = new \Zend\Captcha\ReCaptcha();
        $adapter->setPubkey('6LefWNYSAAAAAMtcdir9eBe97thnOHW0mmo4EQYC');
        $adapter->setPrivkey('6LefWNYSAAAAALi7Y-252ZFP7bEilX6DQKdR0LkW');
        $adapter->setOption('theme', 'white');
        $element->setCaptcha($adapter);
        $this->add($element, array('priority' => -1));

        $added = true;
    }

    private function getFailure() {
        if (isset($this->session->failure) &&
                $this->session->failure >= 3)
            return true;
        return false;
    }

    private function init() {
        $this->session = new \Zend\Session\Container('Protected' . get_called_class(), new \Zend\Session\SessionManager);
        $this->auth = new \Zend\Authentication\AuthenticationService();

        if ($this->getFailure()) {
            $this->addCaptcha();
        }

        $element = new \Zend\Form\Element\Csrf('csrf');
        $inputSpecification = $element->getInputSpecification();
        /* @var $validator \Zend\Validator\Csrf */
        $validator = $inputSpecification['validators'][0];
        if ($validator instanceof \Zend\Validator\Csrf) {
            $validator->setSession($this->session);
            if (!isset($this->session->salt))
                $this->session->salt = 0;
            $validator->setSalt(get_called_class() . $this->session->salt);
        }
        $this->add($element, array('priority' => -1000));

        $name = $this->getName();
        if (!empty($name)) {
            $this->add(array(
                'type' => '\Zend\Form\Element\Hidden',
                'name' => 'formname',
                'options' => array(
                    'required' => false,
                ),
                'attributes' => array(
                    'value' => $name
                )
            ));
        }
    }

    public function isThisSubmited($submitedName = null) {
        $name = $this->getName();
        if (empty($name))
            return true;
        if (!is_null($submitedName)) {
            if (is_array($submitedName)) {
                if (isset($submitedName['formname'])) {
                    return ($name == $submitedName['formname']);
                }
                return true;
            }
            return false;
        }
        if ($this->has('formname')) {
            return ($name == $this->get('formname')->getValue());
        }
        return true;
    }

    public function prepare() {
        parent::prepare();
        foreach ($this->getIterator() as $element) {
            $required = ($element->getOption('required')) ? true : false;
            if ($required) {
                /* @var $element \Zend\Form\Element */
                $class = $element->getAttribute('class');
                $class .= ( empty($class) ? 'required' : ' required' );
                $element->setAttribute('class', $class);
            }
        }
    }

    private function initFilter() {
        ////// ZF1 like
        $filter = new \Zend\InputFilter\BaseInputFilter();
        foreach ($this->getIterator() as $element) {
            $e = new \Zend\InputFilter\Input($element->getName());

            $required = ($element->getOption('required')) ? true : false;
            $e->setRequired($required);

            $validators = new \Zend\Validator\ValidatorChain();
            if (!is_null($element->getOption('validators'))) {
                foreach ($element->getOption('validators') as $k => $v) {
                    $validators->addByName($k, $v);
                }
            }
            if ($element instanceof \Zend\Form\Element\Csrf) {
                $csrf = $element->getInputSpecification();
                $validators->addValidator($csrf['validators'][0]);
            }
            $e->setValidatorChain($validators);

            $filters = new \Zend\Filter\FilterChain();
            if (!is_null($element->getOption('filters'))) {
                foreach ($element->getOption('filters') as $k => $v) {
                    $filters->attachByName($k, $v);
                }
            }
            $e->setFilterChain($filters);

            $filter->add($e);
        }
        $this->setInputFilter($filter);
    }

    public function isValid() {
        $this->initFilter();
        if (!$this->isThisSubmited($this->data)) {
            return false;
        }
        return parent::isValid();
    }

    public function __construct($name = null) {
        parent::__construct($name);
        $this->init();
    }

}
