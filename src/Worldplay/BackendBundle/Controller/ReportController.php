<?php

namespace Worldplay\BackendBundle\Controller;

use Filter\FilterBundle\Action\CsvExportAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;


/**
 * @Route("/report")
 */
class ReportController extends Controller
{

    /**
     * @Route("/customer", name="report_customer")
     * @Method("GET")
     * @Template()
     */
    public function customerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $customers = $em->getRepository('CoreBundle:Customer')->findAll();
        $customerId = $request->get('customerId');
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');

        $data = null;
        if ($customerId) {
            $data = $em->getRepository('CoreBundle:Customer')->reportAccount($customerId, $dateStart, $dateEnd);
        }

        return array(
            'customers' => $customers,
            'data' => $data,
            'count' => count($data),
        );
    }

    /**
     * @Route("/customer/export", name="report_customer_export_pdf")
     * @Method("GET")
     */
    public function exportCustomerPdfAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $customerId = $request->get('customerId');
        $dateStart = $request->get('dateStart');
        $dateEnd = $request->get('dateEnd');

        $data = null;
        if ($customerId) {
            $data = $em->getRepository('CoreBundle:Customer')->reportAccount($customerId, $dateStart, $dateEnd);
        }

        $html = $this->renderView(
            'BackendBundle:Report:exportCustomerPdf.html.twig',
            array(
                'data' => $data,
            )
        );

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            array(
                'Content-Type' => 'application/pdf',
//                'Content-Disposition' => 'attachment; filename="pdf_'.rand(1, 999999).'.pdf"',
            )
        );

    }

    /**
     * @Route("/maintenance", name="report_maintenance")
     * @Method("GET")
     * @Template()
     */
    public function maintenanceAction()
    {
        $filter = $this->get('filter')->createFilterBuilder('CoreBundle:MaintenanceProduct')
            ->addField('id')
            ->addField('customer.name')
            ->addField('statusLogs.status')
            ->addField('statusLogs.createdAt')
            ->addAction(
                'customer_list_csv_action'
                ,
                new CsvExportAction(
                    array(
                        'id',
                        'name',
                    )
                )
            )
            ->build();

        return array(
            'filter' => $filter,
            'result' => $filter->getResult(),
        );
    }

}
