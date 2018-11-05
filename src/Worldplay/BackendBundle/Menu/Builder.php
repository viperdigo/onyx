<?php

namespace Worldplay\BackendBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Security;

class Builder extends ContainerAware
{
    public function sideBar(FactoryInterface $factory, array $options)
    {

        $security = $this->container->get('security.authorization_checker');


        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'page-sidebar-menu page-sidebar-menu-hover-submenu1');
        $menu->setChildrenAttribute('data-keep-expanded', 'false');
        $menu->setChildrenAttribute('data-auto-scroll', 'true');
        $menu->setChildrenAttribute('data-slide-speed', '200');

        if ($security->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Dashboard', array('route' => 'home_dashboard'))->setAttribute('icon', 'icon-home');
        }
        $menu->addChild('Orders')
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', 'icon-book-open');
        $menu['Orders']->addChild('New', array('route' => 'order_new'))->setAttribute('icon', 'fa fa-plus');
        $menu['Orders']->addChild('List', array('route' => 'order'))->setAttribute('icon', 'fa fa-search');

        if ($security->isGranted('ROLE_ADMIN')) {
            $menu->addChild('Customers')
                ->setAttribute('dropdown', true)
                ->setAttribute('icon', 'fa fa-group');
            $menu['Customers']->addChild('New', array('route' => 'customer_new'))->setAttribute(
                'icon',
                'fa fa-plus'
            );
            $menu['Customers']->addChild('List', array('route' => 'customer'))->setAttribute(
                'icon',
                'fa fa-search'
            );
        }
        $menu->addChild('Payments')
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', 'fa fa-money');
        $menu['Payments']->addChild('New', array('route' => 'payment_new'))->setAttribute(
            'icon',
            'fa fa-plus'
        );
        $menu['Payments']->addChild('List', array('route' => 'payment'))->setAttribute(
            'icon',
            'fa fa-search'
        );

        $menu->addChild('Products')
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', 'icon-picture');
        $menu['Products']->addChild('Simple', array('route' => 'product_simple_new'))->setAttribute(
            'icon',
            'fa fa-plus'
        );
        $menu['Products']->addChild('Compound', array('route' => 'product_compound_new'))->setAttribute(
            'icon',
            'fa fa-plus'
        );
        $menu['Products']->addChild('Cabinet', array('route' => 'product_cabinet_new'))->setAttribute(
            'icon',
            'fa fa-plus'
        );
        $menu['Products']->addChild('List', array('route' => 'product'))->setAttribute('icon', 'fa fa-search');

        $menu->addChild('Maintenance Product')
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', ' icon-wrench');
        $menu['Maintenance Product']->addChild('New', array('route' => 'maintenance_new'))->setAttribute(
            'icon',
            'fa fa-plus'
        );
        $menu['Maintenance Product']->addChild('List', array('route' => 'maintenance'))->setAttribute(
            'icon',
            'fa fa-search'
        );

//        $menu->addChild('Reports')
//            ->setAttribute('dropdown', true)
//            ->setAttribute('icon', 'fa fa-file-text');
//        $menu['Reports']->addChild('Customer', array('route' => 'report_customer'))->setAttribute('icon', 'fa fa-group');
//        $menu['Reports']->addChild('Balance', array('route' => 'user'))->setAttribute('icon', 'icon-graph');
//        $menu['Reports']->addChild('Storage', array('route' => 'user'))->setAttribute('icon', 'fa fa-archive');
//        $menu['Reports']->addChild('Maintenance', array('route' => 'report_maintenance'))->setAttribute(
//            'icon',
//            'icon-social-dropbox'
//        );

//        $menu->addChild('Suppliers')
//            ->setAttribute('dropdown', true)
//            ->setAttribute('icon', 'fa fa-building');
//        $menu['Suppliers']->addChild('List', array('route' => 'supplier'))->setAttribute('icon', 'fa fa-search');

        $menu->addChild('Users')
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', 'icon-users');
        $menu['Users']->addChild('Type', array('route' => 'user_type'))->setAttribute('icon', 'fa fa-search');
        $menu['Users']->addChild('List', array('route' => 'user'))->setAttribute('icon', 'fa fa-search');

        return $menu;
    }

    public function userMenu(FactoryInterface $factory, array $options)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav user_menu pull-right');

        $username = $this->container->get('security.context')->getToken()->getUser()->getUsername();

        $menu->addChild('User', array('label' => $username))
            ->setAttribute('divider_prepend', true)
            ->setAttribute('dropdown', true)
            ->setAttribute('icon', 'icon-user');

        $menu['User']->addChild('Sair', array('route' => 'logout'))
            ->setAttribute('icon', '');

        return $menu;
    }
}