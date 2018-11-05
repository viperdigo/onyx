<?php

namespace Worldplay\BackendBundle\Controller;

use Filter\FilterBundle\Action\CsvExportAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Worldplay\BackendBundle\Form\ProductCompoundType;
use Worldplay\BackendBundle\Form\ProductType;
use Worldplay\CoreBundle\Entity\Product;
use Worldplay\CoreBundle\Entity\ProductComponent;

/**
 * @Route("/product")
 */
class ProductController extends Controller
{

    /**
     * @Route("/", name="product")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $filter = $this->get('filter')->createFilterBuilder('CoreBundle:Product')
            ->addField('id',array('length'=>1))
            ->addField('description',array('length'=>3))
            ->addField('type',array('length'=>3))
            ->addField('createdAt')
            ->addOrder('updatedAt','DESC')
            ->addPagination(10)
            ->addCache(0)
            ->build();

        return array(
            'filter' => $filter,
            'result' => $filter->getResult(),
        );
    }

    /**
     * @Route("/simple/", name="product_simple_create")
     * @Method("POST")
     * @Template("BackendBundle:Product:simple_new.html.twig")
     */
    public function simpleCreateAction(Request $request)
    {
        $entity = new Product();
        $form = $this->simpleCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setType(Product::TYPE_SIMPLE);

            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf('Produto %s criado.', $entity->getDescription())
            );

            return $this->redirect($this->generateUrl('product'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @param Product $entity
     * @return \Symfony\Component\Form\Form
     */
    private function simpleCreateForm(Product $entity)
    {
        $form = $this->createForm(
            new ProductType(),
            $entity,
            array(
                'action' => $this->generateUrl('product_simple_create'),
                'method' => 'POST',
                'attr' => array(
                    'class' => 'form-horizontal',
                ),
            )
        );

        return $form;
    }

    /**
     * @Route("/simple/new", name="product_simple_new")
     * @Method("GET")
     * @Template("BackendBundle:Product:simple_new.html.twig")
     */
    public function simpleNewAction()
    {
        $entity = new Product();
        $form = $this->simpleCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/simple/{id}/edit", name="product_simple_edit")
     * @Method("GET")
     * @Template("BackendBundle:Product:simple_edit.html.twig")
     */
    public function simpleEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $editForm = $this->simpleEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @param Product $entity
     * @return \Symfony\Component\Form\Form
     */
    private function simpleEditForm(Product $entity)
    {
        $form = $this->createForm(
            new ProductType(),
            $entity,
            array(
                'action' => $this->generateUrl('product_simple_update', array('id' => $entity->getId())),
                'method' => 'PUT',
                'attr' => array(
                    'class' => 'form-horizontal',
                ),
            )
        );

        return $form;
    }

    /**
     * @Route("/simple/{id}", name="product_simple_update")
     * @Method("PUT")
     * @Template("BackendBundle:Product:simple_edit.html.twig")
     */
    public function simpleUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Product')->findOneBy(array('type' => Product::TYPE_SIMPLE, 'id' => $id));

        if (!$entity instanceof Product) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $editForm = $this->simpleEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setType(Product::TYPE_SIMPLE);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf('Produto %s alterado.', $entity->getDescription())
            );

            return $this->redirect($this->generateUrl('product'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @Route("/cabinet/", name="product_cabinet_create")
     * @Method("POST")
     * @Template("BackendBundle:Product:cabinet_new.html.twig")
     */
    public function cabinetCreateAction(Request $request)
    {
        $entity = new Product();
        $form = $this->cabinetCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setType(Product::TYPE_CABINET);

            $em->persist($entity);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf('Produto %s criado.', $entity->getDescription())
            );

            return $this->redirect($this->generateUrl('product'));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @param Product $entity
     * @return \Symfony\Component\Form\Form
     */
    private function cabinetCreateForm(Product $entity)
    {
        $form = $this->createForm(
            new ProductType(),
            $entity,
            array(
                'action' => $this->generateUrl('product_cabinet_create'),
                'method' => 'POST',
                'attr' => array(
                    'class' => 'form-horizontal',
                ),
            )
        );

        return $form;
    }

    /**
     * @Route("/cabinet/new", name="product_cabinet_new")
     * @Method("GET")
     * @Template("BackendBundle:Product:cabinet_new.html.twig")
     */
    public function cabinetNewAction()
    {
        $entity = new Product();
        $form = $this->cabinetCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/cabinet/{id}/edit", name="product_cabinet_edit")
     * @Method("GET")
     * @Template("BackendBundle:Product:cabinet_edit.html.twig")
     */
    public function cabinetEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Product')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $editForm = $this->cabinetEditForm($entity);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @param Product $entity
     * @return \Symfony\Component\Form\Form
     */
    private function cabinetEditForm(Product $entity)
    {
        $form = $this->createForm(
            new ProductType(),
            $entity,
            array(
                'action' => $this->generateUrl('product_cabinet_update', array('id' => $entity->getId())),
                'method' => 'PUT',
                'attr' => array(
                    'class' => 'form-horizontal',
                ),
            )
        );

        return $form;
    }

    /**
     * @Route("/cabinet/{id}", name="product_cabinet_update")
     * @Method("PUT")
     * @Template("BackendBundle:Product:cabinet_edit.html.twig")
     */
    public function cabinetUpdateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CoreBundle:Product')->findOneBy(array('type' => Product::TYPE_CABINET, 'id' => $id));

        if (!$entity instanceof Product) {
            throw $this->createNotFoundException('Unable to find Product entity.');
        }

        $editForm = $this->cabinetEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $entity->setType(Product::TYPE_CABINET);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf('Produto %s alterado.', $entity->getDescription())
            );

            return $this->redirect($this->generateUrl('product'));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
        );
    }

    /**
     * @Route("/compound/new", name="product_compound_new")
     * @Method("GET")
     * @Template("BackendBundle:Product:compound_new.html.twig")
     */
    public function compoundNewAction()
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->getRepository('CoreBundle:Product')->findBy(
            array(
                'type' => array(
                    Product::TYPE_CABINET,
                    Product::TYPE_SIMPLE,
                ),
            )
        );

        $productsCompounds = $em->getRepository('CoreBundle:Product')->findBy(array('type' => Product::TYPE_COMPOUND));

        return array(
            'productsCompounds' => $productsCompounds,
            'products' => $products,
        );
    }

    /**
     * @Route("/compound/edit/{id}", name="product_compound_edit")
     * @Method("GET")
     * @Template("BackendBundle:Product:compound_edit.html.twig")
     */
    public function compoundEditAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('CoreBundle:Product')->find($id);
        $products = $em->getRepository('CoreBundle:Product')->findBy(
            array(
                'type' => array(
                    Product::TYPE_SIMPLE,
                    Product::TYPE_CABINET,
                ),
            )
        );

        return array(
            'product' => $product,
            'products' => $products,
        );
    }

    /**
     * @Route("/compound/list/{id}", name="product_components")
     * @Method("GET")
     * @Template("BackendBundle:Product:modal_components.html.twig")
     */
    public function findComponents($id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('CoreBundle:Product')->find($id);

        $components = $em->getRepository('CoreBundle:ProductComponent')->findBy(array('product' => $id));

        return array(
            'product' => $product,
            'components' => $components,
        );
    }

    /**
     * @Route("/compound/new", name="product_compound_create")
     * @Method("POST")
     */
    public function postCreateCompoundAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $productRef = $request->get('productRef');
        $description = $request->get('description');
        $costValue = $request->get('costValue');
        $saleValue = $request->get('saleValue');
        $components = $request->get('components');
        $quantities = $request->get('quantity');

        try {

            $productsRef = $em->getRepository('CoreBundle:ProductComponent')->findBy(array('product' => $productRef));

            $product = new Product();
            $product->setDescription($description);
            $product->setCostValue($costValue);
            $product->setSaleValue($saleValue);
            $product->setType(Product::TYPE_COMPOUND);
            $em->persist($product);

            foreach ($productsRef as $productRef) {
                if (!$productRef instanceof ProductComponent) {
                    continue;
                }

                $productComponent = new ProductComponent();
                $productComponent->setProduct($product);
                $productComponent->setComponent($productRef->getComponent());
                $productComponent->setQuantity($productRef->getQuantity());
                $em->persist($productComponent);
            }

            $index = 0;
            foreach ($components as $component) {
                $newComponent = $em->getRepository('CoreBundle:Product')->find($component);

                if (!$newComponent instanceof Product) {
                    continue;
                }

                $productComponent = new ProductComponent();
                $productComponent->setProduct($product);
                $productComponent->setComponent($newComponent);
                $productComponent->setQuantity($quantities[$index]);
                $em->persist($productComponent);

                $index++;
            }

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf('Produto %s criado.', $product->getDescription())
            );
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf('Code: %s Message: %s', $e->getCode(), $e->getMessage())
            );
        }

        return $this->redirect($this->generateUrl('product'));
    }

    /**
     * @Route("/compound/new", name="product_compound_update")
     * @Method("POST")
     */
    public function postUpdateCompoundAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $product = $request->get('product');
        $description = $request->get('description');
        $costValue = $request->get('costValue');
        $saleValue = $request->get('saleValue');
        $components = $request->get('components');
        $quantities = $request->get('quantity');

        try {

            $productComponent = $em->getRepository('CoreBundle:ProductComponent')->findBy(array('product' => $product));
            $em->remove($productComponent);
            $em->persist($productComponent);

            $product = $em->getRepository('CoreBundle:Product')->find($product);
            $product->setDescription($description);
            $product->setCostValue($costValue);
            $product->setSaleValue($saleValue);
            $em->persist($product);

            $index = 0;
            foreach ($components as $component) {
                $newComponent = $em->getRepository('CoreBundle:Product')->find($component);

                if (!$newComponent instanceof Product) {
                    continue;
                }

                $productComponent = new ProductComponent();
                $productComponent->setProduct($product);
                $productComponent->setComponent($newComponent);
                $productComponent->setQuantity($quantities[$index]);
                $em->persist($productComponent);

                $index++;
            }

            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf('Produto %s alterado.', $product->getDescription())
            );
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf('Code: %s Message: %s', $e->getCode(), $e->getMessage())
            );
        }

        return $this->redirect($this->generateUrl('product'));
    }

    /**
     * @Route("/find_sale", name="product_find_sale")
     * @Method("POST")
     */
    public function findSaleValue(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $product = $em->getRepository('CoreBundle:Product')->find($request->get('id'));

        return new Response($product->getSaleValue());
    }


}
