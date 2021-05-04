<?php

namespace App\Manager\Path;

use App\Entity\Path;
use App\PathRenderer\Article as ArticleRenderer;
use App\PathRenderer\Media\Image\Webp as WebpRenderer;
use Symfony\Component\HttpFoundation\Response;

class PathRendererManager
{
    private ArticleRenderer $articleRenderer;
    private WebpRenderer $webpRenderer;

    public function __construct(
        ArticleRenderer $articleRenderer,
        WebpRenderer $webpRenderer
    ) {
        $this->articleRenderer = $articleRenderer;
        $this->webpRenderer = $webpRenderer;
    }

    /**
     * TODO: find the renderer dynamically, by example thanks to an intermediate table.
     * @param Path $path
     * @return mixed
     */
    public function render(Path $path) : Response
    {
        $renderer = $path->getType() == Path::TYPE_MEDIA ? $this->webpRenderer : $this->articleRenderer;
        return $renderer->render($path);
    }
}