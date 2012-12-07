<?php

namespace Application\Model;

use Zend\Db;

class Money extends \Zend\Db\TableGateway\TableGateway {

    protected $userId = null;
    protected $start, $stop;

    protected function getNumberOfDays() {
        return (int) ((($this->stop - $this->start) / 3600) / 24);
    }

    protected function getDateWhere() {
        return "`date` <= DATE(FROM_UNIXTIME({$this->stop})) AND `date` >= DATE(FROM_UNIXTIME({$this->start}))";
    }

    public function getCategories() {
        $catgory = new \Application\Model\Category($this->getAdapter(), $this->userId);
        return $catgory->getCategories();
    }

    public function getSubcategories($category) {
        return array('empty');
    }

    public function deleteMoney($id) {
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
    
    public function getMoneys($limit = 20) {
        $where = new Db\Sql\Where();
        $where->equalTo('Money.owner', $this->userId);

        $select = $this->getSql()->select();
        $select->where($where)
                ->join(array('c' => 'Category'), 'category = c.id', array('categName' => 'name'))
                ->join(array('s' => 'Subcategory'), 'subcategory = s.id', array('subcategName' => 'name'), Db\Sql\Select::JOIN_LEFT)
                ->order('Money.date desc, Money.id desc');
        if (!is_null($limit)) {
            $select->limit($limit);
        }
        $data = $this->selectWith($select);
        return $data;
    }

    public function getMonthSpendingByCategoryPerDay(){
        $where = new Db\Sql\Where();
        $where->equalTo('Money.owner', $this->userId);

        $where1 = new Db\Sql\Where();
        $where1->lessThanOrEqualTo('Money.value', 0);

        $select = $this->getSql()->select();
        $select->where(array($where, $where1, $this->getDateWhere()))
                ->columns(array(
                    'sumary' => new Db\Sql\Predicate\Expression("(sum(Money.value) / {$this->getNumberOfDays()})"),
                    'date' => 'date',
                ))
                ->join(array('c' => 'Category'), 'category = c.id', array('categName' => 'name'))
                ->group('c.id');
        $data = $this->getAdapter()->query($select->getSqlString($this->getAdapter()->getPlatform()), Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        return $data;
    }
    
    public function getMonthSpendingByCategory() {
        $where = new Db\Sql\Where();
        $where->equalTo('Money.owner', $this->userId);

        $where1 = new Db\Sql\Where();
        $where1->lessThanOrEqualTo('Money.value', 0);

        $select = $this->getSql()->select();
        $select->where(array($where, $where1, $this->getDateWhere()))
                ->columns(array(
                    'sumary' => new Db\Sql\Predicate\Expression('sum(Money.value)'),
                    'date' => 'date',
                ))
                ->join(array('c' => 'Category'), 'category = c.id', array('categName' => 'name'))
                ->group('date')->group('c.id');
        $data = $this->getAdapter()->query($select->getSqlString($this->getAdapter()->getPlatform()), Db\Adapter\Adapter::QUERY_MODE_EXECUTE);

        return $data;
    }

    public function getMonthSpending() {
        $where = new Db\Sql\Where();
        $where->equalTo('Money.owner', $this->userId);

        $where1 = new Db\Sql\Where();
        $where1->lessThanOrEqualTo('Money.value', 0);

        $select = $this->getSql()->select();
        $select->where(array($where, $where1, $this->getDateWhere()))
                ->columns(array(
                    'sumary' => new Db\Sql\Predicate\Expression('sum(Money.value)'),
                    'date' => 'date',
                ))
                ->group('date');
        $data = $this->getAdapter()->query($select->getSqlString($this->getAdapter()->getPlatform()), Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        return $data;
    }

    public function getMonthEarning() {
        $where = new Db\Sql\Where();
        $where->equalTo('Money.owner', $this->userId);

        $where1 = new Db\Sql\Where();
        $where1->greaterThanOrEqualTo('Money.value', 0);

        $select = $this->getSql()->select();
        $select->where(array($where, $where1, $this->getDateWhere()))
                ->columns(array(
                    'sumary' => new Db\Sql\Predicate\Expression('sum(Money.value)'),
                    'date' => 'date',
                ))
                ->group('date');
        $data = $this->getAdapter()->query($select->getSqlString($this->getAdapter()->getPlatform()), Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        return $data;
    }

    public function getMonthMoney() {
        $where = new Db\Sql\Where();
        $where->equalTo('Comulative.owner', $this->userId);

        $select = new Db\Sql\Select('Comulative');
        $select->where(array($where, $this->getDateWhere()))
                ->columns(array(
                    'sumary' => 'summary',
                    'date' => 'date',
                ));
        $data = $this->getAdapter()->query($select->getSqlString($this->getAdapter()->getPlatform()), Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        return $data;
    }

    public function getMonthBegginMoney() {
        $where = new Db\Sql\Where();
        $where->equalTo('Comulative.owner', $this->userId);

        $select = new Db\Sql\Select('Comulative');
        $select->where(array($where, $this->getDateWhere()))
                ->columns(array(
                    'sumary' => 'summary',
                    'date' => 'date',
                ));
        //FIXME set limit to sql.
        $data = current($this->getAdapter()->query($select->getSqlString($this->getAdapter()->getPlatform()) . ' LIMIT 1', Db\Adapter\Adapter::QUERY_MODE_EXECUTE)->toArray());
        return (int) ((isset($data['sumary'])) ? $data['sumary'] : 0);
    }

    public function getMonthEarningSummary() {
        $where = new Db\Sql\Where();
        $where->equalTo('Money.owner', $this->userId);

        $where1 = new Db\Sql\Where();
        $where1->greaterThanOrEqualTo('Money.value', 0);

        $select = $this->getSql()->select();
        $select->where(array($where, $where1, $this->getDateWhere()))
                ->columns(array(
                    'sumary' => new Db\Sql\Predicate\Expression('sum(Money.value)'),
                ));
        $data = $this->getAdapter()->query($select->getSqlString($this->getAdapter()->getPlatform()), Db\Adapter\Adapter::QUERY_MODE_EXECUTE)->current();
        if (empty($data)) {
            return 0;
        }
        return (int) $data->sumary + ( ($this->getMonthBegginMoney() > 0) ? $this->getMonthBegginMoney() : 0);
    }

    public function getMonthSpendingSummary() {
        $where = new Db\Sql\Where();
        $where->equalTo('Money.owner', $this->userId);

        $where1 = new Db\Sql\Where();
        $where1->lessThanOrEqualTo('Money.value', 0);

        $select = $this->getSql()->select();
        $select->where(array($where, $where1, $this->getDateWhere()))
                ->columns(array(
                    'sumary' => new Db\Sql\Predicate\Expression('sum(Money.value)'),
                ));
        $data = $this->getAdapter()->query($select->getSqlString($this->getAdapter()->getPlatform()), Db\Adapter\Adapter::QUERY_MODE_EXECUTE)->current();
        if (empty($data)) {
            return 0;
        }
        return (int) $data->sumary;
    }

    public function getMonthSumary() {
        $where = new Db\Sql\Where();
        $where->equalTo('Money.owner', $this->userId);

        $select = $this->getSql()->select();
        $select->where(array($where, $this->getDateWhere()))
                ->columns(array(
                    'sumary' => new Db\Sql\Predicate\Expression('sum(Money.value)'),
                ));
        $data = $this->getAdapter()->query($select->getSqlString($this->getAdapter()->getPlatform()), Db\Adapter\Adapter::QUERY_MODE_EXECUTE)->current();
        if (empty($data)) {
            return 0;
        }
        return (int) $data->sumary;
    }

    public function getSumary() {
        $where = new Db\Sql\Where();
        $where->equalTo('Money.owner', $this->userId);

        $select = $this->getSql()->select();
        $select->where($where)
                ->columns(array(
                    'sumary' => new Db\Sql\Predicate\Expression('sum(Money.value)'),
                ));
        $data = $this->getAdapter()->query($select->getSqlString($this->getAdapter()->getPlatform()), Db\Adapter\Adapter::QUERY_MODE_EXECUTE)->current();
        if (empty($data)) {
            return 0;
        }
        return (int) $data->sumary;
    }

    public function getMonthCategorySummary() {
        $where = new Db\Sql\Where();
        $where->equalTo('Money.owner', $this->userId);

        $where1 = new Db\Sql\Where();
        $where1->lessThanOrEqualTo('Money.value', 0);

        $select = $this->getSql()->select();
        $select->where(array($where, $where1, $this->getDateWhere()))
                ->columns(array(
                    'sumary' => new Db\Sql\Predicate\Expression('sum(Money.value)'),
                    'date' => 'date',
                ))
                ->join(array('c' => 'Category'), 'category = c.id', array('categName' => 'name'))
                ->group('c.id');
        $data = $this->getAdapter()->query($select->getSqlString($this->getAdapter()->getPlatform()), Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
        return $data;
    }

    public function __construct($adapter, $userId, $start = null, $stop = null) {
        if(!$start){
            $start = time();
        }
        if(!$stop){
            $stop = time() - 3600 * 24 * 30; 
        }
        if (!is_numeric($start) || !ctype_xdigit($stop)) {
            $this->start = (int) strtotime($start);
        } else {
            $this->start = (int) $start;
        }
        if (!is_numeric($stop) || !ctype_xdigit($stop)) {
            $this->stop = (int) strtotime($stop);
        } else {
            $this->stop = (int) $stop;
        }
        if($this->start > $this->stop){
            $tStop = $this->stop;
            $this->stop = $this->start;
            $this->start = $tStop;
        }
        parent::__construct('Money', $adapter, new \Zend\Db\TableGateway\Feature\RowGatewayFeature('id'));
        $this->userId = (int) $userId;
    }

}
