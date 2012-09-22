<?php

namespace Application\View\Helper;

class AddMoney extends \Zend\View\Helper\AbstractHelper{
    
    public function __invoke() {
        return new \Application\Form\AddMoney(array('aa'), array('bb'),
                $this->view->url('application/default', array('controller' => 'money', 'action' => 'add')) );
    }
    
}
