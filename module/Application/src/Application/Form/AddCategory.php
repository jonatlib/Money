<?php

namespace Application\Form;

use Application\Form\Protect;

class AddCategory extends Protect {

    public function __construct() {
        parent::__construct('addCategory');
        
        $this->add(array(
            'type' => '\Zend\Form\Element\Text',
            'name' => 'title',
            'options' => array(
                'label' => 'Title',
                'required' => true,
                'validators' => array(
                )
            ),
            'attributes' => array(
                'class' => 'input-xlarge',
                'id' => 'title',
            )
        ));
       
        
        $this->add(array(
            'type' => '\Zend\Form\Element\Submit',
            'name' => 'add',
            'options' => array(
                'label' => 'Create'
            ),
            'attributes' => array(
                'value' => 'Create',
                'class' => 'btn btn-primary btn-large'
            )
        ), array('priority' => -10));
    }
}