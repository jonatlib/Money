<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class UserController extends AbstractActionController {

    public function indexAction() {
        $view = new ViewModel();
        $authService = new \Zend\Authentication\AuthenticationService();
        $user = $authService->getIdentity()->id;
        $model = new \Application\Model\User($this->serviceLocator->get('db-adapter'));
        $view->passwordForm = $passwordForm = new \Application\Form\ChangePassword('changePassword');


        if ($this->request->isPost()) {
            $passwordForm->setData($this->request->getPost());

            if ($passwordForm->isValid()) {
                if (!$model->setPasswordOld($user, $passwordForm->get('oldpassword')->getValue(), $passwordForm->get('password')->getValue())) {
                    $passwordForm->get('oldpassword')->setMessages(array('Wrong old password.'));
                } else {
                    $this->flashMessenger()->addMessage(array('type' => 'success', 'message' => 'Password was successfully changed.'));
                    return $this->redirect()->toUrl($_SERVER['REQUEST_URI']);
                }
            }
        }

        return $view;
    }

    public function settingsAction() {
        $view = new ViewModel();

        return $view;
    }

}
