<?php

namespace Application\Form;

use Zend\Form\Form,
    Zend\Form\Element;

class Protect extends Form {

    /**
     * @var \Zend\Session\Container
     */
    private $session;

    public function setFailure() {
        if (!isset($this->session->failure)) {
            $this->session->failure = 0;
        }
        $this->session->failure = ((int) $this->session->failure) + 1;
    }

    public function resetFailure() {
        $this->session->failure = 0;
        $this->remove('captcha');
    }

    private function getFailure() {
        if (isset($this->session->failure) &&
                $this->session->failure >= 3)
            return true;
        return false;
    }

    private function init() {
        $this->session = new \Zend\Session\Container('Protected' . get_called_class(), new \Zend\Session\SessionManager);

        if ($this->getFailure()) {
            $element = new Element\Captcha('captcha');
            $element->setCaptcha(new \Zend\Captcha\Dumb());
            $this->add($element, array('priority' => -1));
        }

        $element = new \Zend\Form\Element\Csrf('csrf');
        $inputSpecification = $element->getInputSpecification();
        /* @var $validator \Zend\Validator\Csrf */
        $validator = $inputSpecification['validators'][0];
        if ($validator instanceof \Zend\Validator\Csrf) {
            $validator->setSession($this->session);
            $validator->setSalt(get_called_class());
        }
        $this->add($element, array('priority' => -1000));
    }

    private function initFilter(){
        ////// ZF1 like
        $filter = new \Zend\InputFilter\BaseInputFilter();
        foreach($this->getIterator() as $element){
            $e = new \Zend\InputFilter\Input($element->getName());
            $e->setRequired( ($element->getOption('required')) ? true : false );
            
            $validators = new \Zend\Validator\ValidatorChain();
            if(!is_null($element->getOption('validators'))){
                foreach($element->getOption('validators') as $k => $v){
                    $validators->addByName($k, $v);
                }
            }
            if($element instanceof \Zend\Form\Element\Csrf){
                $csrf = $element->getInputSpecification();
                $validators->addValidator($csrf['validators'][0]);
            }
            $e->setValidatorChain($validators);
            
            $filters = new \Zend\Filter\FilterChain();
            if(!is_null($element->getOption('filters'))){
                foreach($element->getOption('filters') as $k => $v){
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
        $result = parent::isValid();
        if (!$result) {
            $this->setFailure();
        } else {
            $this->resetFailure();
        }
        return $result;
    }

    public function __construct($name = null) {
        parent::__construct($name);
        $this->init();
    }

}
