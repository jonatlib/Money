<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CategoryController extends AbstractActionController {

    public function indexAction() {
        $view = new ViewModel();
        $auth = new \Zend\Authentication\AuthenticationService();
        $model = new \Application\Model\Category($this->getServiceLocator()->get('db-adapter'), $auth->getIdentity()->id);
        
        $view->data = $model->getCaregoriesList();
        
        return $view;
    }

    public function createAction() {
        $view = new ViewModel;
        $auth = new \Zend\Authentication\AuthenticationService();
        $model = new \Application\Model\Category($this->getServiceLocator()->get('db-adapter'), $auth->getIdentity()->id);

        $view->form = $form = new \Application\Form\AddCategory();
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                if ($model->addCategory($form->get('title')->getValue())) {
                    $this->flashMessenger()->addMessage(array('type' => 'success', 'message' => 'Category created.'));
                    return $this->redirect()->toUrl($_SERVER['REQUEST_URI']);
                }else{
                    $form->get('title')->setMessages(array('Category couldn\'t be created. Maebye it allready exists.'));
                }
            }
        }
        return $view;
    }

    public function editAction(){
        
    }
    
    public function deleteAction(){
        
    }
}
