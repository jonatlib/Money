<?php

namespace Application;

use Zend\EventManager\StaticEventManager,
    Zend\Mvc\ModuleRouteListener;

class Module {

    public function onBootstrap(\Zend\Mvc\MvcEvent $e) {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $config = $e->getParam('application')->getConfig();
        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        $router = $e->getApplication()->getServiceManager()->get('Router');

        $router->setBaseUrl($config['baseurl']);
        $e->getParam('application')->getServiceManager()->get('viewhelpermanager')->get('basepath')->setBasePath($config['baseurl']);
        
        $config = $e->getApplication()->getServiceManager()->get('config');
        if($config['debug']) define ('DEBUG', true);
    }

    public function init() {
        $events = StaticEventManager::getInstance();

        $events->attach('Zend\Mvc\Application', 'dispatch', array($this, 'initAuth'), 100);
        $events->attach('Zend\Mvc\Application', 'dispatch.error', array($this, 'initAuthError'), 100);

        $events->attach('Zend\Mvc\Application', 'bootstrap', array($this, 'initSession'), 100);
        $events->attach('Zend\View\View', 'renderer', array($this, 'saveSession'), -100);
    }

    public function initSession(\Zend\Mvc\MvcEvent $e) {
        $manager = \Zend\Session\Container::getDefaultManager();
        $session = new \Zend\Session\Container('sess', $manager);
        if (!isset($session->init)) {
            $manager->rememberMe(2 * 60 * 60);
            $manager->regenerateId();

            $session->init = time();
            $session->ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = explode('.', $session->ip);
            $Cip = explode('.', $_SERVER['REMOTE_ADDR']);
            if ((time() - $session->init > 2 * 60 * 60) || ($ip[0] != $Cip[0] || $ip[1] != $Cip[2])) {
                //$manager->destroy(); //FIXME on live server
            }
        }
    }

    public function saveSession() {
        \Zend\Session\Container::getDefaultManager()->writeClose();
    }

    public function initAuth(\Zend\Mvc\MvcEvent $e) {
        $authService = new \Zend\Authentication\AuthenticationService();
        if ($authService->hasIdentity()) {
            return;
        }
        $controller = $e->getRouteMatch()->getParam(ModuleRouteListener::ORIGINAL_CONTROLLER);
        if (strtolower($controller) == 'auth')
            return;
        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        $router = $e->getApplication()->getServiceManager()->get('Router');

        $link = $router->assemble(array(), array('only_return_path' => true, 'name' => 'login'));

        $response = $e->getResponse();
        $response->setStatusCode(302);
        $response->getHeaders()->addHeaderLine('Location', $link);
        return $response;
    }
    
    public function initAuthError(\Zend\Mvc\MvcEvent $e){
        $authService = new \Zend\Authentication\AuthenticationService();
        if ($authService->hasIdentity()) {
            return;
        }
        $e->getViewModel()->setTemplate('layout/login');
    }

    ////////////////////////////////////////////////////////////////////////
    /////////////////////// Config /////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

}
