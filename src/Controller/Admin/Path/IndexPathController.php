<?php

namespace App\Controller\Admin\Path;

use App\Controller\Admin\AbstractAdminController;
use App\Repository\PathRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexPathController extends AbstractAdminController
{
    private PathRepository $pathRepository;

    public function __construct(
        PathRepository $pathRepository
    ) {
        $this->pathRepository = $pathRepository;
    }

    /**
     * @Route("/path", name="admin_path_index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function display() : Response
    {
        $paths = $this->pathRepository->findAllSortedByPath();
        return $this->render('back/path/index.html.twig', ['paths' => $paths]);
    }
}