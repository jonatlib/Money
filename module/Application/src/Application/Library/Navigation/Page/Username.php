<?php

namespace Application\Library\Navigation\Page;
use Zend\Navigation\Page\Uri as UriPage;

class Username extends UriPage implements \Application\Library\Navigation\CanTranslate{
    
    protected $auth;
    
    public function __construct($options = null) {
        parent::__construct($options);
        $this->auth = new \Zend\Authentication\AuthenticationService();
    }
    
    public function getLabel() {
        if(!$this->auth->hasIdentity()) return 'Guest';
        list($mail, $domain) = explode('@', $this->auth->getIdentity()->email);
        $name = $this->auth->getIdentity()->name . ' ' . $this->auth->getIdentity()->lastName;
        return (strlen(trim($name)) > 0) ? $name : ucfirst($mail);
    }
    
    public function getUri() {
        return '#';
    }

    public function cantranslate() {
        return false;
    }
    
}