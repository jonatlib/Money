<?php

namespace Application\Form;

use Application\Form\Protect;

class LostPassword extends Protect {

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
            'type' => '\Zend\Form\Element\Submit',
            'name' => 'submit',
            'options' => array(
                'label' => 'Send'
            ),
            'attributes' => array(
                'value' => 'Send',
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