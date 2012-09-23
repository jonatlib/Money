<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController {

    public function indexAction() {
        $view = new ViewModel();
        $auth = new \Zend\Authentication\AuthenticationService();
        $model = new \Application\Model\Money($this->getServiceLocator()->get('db-adapter'), $auth->getIdentity()->id);

        $view->moneys = $model->getMoneys();
        
        return $view;
    }

}
