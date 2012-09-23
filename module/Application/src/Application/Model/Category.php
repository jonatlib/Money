<?php

namespace Application\Model;

use Zend\Db;

class Category extends \Zend\Db\TableGateway\TableGateway {

    protected $userId = null;

    public function getCategories() {
        $data = $this->select(array('owner' => $this->userId));
        $result = array();
        foreach ($data as $d) {
            $result[$d->id] = $d->name;
        }
        return $result;
    }

    public function addCategory($name) {
        $row = new Db\RowGateway\RowGateway('id', $this->getTable(), $this->getAdapter());
        $row->owner = $this->userId;
        $row->name = $name;
        try {
            return $row->save();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function __construct($adapter, $userId) {
        parent::__construct('Category', $adapter, new \Zend\Db\TableGateway\Feature\RowGatewayFeature('id'));
        $this->userId = $userId;
    }

}
