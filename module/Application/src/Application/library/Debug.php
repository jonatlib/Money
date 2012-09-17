<?php

namespace Application\Library;

class Debug extends \Zend\Debug\Debug{
    public static function dump($var, $label = null, $echo = true) {
        if(defined('DEBUG') && DEBUG){
            parent::dump($var, $label, $echo);
        }
        return;
    }
    
    public static function dThrow($e){
        if(defined('DEBUG') && DEBUG)
            throw $e;
    }
}