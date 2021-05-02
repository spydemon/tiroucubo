<?php

namespace App\Manager\Path;

use App\Entity\Path;
use App\PathRenderer\Article as ArticleRenderer;
use Symfony\Component\HttpFoundation\Response;

class PathRendererManager
{
    private ArticleRenderer $articleRenderer;

    public function __construct(
        ArticleRenderer $articleRenderer
    ) {
        $this->articleRenderer = $articleRenderer;
    }

    /**
     * TODO: find the renderer dynamically, by example thanks to an intermediate table.
     * @param Path $path
     * @return mixed
     */
    public function render(Path $path) : Response
    {
        // TODO: call \App\Controller\AbstractBaseController::addDefaultParameters
        return $this->articleRenderer->render($path);
    }
}