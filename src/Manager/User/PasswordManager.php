<?php

namespace App\Manager\User;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordManager
{
    public function __construct(
        private UserPasswordEncoderInterface $passwordManager
    ) {}

    public function setNewPassword(User $user, string $clearPassword) : void
    {
        $hashedPassword = $this->passwordManager->encodePassword($user, $clearPassword);
        $user->setPassword($hashedPassword);
    }

    public function comparePassword(User $user, string $clearTry) : bool
    {
        return $this->passwordManager->isPasswordValid($user, $clearTry);
    }
}