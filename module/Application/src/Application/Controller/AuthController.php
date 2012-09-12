<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController {

    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $authService;

    public function indexAction() {
        $view = new ViewModel();

        $form = new \Application\Form\Login('aa');

        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $adapter = new \Zend\Authentication\Adapter\DbTable($this->getServiceLocator()->get('db-adapter'), 'Users', 'email', 'password');
                $adapter->setIdentity($form->get('email')->getValue());
                $adapter->setCredential($form->get('password')->getValue());

                try {
                    $result = $this->authService->authenticate($adapter);
                    if ($result->isValid()) {
                        $form->resetFailure();
                        $this->redirect()->toRoute('home');
                        return;
                    } else {
                        $form->setFailure();
                        $view->message = 'authWrong';
                    }
                } catch (\Zend\Db\Exception\RuntimeException $e) {
                    $view->message = 'dbRunTimeError';
                } catch (\Zend\Authentication\Exception\RuntimeException $e) {
                    $form->resetFailure();
                    $view->message = 'authRunTimeError';
                } catch (\Exception $e) {
                    $form->resetFailure();
                    $view->message = 'error';
                }
            }else{
                $form->setFailure();
            }
        }

        $view->form = $form;
        return $view;
    }

    public function loginAction() {
        $this->redirect()->toRoute('application/default', array('controller' => 'auth'));
    }

    public function logoutAction() {
        $this->authService->clearIdentity();

        $this->redirect()->toRoute('application/default', array('controller' => 'auth'));

        $view = new ViewModel;
        $view->setTerminal(true);
        return $view;
    }

    public function registerAction() {
        $view = new ViewModel;
        return $view;
    }

    public function lostAction() {
        $view = new ViewModel;
        return $view;
    }

    public function init() {
        $this->layout('layout/login');
        $this->authService = new \Zend\Authentication\AuthenticationService();
    }

}
