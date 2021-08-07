<?php

namespace App\Manager\Path;

use App\Entity\Path;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PathToUrl
{
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Will generate a URL that will reach the corresponding path.
     *
     * @param Path $path
     * @return string
     */
    public function getUrlForPath(Path $path) : string
    {
        return $this->urlGenerator->generate('default', [], UrlGeneratorInterface::ABSOLUTE_URL)
            . $path;
    }
}