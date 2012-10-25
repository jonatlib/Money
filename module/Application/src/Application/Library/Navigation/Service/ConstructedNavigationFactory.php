<?php

namespace Application\Library\Navigation\Service;

use Zend\ServiceManager\ServiceLocatorInterface,
    Zend\Navigation\Service\AbstractNavigationFactory;

/**
 * Constructed factory to set pages during construction.
 *
 * @category  Zend
 * @package   Zend_Navigation
 */
class ConstructedNavigationFactory extends AbstractNavigationFactory {

    private $init = false;
    
    /**
     * @param string|\Zend\Config\Config|array $config
     */
    public function __construct($config) {
        $this->pages = $this->getPagesFromConfig($config);
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return array|null|\Zend\Config\Config
     */
    public function getPages(ServiceLocatorInterface $serviceLocator) {
        if($this->init) return $this->pages;
        
        $application = $serviceLocator->get('Application');
        $routeMatch = $application->getMvcEvent()->getRouteMatch();
        $router = $application->getMvcEvent()->getRouter();

        $this->pages = $this->injectComponents($this->pages, $routeMatch, $router);
        return $this->pages;
    }

    /**
     * @return string
     */
    public function getName() {
        return 'constructed';
    }

}
