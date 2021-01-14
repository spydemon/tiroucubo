<?php

namespace App\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractAdminController
{
    /**
     * TODO: replace with PHP 8 annotation when available.
     * @IsGranted("ROLE_ADMIN")
     * @Route("/dashboard", name="back_dashboard")
     */
    public function display() : Response
    {
        return $this->render('back/dashboard.html.twig');
    }
}
