<?php

namespace App\Controller\Front;

use App\Controller\AbstractBaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractBaseController
{
    #[Route('/', name: 'front_home')]
    public function display() : Response
    {
        return $this->render('front/home.html.twig');
    }
}
