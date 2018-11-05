<?php

namespace Worldplay\UserBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Worldplay\CoreBundle\Entity\User;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;


class LoadUser extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function load(ObjectManager $manager)
    {

        $adminType = $this->getReference('administrator');
        $supervisorType = $this->getReference('administrator');
        $operatorType = $this->getReference('administrator');

        // Adiciona Administradores
        $admin = new User();
        $admin->setUsername('rodrigo');
        $admin->setPlainPassword('hseDP09zP');
        $admin->setIsActive(true);
        $admin->setRoles(array('ROLE_ADMIN'));
        $admin->setEmail('viperdigo@gmail.com');
        $admin->setName('Rodrigo Alfieri');
        $admin->setUserType($adminType);
        $manager->persist($admin);

        $admin = new User();
        $admin->setUsername('tiago');
        $admin->setPlainPassword('123456');
        $admin->setIsActive(true);
        $admin->setRoles(array('ROLE_ADMIN'));
        $admin->setEmail('tiagomascanha@gmail.com');
        $admin->setName('Tiago Mascanha');
        $admin->setUserType($adminType);
        $manager->persist($admin);

        $user = new User();
        $user->setUsername('teste');
        $user->setPlainPassword('teste');
        $user->setIsActive(true);
        $user->setRoles(array('ROLE_USER'));
        $user->setEmail('teste@gmail.com');
        $user->setName('Teste (ROLEUSER)');
        $user->setUserType($operatorType);
        $manager->persist($user);

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
        return 2;
    }
}