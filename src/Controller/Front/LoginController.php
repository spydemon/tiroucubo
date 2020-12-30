<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{

    #[Route('/login', name: 'login_display')]
    public function display() : Response
    {
        return $this->render('front/login.html.twig', [
            //TODO: fetch those parameters from somewhere else.
            'page' => [
                'author' => 'Administrator',
                'lang' => 'en',
                'title' => 'Login'
            ],
            'website' => [
                'title' => 'Spyzone'
            ]
        ]);
    }
}
