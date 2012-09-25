<?php

namespace Application\Model;

use Zend\Db;

class Money extends \Zend\Db\TableGateway\TableGateway {

    protected $userId = null;

    public function getCategories() {
        $catgory = new \Application\Model\Category($this->getAdapter(), $this->userId);
        return $catgory->getCategories();
    }

    public function getSubcategories($category) {
        return array('empty');
    }

    public function deleteMoney($id){
        return $this->delete(array('id' => $id));
    }
    
    public function addMoney($data) {
        $row = new Db\RowGateway\RowGateway('id', $this->getTable(), $this->getAdapter());
        $row->category = $data['group'];
        $row->owner = $this->userId;
        $row->title = $data['title'];
        $row->date = new Db\Sql\Expression('NOW()');
        $row->value = $data['money'];
        try {
            return $row->save();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getMoneys() {
        $where = new Db\Sql\Where();
        $where->equalTo('Money.owner', $this->userId);

        $select = $this->getSql()->select();
        $select->where($where)
                ->join(array('c' => 'Category'), 'category = c.id', array('categName' => 'name'))
                ->join(array('s' => 'Subcategory'), 'subcategory = s.id', array('subcategName' => 'name'), Db\Sql\Select::JOIN_LEFT)
                ->order('Money.date desc, Money.id desc');
        $data = $this->selectWith($select);
        return $data;
    }

    public function getDayMoneySummary() {
        $where = new Db\Sql\Where();
        $where->equalTo('Money.owner', $this->userId);
        
        $where1 = new Db\Sql\Where();
        $where1->lessThanOrEqualTo('Money.value', 0);
        
        $select = $this->getSql()->select();
        $select->where($where)->where($where1)->where('`Money`.`date` <= DATE(NOW()) AND `Money`.`date` > DATE(DATE_ADD(NOW(), INTERVAL -1 MONTH))')
                ->columns(array(
                    'sumary' => new Db\Sql\Predicate\Expression('sum(Money.value)'),
                    'date' => 'date',
                ))
                ->join(array('c' => 'Category'), 'category = c.id', array('categName' => 'name'))
                ->group('Money.date')->group('c.id');
        $data = $this->getAdapter()->query($select->getSqlString($this->getAdapter()->getPlatform()), Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        return $data;
    }

    public function __construct($adapter, $userId) {
        parent::__construct('Money', $adapter, new \Zend\Db\TableGateway\Feature\RowGatewayFeature('id'));
        $this->userId = (int) $userId;
    }

}
