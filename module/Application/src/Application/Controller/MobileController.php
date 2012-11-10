<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MobileController extends AbstractActionController {

    public function indexAction() {
        
    }
    
    public function init(\Zend\Mvc\MvcEvent $e) {
        $this->layout('layout/mobile');
    }

}
