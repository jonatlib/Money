<?php

namespace Application\View\Helper;

class User extends \Zend\View\Helper\AbstractHelper{
    
    protected $auth;
    
    public function __invoke() {
        list($mail, $domain) = explode('@', $this->auth->getIdentity()->email);
        $name = $this->auth->getIdentity()->name . ' ' . $this->auth->getIdentity()->lastName;
        return (strlen(trim($name)) > 0) ? $name : ucfirst($mail);
    }
    
    public function __construct() {
        $this->auth = new \Zend\Authentication\AuthenticationService();
    }
    
}
