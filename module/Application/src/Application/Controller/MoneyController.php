<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MoneyController extends AbstractActionController {

    public function indexAction() {
        return new ViewModel();
    }

    public function addAction() {
        $session = new \Zend\Session\Container('sess');
        /* @var $last \Zend\Mvc\Router\RouteMatch */
        $last = $session->lastPage;
        if (isset($last)) {
            return $this->redirect()->toRoute($last->getMatchedRouteName(), $last->getParams());
        } else {
            return $this->redirect()->toRoute('home');
        }
    }

}
