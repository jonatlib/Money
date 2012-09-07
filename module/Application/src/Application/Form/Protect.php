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

    public function isValid() {
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
