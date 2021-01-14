<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Manager\User\PasswordManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    private PasswordManager $passwordManager;

    private array $userData = [
        [
            'email' => 'admin@tiroucubo.local',
            'password' => 'pa$$word',
            'roles' => [ User::USER_ROLE_ADMIN ]
        ],
        [
            'email' => 'user@tiroucubo.local',
            'password' => 'pa$$word',
            'roles' => []
        ]
    ];

    public function __construct(
        PasswordManager $passwordManager
    ) {
        $this->passwordManager = $passwordManager;
    }

    public function load(ObjectManager $manager)
    {
        foreach ($this->userData as $currentUser) {
            $user = new User();
            $user
                ->setEmail($currentUser['email'])
                ->setRoles($currentUser['roles']);
            $this->passwordManager->setNewPassword($user, $currentUser['password']);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
