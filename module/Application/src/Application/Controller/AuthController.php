<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController
{
    
    public function indexAction()
    {
        $view = new ViewModel();
        
        $form = new \Application\Form\Login('aa');
               
        if($this->request->isPost()){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                
            }
        }
        
        $view->form = $form;
        return $view;
    }
    
    public function init() {
        $this->layout('layout/login');
    }
}
