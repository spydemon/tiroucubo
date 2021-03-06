<?php

namespace App\Controller\Front;

use App\Controller\AbstractBaseController;
use LogicException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * The content of this class was mainly generated by the `make:auth` command.
 * It handle everything that deal with user authentication.
 */
class SecurityController extends AbstractBaseController
{
    /**
     * @Route("/login", name="front_security_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('default');
        }
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('front/login.html.twig', [
            'last_username' => $lastUsername,
            'page' => [
                'title' => 'Login'
            ]
        ]);
    }

    /**
     * @Route("/logout", name="front_security_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
