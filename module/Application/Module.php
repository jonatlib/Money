<?php
namespace Application;

use Zend\Mvc\ModuleRouteListener;

class Module
{
    public function onBootstrap(\Zend\Mvc\MvcEvent $e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $config = $e->getParam('application')->getConfig();
        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        $router = $e->getApplication()->getServiceManager()->get('Router');
        
        $router->setBaseUrl($config['baseurl']);
        $e->getParam('application')->getServiceManager()->get('viewhelpermanager')->get('basepath')->setBasePath($config['baseurl']);
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
}
