<?php

namespace Application\Form;

use Application\Form\Protect,
    \Zend\Form;

class Register extends Protect {

    public function __construct($name = null) {
        parent::__construct($name);
        
        
        $loginInfo = new Form\Fieldset('loginInfo');
        $loginInfo->setLabel('Login Info');
                
        $loginInfo->add(array(
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
        $loginInfo->add(array(
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
        $loginInfo->add(array(
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
        $this->add($loginInfo);
        
        
        $personInfo = new Form\Fieldset('personInfo');
        $personInfo->setLabel('Personal Info');
        $personInfo->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'name',
            'options' => array(
                'label' => 'First Name',
                'required' => false
            ),
            'attributes' => array(
                'class' => 'input-xlarge',
                'id' => 'firstname'
            )
        ));
        $personInfo->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'lastName',
            'options' => array(
                'label' => 'Last Name',
                'required' => false
            ),
            'attributes' => array(
                'class' => 'input-xlarge',
                'id' => 'lastname'
            )
        ));
        $this->add($personInfo);
        
        ////////////////////////////////////////////////////////////////
        // Buttons
        $this->add(array(
            'type' => '\Zend\Form\Element\Submit',
            'name' => 'submit',
            'options' => array(
                'label' => 'Register'
            ),
            'attributes' => array(
                'value' => 'Register',
                'class' => 'btn btn-primary btn-large left'
            )
        ), array('priority' => -10));
        $this->add(array(
            'type' => '\Zend\Form\Element\Button',
            'name' => 'reset',
            'options' => array(
                'label' => 'Reset'
            ),
            'attributes' => array(
                'value' => 'Reset',
                'class' => 'btn btn-primary btn-large right'
            )
         ), array('priority' => -10));
    }

}