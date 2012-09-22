<?php

namespace Application\Form;

use Application\Form\Protect;

class ChangePassword extends Protect {

    public function __construct($name = null) {
        parent::__construct($name);
         
        $this->add(array(
            'type' => '\Zend\Form\Element\Password',
            'name' => 'password',
            'options' => array(
                'label' => 'Password (min. 6 chars)',
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
            'type' => '\Zend\Form\Element\Password',
            'name' => 'passwordtwo',
            'options' => array(
                'label' => 'Password again',
                'required' => true,
                'validators' => array(
                    '\Zend\Validator\Identical' => array('token' => 'password')
                )
            ),
            'attributes' => array(
                'class' => 'input-xlarge',
                'id' => 'passwordtwo'
            )
        ));
        
        $this->addCaptcha(true);
        ////////////////////////////////////////////////////////////////
        // Buttons
        $this->add(array(
            'type' => '\Zend\Form\Element\Submit',
            'name' => 'submit',
            'options' => array(
                'label' => 'Save'
            ),
            'attributes' => array(
                'value' => 'Save',
                'class' => 'btn btn-primary btn-large'
            )
        ), array('priority' => -10));
    }

}