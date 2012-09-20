<?php

return array(
    'router' => array(
        'routes' => array(
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            'db-adapter' => function($sm) {
                $config = $sm->get('config');
                $config = $config['db'];
                $dbAdapter = new \Zend\Db\Adapter\Adapter($config);
                return $dbAdapter;
            },
            'mail' => function($sm){
                $adapter = $sm->get('db-adapter');
                return new \Application\Model\Email('no-replay@no-money.cz', 'No-Money: ', $adapter, $sm->get('translator'));
            },
        ),
    ),
    'translator' => array(
        'locale' => 'cs_CZ',
        'translation_patterns' => array(
            array(
                'type' => 'phparray',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.php',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
//            'Application\Controller\Auth' => 'Application\Controller\AuthController'
        ),
        'factories' => array(
            'Application\Controller\Auth' => function(Zend\Mvc\Controller\ControllerManager $cm) {
                /* @var $em Zend\EventManager\SharedEventManager */
                $em = $cm->getServiceLocator()->get('SharedEventManager');
                $instance = new Application\Controller\AuthController();
                $em->attach('Application\Controller\AuthController', 'dispatch', array($instance, 'init'), 1000);
                return $instance;
            },
        )
    ),
    'view_helpers' => array(
        'invokables' => array(
            'public' => 'Application\View\Helper\PublicLink'
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'layout/login' => __DIR__ . '/../view/layout/login.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
);
