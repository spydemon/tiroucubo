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

    public function tiroucuboPathUrl(
        string $pathString,
        bool $addCurrentLocalePrefix = true,
        array $getParams = []
    ) : string
    {
        if ($addCurrentLocalePrefix) {
            $locale = $this->requestStack->getMasterRequest()->getLocale();
            $pathString = "$locale/$pathString";
        }
        $path = $this->pathRepository->findByPath($pathString);
        if (!$path) {
            return '';
        }
        $url = $this->pathRepository->getUrlForPath($path);
        $firstParam = true;
        foreach ($getParams as $key => $value) {
            $delimiter = $firstParam ? '?' : '&';
            $url .= "{$delimiter}{$key}={$value}";
            $firstParam = false;
        }
        return $url;
    }
}