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

        $view->form = $form = new \Application\Form\Login('login');

        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $staticSalt = \Application\Model\User::getStaticSalt();
                $adapter = new \Zend\Authentication\Adapter\DbTable(
                        $this->getServiceLocator()->get('db-adapter'), 'Users', 'email', 'password',
                        "SHA1(CONCAT(?, CONCAT(salt, '{$staticSalt}'))) && ( deleted != 1 )");

                $adapter->setIdentity($form->get('email')->getValue());
                $adapter->setCredential($form->get('password')->getValue());

                try {
                    $result = $this->authService->authenticate($adapter);
                    if ($result->isValid()) {
                        $form->resetFailure();
                        $this->redirect()->toRoute('home');
                        return $view;
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
            } else {
                $form->setFailure();
            }
        }

        return $view;
    }

    public function loginAction() {
        $this->redirect()->toRoute('application/default', array('controller' => 'auth'));
    }

    public function logoutAction() {
        $this->authService->clearIdentity();

        $manager = \Zend\Session\Container::getDefaultManager();
        $manager->regenerateId();
        $manager->destroy();

        $this->redirect()->toRoute('login');
        return $this->getResponse();
    }

    public function registerAction() {
        $view = new ViewModel();

        $view->form = $form = new \Application\Form\Register('register');
        $form->addCaptcha();

        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                try {
                    $model = new \Application\Model\User($this->getServiceLocator()->get('db-adapter'));
                    $data = $form->getData();
                    if ($model->registerUser(array_merge($data['loginInfo'], $data['personInfo']))) {
                        $view->message = 'success';
                    } else {
                        $view->message = 'errorUnknown';
                    }
                } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
                    $view->message = 'error';
                    /* @var $e \RuntimeException */
                    switch ((int) $e->getPrevious()->getCode()) {
                        case 23000: $view->message = 'errorUnique';
                            break;
                        default : \Application\Library\Debug::dThrow($e);
                    }
                }
            }
        }

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
