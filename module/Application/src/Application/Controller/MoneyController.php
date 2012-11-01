<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MoneyController extends AbstractActionController {

    public function indexAction() {
        return new ViewModel();
    }

    public function addAction() {
        $auth = new \Zend\Authentication\AuthenticationService();
        $model = $this->getServiceLocator()->get('\Application\Model\Money');

        $form = new \Application\Form\AddMoney($model->getCategories(), '');
        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {

                if ($model->addMoney($form->getData())) {
                    $this->flashMessenger()->addMessage(array('type' => 'success', 'message' => 'Money was successfully added.'));
                } else {
                    $this->flashMessenger()->addMessage(array('type' => 'warning', 'message' => 'There was an error while adding money. Try again please.'));
                }
            } else {
                $this->flashMessenger()->addMessage(array('type' => 'error', 'message' => 'Money has to be number. And title have to be set.'));
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

    public function deleteAction() {
        $auth = new \Zend\Authentication\AuthenticationService();
        $model = $this->getServiceLocator()->get('\Application\Model\Money');

        $id = $this->event->getRouteMatch()->getParam('id', null);
        if (is_null($id)) {
            $this->redirect()->toRoute('application/default', array('controller' => 'Index'));
        }
        
        if ($model->deleteMoney($id)) {
            $this->flashMessenger()->addMessage(array('type' => 'success', 'message' => 'Money was successfully deleted.'));
        } else {
            $this->flashMessenger()->addMessage(array('type' => 'warning', 'message' => 'There was an error while deleting money. Try again please.'));
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
