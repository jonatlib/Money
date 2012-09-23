<?php

namespace Application\View\Helper;

class FlashMessanger extends \Zend\View\Helper\AbstractHelper{
        
    /**
     * @var \Zend\Mvc\Controller\Plugin\FlashMessenger
     */
    protected $flashMessanger;
    
    public function __invoke($namespace = null) {
        if(!is_null($namespace)){
            $this->flashMessanger->setNamespace($namespace);
        }
        return $this->flashMessanger;
    }
    
    public function setFlashMessanger($flash){
        $this->flashMessanger = $flash;
    }
    
}
