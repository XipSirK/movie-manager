<?php

namespace KriSpiX\UserBundle\DataFixtures\ORM;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use KriSpiX\UserBundle\Entity\User;

class LoadUser extends Controller implements FixtureInterface 
{
    public function load(ObjectManager $manager)
    {

        $listNames = array('demo', 'admin');

        foreach ($listNames as $name) {
            $user = new User;
            
            $user->setUsername($name);
            
            $plainPassword = $name;         
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);
            $user->setSalt('');

            if($name == 'admin') {
                $user->setRoles(array('ROLE_ADMIN'));
            } elseif($name == 'demo') {
                $user->setRoles(array('ROLE_USER'));
            }

            $manager->persist($user);
        }
        $manager->flush();
    }
}