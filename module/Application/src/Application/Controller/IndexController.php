<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController {

    protected function getModel(){
        $auth = new \Zend\Authentication\AuthenticationService();
        return $this->getServiceLocator()->get('\Application\Model\Money');
    }
    
    public function indexAction() {
        $view = new ViewModel();
        
        $model = $this->getModel();        
        $view->moneys = $model->getMoneys(5);
        
        return $view;
    }
    
    public function historyAction() {
        $view = new ViewModel();
        
        $model = $this->getModel();
        $view->moneys = $model->getMoneys();
        
        return $view;
    }

}
