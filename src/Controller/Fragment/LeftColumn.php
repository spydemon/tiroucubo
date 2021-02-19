<?php

namespace App\Controller\Fragment;

use App\Entity\Path;
use App\Manager\Cache\NameManager;
use App\Repository\PathRepository;
use App\Repository\PathMapRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class LeftColumn extends AbstractController
{
    private CacheInterface $entityPathCache;
    private NameManager $nameManager;
    private PathRepository $pathRepository;
    private PathMapRepository $pathMapRepository;
    private RequestStack $requestStack;
    private UrlGeneratorInterface $urlGenerator;

    private string $rootUrl;

    public function __construct(
        CacheInterface $entityPathCache,
        NameManager $nameManager,
        PathRepository $pathRepository,
        PathMapRepository $pathMapRepository,
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->entityPathCache = $entityPathCache;
        $this->nameManager = $nameManager;
        $this->pathRepository = $pathRepository;
        $this->pathMapRepository = $pathMapRepository;
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
        $this->rootUrl = $this->urlGenerator->generate('default', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function display() : Response
    {
        return new Response($this->getMenu());
    }

    protected function getMenu() : string
    {
        $url = $this->requestStack->getMasterRequest()->getRequestUri();
        $pathMap = $this->pathMapRepository->getPathMapForUrl($url);
        if (is_null($pathMap)) {
            return '';
        }
        $path = $pathMap->getPath();
        return $this->entityPathCache->get(
            $this->nameManager->encodeCacheKey('get_menu_' . $path->getId()),
            function (ItemInterface $item) use ($path) {
                return $this->generateMenu($path, false);
            }
        );
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
        // We don't display the menu item if neither it nor its children contain any article with an activated
        // version if the path has the "dynamic" type.
        if ($parent->getType() == Path::TYPE_DYNAMIC && !$this->pathRepository->countActiveContent($parent)) {
            return $html;
        }
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
