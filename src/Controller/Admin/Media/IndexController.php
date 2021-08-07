<?php

namespace App\Controller\Admin\Media;

use App\Controller\Admin\AbstractAdminController;
use App\Helper\TwigDefaultParameters;
use App\Manager\Path\PathToUrl;
use App\Repository\MediaRepository;
use App\Repository\PathMediaRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractAdminController
{

    private MediaRepository $mediaRepository;
    private PathMediaRepository $pathMediaRepository;
    private PathToUrl $pathToUrl;

    public function __construct(
        MediaRepository $mediaRepository,
        PathMediaRepository $pathMediaRepository,
        PathToUrl $pathToUrl,
        TwigDefaultParameters $twigDefaultParameters
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->pathMediaRepository = $pathMediaRepository;
        $this->pathToUrl = $pathToUrl;
        parent::__construct($twigDefaultParameters);
    }

    /**
     * @Route("/media", name="admin_media_index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function display() : Response
    {
        return $this->render('back/media/index.html.twig', ['medias' => $this->getAllMedias()]);
    }

    protected function getAllMedias() : array
    {
        $medias = $this->mediaRepository->findAll();
        $mediasAsArray = [];
        foreach ($medias as $currentMedia) {
            $allPaths = $this->pathMediaRepository->findPathsbyMedia($currentMedia);
            $mediasAsArray[] = [
                'media_id' => $currentMedia->getId(),
                'media_url' => $this->pathToUrl->getUrlForPath($allPaths[0]->getPath()),
                'all_paths' => $allPaths
            ];
        }
        return $mediasAsArray;
    }
}