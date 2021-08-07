<?php

namespace App\Form\AdminMediaEdit;

use App\Entity\Media;
use App\Manager\Path\PathToUrl;
use App\Repository\PathMediaRepository;

/**
 * The aim of this class is to set default values in a FormData.
 */
class FormDataFeeder
{

    private PathMediaRepository $pathMediaRepository;
    private PathToUrl $pathToUrl;

    public function __construct(PathMediaRepository $pathMediaRepository, PathToUrl $pathToUrl)
    {
        $this->pathMediaRepository = $pathMediaRepository;
        $this->pathToUrl = $pathToUrl;
    }

    public function feed(FormData $formData, Media $media) : void
    {
        $allMediaPath = $this->pathMediaRepository->findPathsByMedia($media);
        $paths = [];
        $url = null;
        foreach ($allMediaPath as $mediaPath) {
            $paths[] = $mediaPath->getPath()->__toString();
            if (is_null($url)) {
                $url = $this->pathToUrl->getUrlForPath($mediaPath->getPath());
            }
        }
        $formData->setPath($paths);
        if ($url) {
            $formData->setUrl($url);
        }
    }
}