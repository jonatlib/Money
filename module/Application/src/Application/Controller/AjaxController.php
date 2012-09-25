<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class AjaxController extends AbstractActionController {

    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $auth;
    protected $userId;
    
    public function indexAction() {
        $view = new JsonModel();
        $model = new \Application\Model\Money($this->getServiceLocator()->get('db-adapter'), $this->userId);
        
        $view->data = $model->getMonthCategorySummary();
        
        return $view;
    }
    
    public function init(){
        $this->auth = new \Zend\Authentication\AuthenticationService();
        $this->userId = $this->auth->getIdentity()->id;
    }

}
