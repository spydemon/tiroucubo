<?php

namespace App\PathRenderer\Media\Image;

use App\Entity\Path;
use App\PathRenderer\PathRendererInterface;
use App\Repository\PathMediaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Webp implements PathRendererInterface
{
    private PathMediaRepository $pathMediaRepository;

    public function __construct(
        PathMediaRepository $pathMediaRepository
    ) {
        $this->pathMediaRepository = $pathMediaRepository;
    }

    public function render(Path $path) : Response
    {
        //TODO: cache the image to render.
        //TODO: add a way to set the media size depending of the device.
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