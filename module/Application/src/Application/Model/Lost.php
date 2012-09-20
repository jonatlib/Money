<?php

namespace Application\Model;
use Zend\Db;

class Lost extends \Zend\Db\TableGateway\TableGateway {
    /**
     * @var User
     */
    protected $userModel;    
    
    public function createRequest($email){
        $users = $this->userModel->select(array('email' => $email));
        if($users->count() < 1){
            return false;
        }
        /* @var $user \Zend\Db\RowGateway\RowGateway */
        $user = $users->current();
        $old = $this->select(array('user' => $user->id));
        foreach($old as $o){
            $this->delete(array('id' => $o->id));
        }
        
        $row = new Db\RowGateway\RowGateway('id', $this->getTable(), $this->getAdapter());
        $row->id = sha1(rand(0, time()) . User::getStaticSalt() . $email );
        $row->user = $user->id;
        $row->save();
        return $row->id;
    }
    
    public function resetPassword($hash){
        $hashs = $this->select(array('id' => $hash));
        if($hashs->count() < 1){
            return false;
        }
        $hash = $hashs->current();
        
        $password = substr(sha1(time()), 0, 6);
        $this->userModel->setPassword($hash->user, $password);
        
        foreach ($hashs as $h){
            $this->delete(array('id' => $h->id));
        }
        
        return array('id' => $hash->user, 'password' => $password);
    }
    
    public function __construct($adapter) {
        parent::__construct('Lost', $adapter, new \Zend\Db\TableGateway\Feature\RowGatewayFeature('id'));
        $this->userModel = new User($adapter);
    }
    
}
