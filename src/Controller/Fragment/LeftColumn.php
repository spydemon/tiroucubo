<?php

namespace App\Controller\Fragment;

use App\Entity\Path;
use App\Repository\PathRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LeftColumn extends AbstractController
{
    private PathRepository $pathRepository;
    private RequestStack $requestStack;
    private UrlGeneratorInterface $urlGenerator;

    private string $rootUrl;

    public function __construct(
        PathRepository $pathRepository,
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->pathRepository = $pathRepository;
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
        $this->rootUrl = $this->urlGenerator->generate('front_home', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function display() : Response
    {
        // The masterRequest returns the original one done by the user, and not the child one that represent the fragment that is loaded in the generation of the master one.
        $masterRequest = $this->requestStack->getMasterRequest();
        // TODO : load the path dynamically depending of the root URL.
        $root = $this->pathRepository->findByPath('en/magento');
        $menu = $this->generateMenu($root, false);
        return new Response($menu);
    }

    /**
     * TODO: highlight currently selected entry.
     * @param Path $parent
     * @param bool $include
     * @return string
     */
    protected function generateMenu(Path $parent, bool $include) : string
    {
        $html = '';
        if ($include) {
            $html .= '<ul>';
            $html .= "<li><a href='{$this->rootUrl}{$parent}'>{$parent->getTitle()}</a></li>";
        }
        foreach ($parent->getChild() as $currentChild) {
            $html .= $this->generateMenu($currentChild, true);
        }
        if ($include) {
            $html .= '</ul>';
        }
        return $html;
    }
}
