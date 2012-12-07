<?php

namespace Application\Model;

use Zend\Db;

class UserVars extends \Zend\Db\TableGateway\TableGateway {

    protected $user;

    public function setVariable($name, $value) {
        $row = new Db\RowGateway\RowGateway(array('name', 'user'), $this->getTable(), $this->getAdapter());
        $row->user = $this->user;
        $row->name = $name;
        $row->value = $value;
        try {
            return $row->save();
        } catch (\Exception $e) {
            if (23000 == $e->getPrevious()->getCode()) {
                /* @var $data \Zend\Db\ResultSet\ResultSet */
                if (($data = $this->getData($name)) !== false) {
                    try {
                        $this->update(array('value' => $value), array('name' => $data->name, 'user' => $this->user));
                    } catch (\Exception $ex) {}
                }
            }
            return false;
        }
    }

    public function getVariable($name) {
        if (($var = $this->getData($name)) === false)
            return false;
        return $var->value;
    }

    private function getData($name) {
        $var = $this->select(array('name' => $name, 'user' => $this->user));
        if ($var->count() < 1) {
            return false;
        }
        return $var->current();
    }

    public function __construct(Db\Adapter\Adapter $adapter, $user) {
        $this->user = (int) intval($user);
        parent::__construct('UserVars', $adapter, new \Zend\Db\TableGateway\Feature\RowGatewayFeature(array('name', 'user')));
    }

}
