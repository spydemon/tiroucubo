<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{

    protected ?Form $formLogin = null;

    #[Route('/login', name: 'login_display', methods: ['GET'])]
    public function display() : Response
    {
        $formLoginView = $this->getFormLogin()->createView();
        return $this->render('front/login.html.twig', [
            'form_login' => $formLoginView,
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

    #[Route('/login', name: 'login_process', methods: ['POST'])]
    public function process() : Response
    {
        $formLogin = $this->getFormLogin();
        if ($formLogin->isSubmitted() && $formLogin->isValid()) {
            $this->addFlash('error', 'Invalid user or password provided.');
        }
        $response = new RedirectResponse($this->generateUrl('login_display'));
        return $response->send();
    }

    protected function getFormLogin() : Form
    {
        if (is_null($this->formLogin)) {
            $this->formLogin = $this->createFormBuilder()
                ->add('email', EmailType::class)
                ->add('password', PasswordType::class)
                ->getForm();
        }
        return $this->formLogin;
    }
}
