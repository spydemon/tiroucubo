<?php

namespace App\Twig\Extension;

use App\Repository\PathRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TiroucuboPathUrlExtension extends AbstractExtension
{
    private PathRepository $pathRepository;
    private RequestStack $requestStack;

    public function __construct(
        PathRepository $pathRepository,
        RequestStack $requestStack
    ) {
        $this->pathRepository = $pathRepository;
        $this->requestStack = $requestStack;
    }

    public function getFunctions() : array
    {
        return [
            new TwigFunction('tiroucuboPathUrl', [$this, 'tiroucuboPathUrl'])
        ];
    }

    public function tiroucuboPathUrl(string $pathString) : string
    {
        $locale = $this->requestStack->getMasterRequest()->getLocale();
        $path = $this->pathRepository->findByPath("$locale/$pathString");
        if (!$path) {
            return '';
        }
        return $this->pathRepository->getUrlForPath($path);
    }
}