<?php

namespace App\Controller\Fragment;

use App\Entity\User;
use App\Manager\User\RoleManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Navigation extends AbstractController
{
    private RoleManager $roleManager;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        RoleManager $roleManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->roleManager = $roleManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function display() : Response
    {
        return $this->render(
            'fragment/_navigation.html.twig',
            [
                'user_admin' => $this->hasUserRole(User::USER_ROLE_ADMIN),
                'user_authenticated' => $this->hasUserRole(User::USER_ROLE_AUTHENTICATED),
            ]
        );
    }

    protected function hasUserRole(string $role) : bool
    {
        $token = $this->tokenStorage->getToken();
        if (is_null($token)) {
            return false;
        }
        $user = $token->getUser();
        if (is_null($user)) {
            return false;
        }
        return $this->roleManager->hasRole($user, $role);
    }
}
