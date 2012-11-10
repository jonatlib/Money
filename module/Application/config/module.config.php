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
                            'route' => '[:controller[/[:action]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'action' => 'index'
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'invokables' => array(
            'mail-adapter' => '\Zend\Mail\Transport\Sendmail',
        ),
        'factories' => array(
            '\Application\Model\Money' => function($sm){
                $auth = new \Zend\Authentication\AuthenticationService();
                return new \Application\Model\Money($sm->get('db-adapter'), $auth->getIdentity()->id);
            },
            'Navigation' => function($sm) {
                $config = \Zend\Config\Factory::fromFile(__DIR__ . '/../config/navigation.xml', true);
                
                $f = new Application\Library\Navigation\Service\ConstructedNavigationFactory($config);
                return $f->createService($sm);
            },
            'translator' => function($sm) {
                $factory = new \Application\Library\I18n\Translator\TranslatorServiceFactory();
                $instance = $factory->createService($sm);

                $log = new \Zend\Log\Logger();
                $log->addWriter(new \Zend\Log\Writer\Stream(__DIR__ . '/../log/translator.log'));

                $instance->setLog($log);
                return $instance;
            },
            'db-adapter' => function($sm) {
                $config = $sm->get('config');
                $config = $config['db'];
                $dbAdapter = new \Zend\Db\Adapter\Adapter($config);
                return $dbAdapter;
            },
            'mail' => function($sm) {
                $adapter = $sm->get('db-adapter');
                $mailadapter = $sm->get('mail-adapter');
                return new \Application\Model\Email('no-replay@no-money.cz', 'No-Money: ', $mailadapter, $adapter, $sm->get('translator'));
            },
        ),
    ),
    'translator' => array(
        'locale' => 'cs_CZ',
        'translation_file_patterns' => array(
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
            'Application\Controller\Money' => 'Application\Controller\MoneyController',
            'Application\Controller\User' => 'Application\Controller\UserController',
            'Application\Controller\Category' => 'Application\Controller\CategoryController',
        ),
        'factories' => array(
            'Application\Controller\Auth' => function(Zend\Mvc\Controller\ControllerManager $cm) {
                /* @var $em Zend\EventManager\SharedEventManager */
                $em = $cm->getServiceLocator()->get('SharedEventManager');
                $instance = new Application\Controller\AuthController();
                $em->attach('Application\Controller\AuthController', 'dispatch', array($instance, 'init'), 10);
                return $instance;
            },
            'Application\Controller\Mobile' => function(Zend\Mvc\Controller\ControllerManager $cm) {
                /* @var $em Zend\EventManager\SharedEventManager */
                $em = $cm->getServiceLocator()->get('SharedEventManager');
                $instance = new Application\Controller\MobileController();
                $em->attach('Application\Controller\MobileController', 'dispatch', array($instance, 'init'), 10);
                return $instance;
            },
            'Application\Controller\Ajax' => function(Zend\Mvc\Controller\ControllerManager $cm) {
                /* @var $em Zend\EventManager\SharedEventManager */
                $em = $cm->getServiceLocator()->get('SharedEventManager');
                $instance = new Application\Controller\AjaxController();
                $em->attach('Application\Controller\AjaxController', 'dispatch', array($instance, 'init'), 10);
                return $instance;
            },
        )
    ),
    'view_helpers' => array(
        'invokables' => array(
            'public' => 'Application\View\Helper\PublicLink',
            'user' => 'Application\View\Helper\User',
            'formSelect' => 'Application\Library\Form\View\Helper\FormSelect'
        ),
        'factories' => array(
            'addMoney' => function($sm) {
                $instance = new \Application\View\Helper\AddMoney();
                $auth = new \Zend\Authentication\AuthenticationService();
                if ($auth->hasIdentity()) {
                    $instance->setMoneyModel($sm->getServiceLocator()->get('\Application\Model\Money'));
                }
                return $instance;
            },
            'money' => function($sm) {
                $instance = new \Application\View\Helper\Money();
                $auth = new \Zend\Authentication\AuthenticationService();
                if ($auth->hasIdentity()) {
                    $instance->setMoneyModel($sm->getServiceLocator()->get('\Application\Model\Money'));
                }
                return $instance;
            },
            'flashMessanger' => function($sm) {
                $instance = new \Application\View\Helper\FlashMessanger();
                $instance->setFlashMessanger($sm->getServiceLocator()
                                ->get('ControllerPluginManager')
                                ->get('flashMessenger'));
                return $instance;
            },
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
        'strategies' => array(
            'ViewJsonStrategy',
        ),
    ),
);
