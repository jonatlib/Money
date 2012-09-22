<?php

namespace Application\Form;

use Application\Form\Protect;

class AddMoney extends Protect {

    public function __construct($money, $groups, $subgroups, $action) {
        parent::__construct('addMoney');
        $this->setAttribute('action', $action);
        
        $this->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'money',
            'options' => array(
                'required' => true,
                'validators' => array(
                    '\Zend\Validator\Int' => array()
                )
            ),
            'attributes' => array(
                'class' => 'input-medium',
                'id' => 'money',
                'placeholder' => $money
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
            'type' => '\Zend\Form\Element\Select',
            'name' => 'subgroup',
            'options' => array(
                'required' => true,
                'validators' => array(
                    
                ),
                'value_options' => $subgroups
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