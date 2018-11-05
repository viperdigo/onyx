<?php

namespace Worldplay\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CustomerRepository extends EntityRepository
{
    public function reportAccount($customerId, $dateStart = false, $dateEnd = false)
    {
        $conn = $this->getEntityManager('')->getConnection();

        $params = array(
            'customerId' => $customerId,
        );

        $dateStart = \DateTime::createFromFormat('d/m/Y', $dateStart);
        $dateEnd = \DateTime::createFromFormat('d/m/Y', $dateEnd);

        $andWhere = '';
        if ($dateStart && $dateEnd) {
            $dateStart = $dateStart->format('Y-m-d');
            $dateEnd = $dateEnd->format('Y-m-d');
            $andWhere .= " AND se.createdAt BETWEEN '$dateStart 00:00:00' AND '$dateEnd 23:59:59' ";
        } elseif ($dateStart) {
            $dateStart = $dateStart->format('Y-m-d');
            $andWhere .= " AND se.createdAt >= '$dateStart 00:00:00'";
        } elseif ($dateEnd) {
            $dateEnd = $dateEnd->format('Y-m-d');
            $andWhere .= " AND se.createdAt <= '$dateEnd 23:59:59'";
        }

        $sql = <<<SQL
SELECT
	se.type,
	se.typeId,
	se.customer,
	se.createdAt,
	se.note,
	se.product,
	se.quantity,
	se.soldValue,
	se.amount
FROM (SELECT
	      'Payment'     type,
	      p.id          typeId,
	      c.id          customer,
	      p.received_at createdAt,
	      p.note        note,
	      ''            product,
	      ''            quantity,
	      ''            soldValue,
	      p.amount      amount
      FROM customers c JOIN payments p
		      ON c.id = p.fk_customer
      UNION
      SELECT
	      'Order'        type,
	      o.id           typeId,
	      c.id           customer,
	      o.created_at   createdAt,
	      o.note         note,
	      pr.description product,
	      oi.quantity    quantity,
	      oi.sold_value  soldValue,
	      oi.subtotal    amount
      FROM customers c
	      JOIN orders o
		      ON c.id = o.fk_customer
	      JOIN order_items oi
		      ON o.id = oi.fk_order
	      JOIN products pr
		      ON pr.id = oi.fk_product) se
WHERE se.customer = :customerId
{$andWhere}
ORDER BY se.createdAt ASC, se.type DESC;
SQL;

        $st = $conn->executeQuery($sql, $params);

        return $st->fetchAll();

    }

    public function getStatement($customerId, $days = false)
    {
        $conn = $this->getEntityManager('')->getConnection();

        $params = array(
            'customerId' => $customerId,
        );

		$andWhere = '';
		if($days) $andWhere = " AND se.createdAt >= DATE_SUB(CURDATE(),INTERVAL $days DAY)";


        $sql = <<<SQL
SELECT
	se.type,
	se.typeId,
	se.customer,
	se.createdAt,
	se.note,
	se.product,
	se.quantity,
	se.soldValue,
	se.amount
FROM (SELECT
	      'Payment'     type,
	      p.id          typeId,
	      c.id          customer,
	      p.received_at createdAt,
	      p.note        note,
	      ''            product,
	      ''            quantity,
	      ''            soldValue,
	      p.amount      amount
      FROM customers c JOIN payments p
		      ON c.id = p.fk_customer
      UNION
      SELECT
	      'Order'        type,
	      o.id           typeId,
	      c.id           customer,
	      o.created_at   createdAt,
	      o.note         note,
	      pr.description product,
	      oi.quantity    quantity,
	      oi.sold_value  soldValue,
	      oi.subtotal    amount
      FROM customers c
	      JOIN orders o
		      ON c.id = o.fk_customer
	      JOIN order_items oi
		      ON o.id = oi.fk_order
	      JOIN products pr
		      ON pr.id = oi.fk_product) se
WHERE se.customer = :customerId
     {$andWhere}
ORDER BY se.createdAt, se.type ASC;
SQL;

        $st = $conn->executeQuery($sql, $params);

        return $st->fetchAll();
    }

}