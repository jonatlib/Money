<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MoneyController extends AbstractActionController
{
    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function addAction()
    {
        $view = new ViewModel();
        
        return $view;
    }
}
