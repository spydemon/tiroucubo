<?php

namespace App\PathRenderer;

use App\Entity\Path;
use Symfony\Component\HttpFoundation\Response;

interface PathRendererInterface
{
    public function render(Path $path) : Response;
}