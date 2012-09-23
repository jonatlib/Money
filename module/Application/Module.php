<?php

namespace Application;

use Zend\EventManager\StaticEventManager,
    Zend\Mvc\ModuleRouteListener;

class Module {

    protected $lastRouteMatch;

    public function init() {
        $events = StaticEventManager::getInstance();
        //Plugins
        $events->attach('Zend\Mvc\Application', \Zend\Mvc\MvcEvent::EVENT_ROUTE, array($this, 'pluginLastPage'), 1);
        $events->attach('Zend\Mvc\Application', 'dispatch', array($this, 'pluginAuth'), 100);
        $events->attach('Zend\Mvc\Application', 'dispatch', array($this, 'pluginSession'), 1);
        $events->attach('Zend\Mvc\Application', 'dispatch.error', array($this, 'pluginAuthError'), 100);
        $events->attach('Zend\View\View', 'response', array($this, 'pluginSessionSave'), 100);
        //Bootstrap
        $events->attach('Zend\Mvc\Application', 'bootstrap', array($this, 'initSession'), 100);
        $events->attach('Zend\Mvc\Application', 'bootstrap', array($this, 'initRouter'), 100);
        $events->attach('Zend\Mvc\Application', 'bootstrap', array($this, 'initLocale'), 100);
        $events->attach('Zend\Mvc\Application', 'bootstrap', array($this, 'initView'), 100);
        $events->attach('Zend\Mvc\Application', 'bootstrap', array($this, 'initDebug'), 100);
    }

    ///////////////////////////////////////////////////////////////////////////
    /////////////////////// Bootstrap /////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////

    public function initRouter(\Zend\Mvc\MvcEvent $e) {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $config = $e->getParam('application')->getConfig();

        /* @var $router \Zend\Mvc\Router\Http\TreeRouteStack */
        $router = $e->getApplication()->getServiceManager()->get('Router');
        $router->setBaseUrl($config['baseurl']);

        $xml = \Zend\Config\Factory::fromFile(__DIR__ . '/config/router.xml', true);
        $router->addRoutes($xml);
    }

    public function initLocale(\Zend\Mvc\MvcEvent $e) {
        /* @var $translator \Zend\I18n\Translator\Translator */
        $translator = $e->getApplication()->getServiceManager()->get('translator');
    }

    public function initSession(\Zend\Mvc\MvcEvent $e) {
        $manager = \Zend\Session\Container::getDefaultManager();
        $session = new \Zend\Session\Container('sess', $manager);
        if (!isset($session->init)) {
            $manager->rememberMe(2 * 60 * 60);
            $manager->regenerateId();

            $session->init = time();
            $session->ip = $_SERVER['REMOTE_ADDR'];
            $session->lastPage = $e->getRouteMatch();
        } else {
//            $ip = explode('.', $session->ip);
//            $Cip = explode('.', $_SERVER['REMOTE_ADDR']);
//            if ((time() - $session->init > 2 * 60 * 60) || ($ip[0] != $Cip[0] || $ip[1] != $Cip[2])) {
            if ((time() - $session->init) > 2 * 60 * 60) {
                $manager->destroy(); //FIXME on live server
            }
        }
    }

    public function initView(\Zend\Mvc\MvcEvent $e) {
        $config = $e->getApplication()->getServiceManager()->get('config');
        $e->getParam('application')->getServiceManager()->get('viewhelpermanager')->get('basepath')->setBasePath($config['baseurl']);
    }

    public function initDebug(\Zend\Mvc\MvcEvent $e) {
        $config = $e->getApplication()->getServiceManager()->get('config');
        if ($config['debug'])
            define('DEBUG', true);
    }

    ///////////////////////////////////////////////////////////////////////////
    ////////////////////////// Plugins ///////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////

    public function pluginLastPage(\Zend\Mvc\MvcEvent $e) {
        if (substr_count(strtolower($e->getRouteMatch()->getParam('__CONTROLLER__')), 'ajax'))
            return;
        $this->lastRouteMatch = clone $e->getRouteMatch();
        $this->lastRouteMatch->setParam('controller', $this->lastRouteMatch->getParam('__CONTROLLER__'));
    }

    public function pluginSession() {
        $manager = \Zend\Session\Container::getDefaultManager();

        $session = new \Zend\Session\Container('sess', $manager);
        $session->lastPage = $this->lastRouteMatch;
    }

    public function pluginSessionSave(\Zend\View\ViewEvent $e) {
        \Zend\Session\Container::getDefaultManager()->writeClose();
    }

    public function pluginAuth(\Zend\Mvc\MvcEvent $e) {
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

    public function pluginAuthError(\Zend\Mvc\MvcEvent $e) {
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
