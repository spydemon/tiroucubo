<?php

namespace App\PathRenderer\Media\Image;

use App\Entity\Path;
use App\PathRenderer\PathRendererInterface;
use App\Repository\MediaRepository;
use App\Repository\PathMediaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Webp implements PathRendererInterface
{
    private MediaRepository $mediaRepository;
    private PathMediaRepository $pathMediaRepository;

    public function __construct(
        MediaRepository $mediaRepository,
        PathMediaRepository $pathMediaRepository
    ) {
        $this->pathMediaRepository = $pathMediaRepository;
        $this->mediaRepository = $mediaRepository;
    }

    public function render(Path $path) : Response
    {
        $media = $this->pathMediaRepository->findMediaByPath($path);
        if (is_null($media)) {
            throw new NotFoundHttpException();
        }
        $response = new Response();
        $response->setContent(stream_get_contents($media->getContent()));
        $response->headers->add(['content-type' => 'image/webp']);
        return $response;
    }
}