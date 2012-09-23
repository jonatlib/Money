<?php

namespace Application\Form;

use Application\Form\Protect;

class AddMoney extends Protect {

    public function __construct($groups, $action) {
        parent::__construct('addMoney');
        $this->setAttribute('action', $action);
        
        $this->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'title',
            'options' => array(
                'required' => true,
                'validators' => array(
                )
            ),
            'attributes' => array(
                'class' => 'input-medium',
                'id' => 'title',
                'placeholder' => 'Title'
            )
        ));
        $this->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'money',
            'options' => array(
                'required' => true,
                'validators' => array(
                    '\Zend\Validator\Regex' => array('pattern' => '/^(\-)?[0-9]+([\,\.][0-9]+)?$/')
                )
            ),
            'attributes' => array(
                'class' => 'input-medium',
                'id' => 'money',
                'placeholder' => 'Money'
            )
        ));
        $this->add(array(
            'type' => '\Zend\Form\Element\Select',
            'name' => 'group',
            'options' => array(
                'required' => true,
                'validators' => array(
                    
                ),
                'value_options' => $groups
            ),
            'attributes' => array(
                'class' => 'input-medium',
            )
        ));
        
        $this->add(array(
            'type' => '\Zend\Form\Element\Submit',
            'name' => 'add',
            'options' => array(
                'label' => 'Add'
            ),
            'attributes' => array(
                'value' => 'Add',
                'class' => 'btn btn-inverse btn-large'
            )
        ), array('priority' => -10));
    }
}