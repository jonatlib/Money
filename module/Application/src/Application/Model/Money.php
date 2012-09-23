<?php

namespace Application\Model;
use Zend\Db;

class Money extends \Zend\Db\TableGateway\TableGateway {
    
    protected $userId = null;
    
    public function addMoney($data){
        return true;    
    }
    
    public function __construct($adapter, $userId) {
        parent::__construct('Money', $adapter, new \Zend\Db\TableGateway\Feature\RowGatewayFeature('id'));
        $this->userId = $userId;
    }
    
}
