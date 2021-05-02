<?php

namespace App\Controller\Admin;

use App\Helper\TwigDefaultParameters;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DashboardController extends AbstractAdminController
{
    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator,
        TwigDefaultParameters $twigDefaultParameters
    ) {
        $this->translator = $translator;
        $this->setPageTitle($this->translator->trans('Admin dashboard'));
        return parent::__construct($twigDefaultParameters);
    }

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
