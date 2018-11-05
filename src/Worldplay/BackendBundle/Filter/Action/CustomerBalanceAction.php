<?php

namespace Worldplay\BackendBundle\Filter\Action;


use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Filter\FilterBundle\Service\Filter\Property;
use Filter\FilterBundle\Service\Filter\Action;

class CustomerBalanceAction implements Action
{

    private $id;
    private $name;
    private $balance;
    private $max;
    private $status;

    public function __construct($id = 'id', $name = 'name', $status = 'status', $balance = 'balance', $max = 'payments.receivedAt')
    {
        $this->id = $id;
        $this->name = $name;
        $this->balance = $balance;
        $this->max = $max;
        $this->status = $status;
    }

    public function prepare(QueryBuilder $qb, callable $alias, Property $root)
    {
        $qb->select(sprintf('%s AS _id', $alias($this->id)))
            ->addSelect(sprintf('%s AS _name', $alias($this->name)))
            ->addSelect(sprintf('%s AS _balance', $alias($this->balance)))
            ->addSelect(sprintf('%s AS _status', $alias($this->status)))
            ->addSelect(sprintf('MAX(DATE(%s)) AS _date', $alias($this->max)))
            ->addGroupBy('_id')
            ->addGroupBy('_name')
            ->addGroupBy('_status')
            ->addGroupBy('_balance');
    }

    public function execute(Query $query)
    {
        $rows = $query->getArrayResult();

        $result = $rows;

        return $result;
    }
}
