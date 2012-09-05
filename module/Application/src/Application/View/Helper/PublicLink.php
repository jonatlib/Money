<?php

namespace Application\View\Helper;

class PublicLink extends \Zend\View\Helper\AbstractHelper{
    
    public function __invoke($path) {
        return $this->view->basePath( '/public/' . ltrim($path, '/') );
    }
    
}
