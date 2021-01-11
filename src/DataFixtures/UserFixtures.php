<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Manager\User\PasswordManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    private PasswordManager $passwordManager;

    public function __construct(
        PasswordManager $passwordManager
    ) {
        $this->passwordManager = $passwordManager;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user
            ->setEmail('admin@tiroucubo.local')
            ->setRoles(['ROLE_ADMIN']);
        $this->passwordManager->setNewPassword($user, 'pa$$word');
        $manager->persist($user);
        $manager->flush();
    }
}
