<?php

namespace App\Manager\User;

use Symfony\Component\Security\Core\User\UserInterface;

class RoleManager
{
    public function hasRole(UserInterface $user, string $role) : bool
    {
        foreach ($user->getRoles() as $currentRole) {
            if ($currentRole == $role) {
                return true;
            }
        }
        return false;
    }
}
