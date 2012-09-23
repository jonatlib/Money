<?php

namespace Application\View\Helper;

class AddMoney extends \Zend\View\Helper\AbstractHelper {

    /**
     * @var \Application\Model\Money
     */
    protected $model = null;

    public function setMoneyModel($model) {
        $this->model = $model;
    }

    public function __invoke() {
        if (is_null($this->model))
            return;
        return new \Application\Form\AddMoney($this->model->getCategories(),
                        $this->view->url('application/default', array('controller' => 'money', 'action' => 'add')));
    }

}
