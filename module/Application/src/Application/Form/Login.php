<?php

namespace Application\Form;

use Application\Form\Protect;

class Login extends Protect {

    public function __construct($name = null) {
        parent::__construct($name);

        $this->add(array(
            'type' => '\Zend\Form\Element\Email',
            'name' => 'email',
            'options' => array(
                'label' => 'Email',
                'required' => true,
                'validators' => array(
                    '\Zend\Validator\EmailAddress' => array()
                )
            ),
            'attributes' => array(
                'class' => 'input-xlarge',
                'id' => 'email'
            )
        ));
        $this->add(array(
            'type' => '\Zend\Form\Element\Password',
            'name' => 'password',
            'options' => array(
                'label' => 'Password',
                'required' => true,
                'validators' => array(
                    '\Zend\Validator\StringLength' => array('min' => 6)
                )
            ),
            'attributes' => array(
                'class' => 'input-xlarge',
                'id' => 'password'
            )
        ));
        $this->add(array(
            'type' => '\Zend\Form\Element\Submit',
            'name' => 'submit',
            'options' => array(
            ),
            'attributes' => array(
                'value' => 'Login',
                'class' => 'btn btn-primary btn-large left'
            )
        ), array('priority' => -10));
        $this->add(array(
            'type' => '\Zend\Form\Element\Button',
            'name' => 'reset',
            'options' => array(
            ),
            'attributes' => array(
                'value' => 'Reset',
                'class' => 'btn btn-primary btn-large right'
            )
         ), array('priority' => -10));
    }

}