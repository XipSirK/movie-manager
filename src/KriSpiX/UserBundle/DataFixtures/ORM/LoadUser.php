<?php

namespace KriSpiX\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KriSpiX\UserBundle\Entity\User;

class LoadUser implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {

        $listNames = array('anthony', 'admin');

        foreach ($listNames as $name) {
            $user = new User;

            // Le nom d'utilisateur et le mot de passe sont identiques
            $user->setUsername($name);
            $user->setPassword($name);

            // On ne se sert pas du sel pour l'instant
            $user->setSalt('');
            // On définit uniquement le role ROLE_USER qui est le role de base
            if($name == 'anthony') {
                $user->setRoles(array('ROLE_ADMIN'));
            } elseif($name == 'admin') {
                $user->setRoles(array('ROLE_SUPER_ADMIN'));
            }

            $manager->persist($user);
        }
        $manager->flush();
    }
}