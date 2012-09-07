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
    }

    public function init() {
        $events = StaticEventManager::getInstance();
        $events->attach('Zend\Mvc\Application', 'dispatch', array($this, 'initAuth'), 100);
        $events->attach('Zend\Mvc\Application', 'bootstrap', array($this, 'initCustom'), 100);
    }

    public function initAuth(\Zend\Mvc\MvcEvent $e) {
        $controller = $e->getRouteMatch()->getParam(ModuleRouteListener::ORIGINAL_CONTROLLER);
        if (strtolower($controller) == 'auth')
            return;
        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        $router = $e->getApplication()->getServiceManager()->get('Router');

        $link = $router->assemble(array('controller' => 'auth'), array('only_return_path' => true, 'name' => 'application/default'));

        $response = $e->getResponse();
        $response->setStatusCode(302);
        $response->getHeaders()->addHeaderLine('Location', $link);
        return $response;
    }

    public function initCustom(\Zend\Mvc\MvcEvent $e) {
        
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
