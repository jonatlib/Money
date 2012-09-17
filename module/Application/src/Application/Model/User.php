<?php

namespace Application\Model;
use Zend\Db;

class User extends \Zend\Db\TableGateway\TableGateway {
    
    private static $STATIC_SALT = '64a3f1d07d4ea98e6f2ccec179d6714897fc02d3';
    
    public static final function getStaticSalt(){
        return self::$STATIC_SALT;
    }
    
    public function registerUser(array $data){
        $row = new Db\RowGateway\RowGateway('id', $this->getTable(), $this->getAdapter());
        $row->email = $data['email'];
        $row->name = $data['name'];
        $row->lastName = $data['lastName'];
        $row->salt = $salt = sha1( rand(0, time()) );
        $row->password = sha1($data['password'] . $salt . self::$STATIC_SALT);
        $row->deleted = 0;
        $row->register = new Db\Sql\Expression('NOW()');
        $row->role = 'user';
        return $row->save();
    }
    
    public function __construct($adapter) {
        parent::__construct('Users', $adapter, new \Zend\Db\TableGateway\Feature\RowGatewayFeature('id'));
    }
    
}
