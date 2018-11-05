<?php

namespace Worldplay\BackendBundle\Filter\Action;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use Filter\FilterBundle\Service\Filter\Property;
use Filter\FilterBundle\Service\Filter\Action;
use Knp\Bundle\SnappyBundle\Snappy\LoggableGenerator;

class CustomerBalanceExportPdfAction implements Action
{
    private $template;
    /**
     * @var LoggableGenerator
     */
    private $pdf;
    /**
     * @var \Twig_Environment
     */
    private $twig;

    private $id = 'id';
    private $name = 'name';
    private $balance = 'balance';
    private $max = 'payments.receivedAt';
    private $status= 'status';

    /**
     * @param LoggableGenerator $pdf
     * @param \Twig_Environment $twig
     * @param $template
     */
    public function __construct(LoggableGenerator $pdf, \Twig_Environment $twig, $template)
    {
        $this->template = $template;
        $this->pdf = $pdf;
        $this->twig = $twig;
    }

    /**
     * @param QueryBuilder $qb
     * @param callable $alias
     * @param Property $root
     */
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

    /**
     * @param Query $query
     * @throws \Exception
     */
    public function execute(Query $query)
    {
        $rows = $query->getArrayResult();

        $pdf = $this->generatePdf($rows);
        $this->output($pdf);
    }

    /**
     * @param Query $query
     * @return \Doctrine\DBAL\Driver\Statement
     * @throws \Doctrine\DBAL\DBALException
     */
    private function buildStatement(Query $query)
    {

        $sql = $query->getSQL();

        $parameters = $query->getParameters();

        $parser = new \Doctrine\ORM\Query\Parser($query);
        $result = $parser->parse();
        $paramMappings = $result->getParameterMappings();

        $params = array();
        $types = array();

        foreach ($parameters as $parameter) {
            $mappings = $paramMappings[$parameter->getName()];

            foreach ($mappings as $position) {
                $params[$position] = $parameter->getValue();
                $types[$position] = $parameter->getType();
            }
        }

        return $query->getEntityManager()->getConnection()->executeQuery($sql, $params, $types);
    }

    /**
     * @param $stm
     * @return string
     */
    private function generatePdf($rows)
    {
        $html = $this->twig->render($this->template, array('rows' => $rows));

        return $this->pdf->getOutputFromHtml($html);

    }

    /**
     * @param $output
     */
    private function output($output)
    {
        header('Content-type: application/pdf');
//        header('Content-disposition: '.sprintf('attachment; filename="%s"', basename($filename)));
        header('Content-length: '.strlen($output));
        echo $output;
    }
}
