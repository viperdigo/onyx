<?php

namespace Worldplay\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Worldplay\CoreBundle\Entity\UserType;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;


class LoadUserType extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function load(ObjectManager $manager)
    {
        // Type Admin
        $userTypeAdmin = new UserType();
        $userTypeAdmin->setName('administrator');
        $userTypeAdmin->setDescription('Administrador do sistema');
        $this->setReference('administrator', $userTypeAdmin);
        $manager->persist($userTypeAdmin);

        //Type Supervisor
        $userTypeSu = new UserType();
        $userTypeSu->setName('supervisor');
        $userTypeSu->setDescription('Supervisor do sistema');
        $this->setReference('ope',$userTypeSu);
        $manager->persist($userTypeSu);

        //Type Operador
        $userTypeOpe = new UserType();
        $userTypeOpe->setName('operator');
        $userTypeOpe->setDescription('Operador do sistema');
        $this->setReference('ope',$userTypeOpe);
        $manager->persist($userTypeOpe);

        $manager->flush();
    }

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}