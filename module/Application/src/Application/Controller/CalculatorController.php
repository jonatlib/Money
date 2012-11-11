<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class CalculatorController extends AbstractActionController {

    /**
     * @return \Application\Model\Money
     */
    protected function getModel() {
        /* @var $model \Application\Model\Money */
        return $this->getServiceLocator()->get('\Application\Model\Money');
    }

    public function indexAction() {
        return new ViewModel(array( 'money' => $this->getModel()->getMonthSpendingByCategoryPerDay() ));
    }

}
