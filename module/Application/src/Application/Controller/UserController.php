<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    public function indexAction()
    {
        $view = new ViewModel();
        
        return $view;
    }
    
    public function settingsAction()
    {
        $view = new ViewModel();
        
        return $view;
    }
}