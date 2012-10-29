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
    /**
     * @var \Application\Model\Money
     */
    protected $model;
    
    public function indexAction() {
        $view = new JsonModel();
        $view->data = $this->model->getMonthCategorySummary();
        return $view;
    }
    
    public function summaryAction(){
        $view = new JsonModel();
        $view->data = $this->model->getMonthSumary();
        return $view;
    }
    
    public function moneyAction(){
        $view = new JsonModel();
        $view->data = $this->model->getMonthMoney();
        return $view;
    }
    
    public function spendingAction(){
        $view = new JsonModel();
        $view->data = $this->model->getMonthSpending();
        return $view;
    }
    
    public function earningAction(){
        $view = new JsonModel();
        $view->data = $this->model->getMonthEarning();
        return $view;
    }
    
    public function spendingcategoryAction(){
        $view = new JsonModel();
        $view->data = $this->model->getMonthSpendingByCategory();
        return $view;
    }
    
    public function linegraphAction(){
        $view = new JsonModel();
        $data = array('Date' => array('Date'));
        
        $d = $this->model->getMonthSpendingByCategory()->toArray();
        foreach($d as $val){
            if(!in_array($val['categName'], $data['Date']))
                $data['Date'][] = $val['categName'];
        }
        foreach($d as $val){
            $index = array_search($val['categName'], $data['Date']);
            if(empty($data[$val['date']])){
                foreach($data['Date'] as $i => $v) $data[$val['date']][$i] = 0;
                $data[$val['date']][0] = $val['date'];
            }
            $data[$val['date']][$index] = (int) abs($val['sumary']);
        }
        $view->data = array_values($data);
        return $view;
    }
    
    public function translateAction(){
        $view = new JsonModel();
        
        if(isset($_GET['text']) && !empty($_GET['text'])){
            $translator = $this->serviceLocator->get('translator');
            $view->text = $translator->translate($_GET['text']);
        }else{
            $view->text = 'unknown';
        }
        
        return $view;
    }
    
    public function init(){
        $this->auth = new \Zend\Authentication\AuthenticationService();
        $this->userId = $this->auth->getIdentity()->id;
        $this->model = new \Application\Model\Money($this->getServiceLocator()->get('db-adapter'), $this->userId);
    }

}
