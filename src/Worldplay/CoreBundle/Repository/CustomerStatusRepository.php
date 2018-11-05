<?php

namespace Worldplay\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CustomerStatusRepository extends EntityRepository
{
    public function getStatus( $customer )
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select(array('csl.status, MIN(csl.createdAt) as createdAt'))
            ->from('CoreBundle:CustomerStatusLog', 'csl')
            ->innerJoin('csl.customer', 'c')
            ->where("c.id=" . $customer)
            ->addGroupBy('csl.status')
        ;

//        $qb->orderBy('csl.createdAt', 'DESC');
        $results = $qb->getQuery()->getArrayResult();

        $status = array(
            'activated'  => '',
            'blocked'    => '',
        );

        if( count($results) > 0 ) {
            foreach( $results as $result ) {
                $status[$result['status']] = $result['createdAt'];
            }
        }

        return $status;
    }

}