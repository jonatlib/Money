<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MoneyController extends AbstractActionController {

    public function indexAction() {
        return new ViewModel();
    }

    public function addAction() {
        $form = new \Application\Form\AddMoney(array('aa'), array('bb'), '');
        if($this->request->isPost()){
            $form->setData($this->request->getPost());
            if($form->isValid()){
                $auth = new \Zend\Authentication\AuthenticationService();
                $model = new \Application\Model\Money($this->getServiceLocator()->get('db-adapter'), $auth->getIdentity()->id);
                
                if($model->addMoney($form->getData())){
                    $this->flashMessenger()->addMessage(array('type' => 'success', 'message' => 'Money was successfully added.'));
                }else{
                    $this->flashMessenger()->addMessage(array('type' => 'warning', 'message' => 'There was an error while adding money. Try again please.'));
                }
            }else{
                $this->flashMessenger()->addMessage(array('type' => 'error', 'message' => 'Money has to be number.'));
            }
        }
        
        $session = new \Zend\Session\Container('sess');
        /* @var $last \Zend\Mvc\Router\RouteMatch */
        $last = $session->lastPage;
        if (isset($last)) {
            return $this->redirect()->toRoute($last->getMatchedRouteName(), $last->getParams());
        } else {
            return $this->redirect()->toRoute('home');
        }
    }

}
