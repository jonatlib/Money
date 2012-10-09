<?php

namespace Application\View\Helper;

class Money extends \Zend\View\Helper\AbstractHelper{
    
    /**
     * @var \Application\Model\Money
     */
    protected $model;
    
    public function setMoneyModel($model){
        $this->model = $model;
    }
    
    /**
     * @return \Application\Model\Money
     */
    public function __invoke() {
        return $this->model;
    }
    
}
