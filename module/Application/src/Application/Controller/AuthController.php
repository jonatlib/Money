<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AuthController extends AbstractActionController {

    /**
     * @var \Zend\Authentication\AuthenticationService
     */
    protected $authService;

    /**
     * @var \Application\Model\Email
     */
    protected $mail;

    protected function conditionRedirect($logged) {
        if ($logged && $this->authService->hasIdentity()) {
            $this->redirect()->toRoute('home');
            return;
        } else if (!$logged && !$this->authService->hasIdentity()) {
            $this->redirect()->toRoute('home');
            return;
        }
        return;
    }

    public function indexAction() {
        $view = new ViewModel();
        $view->form = $form = new \Application\Form\Login('login');
        
        $this->conditionRedirect(true);
        
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
                        \Zend\Session\Container::getDefaultManager()->regenerateId();
                        $this->conditionRedirect(true);
                        return $view;
                    } else {
                        $form->setFailure();
                        $view->message = 'Wrong password or user name.';
                    }
                } catch (\Zend\Db\Exception\RuntimeException $e) {
                    $view->message = 'Runtime DB error.';
                } catch (\Zend\Authentication\Exception\RuntimeException $e) {
                    $form->resetFailure();
                    $view->message = 'Authentacion error.';
                } catch (\Exception $e) {
                    $form->resetFailure();
                    $view->message = 'Error';
                    \Application\Library\Debug::dThrow($e);
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
        $this->conditionRedirect(false);
        $this->authService->clearIdentity();

        $manager = \Zend\Session\Container::getDefaultManager();
        $manager->regenerateId();
        $manager->destroy();

        $this->redirect()->toRoute('login');
        return $this->getResponse();
    }

    public function registerAction() {
        $view = new ViewModel();

        $this->conditionRedirect(true);
        
        $view->form = $form = new \Application\Form\Register('register');
        $form->addCaptcha();

        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                try {
                    $model = new \Application\Model\User($this->getServiceLocator()->get('db-adapter'));
                    $data = $form->getData();
                    if (($user = $model->registerUser(array_merge($data['loginInfo'], $data['personInfo'])))) {
                        $this->mail->sendTemplate($user->email, 'welcome', array('email' => $user->email));
                        $view->message = 'You ware successfull registered.';
                    } else {
                        $view->message = 'Error';
                    }
                } catch (\Zend\Db\Adapter\Exception\InvalidQueryException $e) {
                    $view->message = 'error';
                    /* @var $e \RuntimeException */
                    switch ((int) $e->getPrevious()->getCode()) {
                        case 23000: $view->message = 'This email is allready registered.';
                            break;
                        default : \Application\Library\Debug::dThrow($e);
                    }
                }
            }
        }

        return $view;
    }

    public function lostAction() {
        $view = new ViewModel();

        $this->conditionRedirect(true);
        
        $view->form = $form = new \Application\Form\LostPassword('lostpassword');
        $form->addCaptcha();

        if ($this->request->isPost()) {
            $form->setData($this->request->getPost());
            if ($form->isValid()) {
                $model = new \Application\Model\Lost($this->getServiceLocator()->get('db-adapter'));
                $email = $form->get('email')->getValue();
                if (($hash = $model->createRequest($email))) {
                    $view->message = 'success';
                    $this->mail->sendTemplate($email, 'lost', array(
                        'link' => 'http://' . $_SERVER['HTTP_HOST'] . $this->url()->fromRoute('lostpassword/default', array('id' => $hash)),
                        'email' => $email));
                } else {
                    $view->message = 'User not found';
                }
            }
        }

        return $view;
    }

    public function retrieveAction() {
        $view = new ViewModel();
        
        $this->conditionRedirect(true);
        
        $view->id = $id = $this->event->getRouteMatch()->getParam('id', null);
        if (is_null($id) || strlen($id) != 40) {
            $this->redirect()->toRoute('application/default', array('controller' => 'Index'));
        }

        $model = new \Application\Model\Lost($this->getServiceLocator()->get('db-adapter'));
        if (($data = $model->resetPassword($id))) {
            $view->message = $data['password'];
            $this->mail->sendTemplate((int) $data['id'], 'recover', array('password' => $data['password']));
        } else {
            $this->redirect()->toRoute('application/default', array('controller' => 'Index'));
        }

        return $view;
    }

    public function init(\Zend\Mvc\MvcEvent $e) {
        $this->layout('layout/login');
        $this->authService = new \Zend\Authentication\AuthenticationService();
        $this->mail = $e->getApplication()->getServiceManager()->get('mail');
    }

}
