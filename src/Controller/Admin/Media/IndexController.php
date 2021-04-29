<?php

namespace App\Controller\Admin\Media;

use App\Controller\Admin\AbstractAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractAdminController
{

    /**
     * @Route("/media", name="admin_media_index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function display() : Response
    {
        return $this->render('back/media/index.html.twig');
    }
}