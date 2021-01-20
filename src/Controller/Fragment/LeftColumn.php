<?php

namespace App\Controller\Fragment;

use App\Entity\Path;
use App\Manager\Cache\NameManager;
use App\Repository\PathRepository;
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
    private RequestStack $requestStack;
    private UrlGeneratorInterface $urlGenerator;

    private string $rootUrl;

    public function __construct(
        CacheInterface $entityPathCache,
        NameManager $nameManager,
        PathRepository $pathRepository,
        RequestStack $requestStack,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->entityPathCache = $entityPathCache;
        $this->nameManager = $nameManager;
        $this->pathRepository = $pathRepository;
        $this->requestStack = $requestStack;
        $this->urlGenerator = $urlGenerator;
        $this->rootUrl = $this->urlGenerator->generate('default', [], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    public function display() : Response
    {
        // The masterRequest returns the original one done by the user, and not the child one that represent the fragment that is loaded in the generation of the master one.
        $masterRequest = $this->requestStack->getMasterRequest();
        return new Response($this->getMenu());
    }

    protected function getMenu() : string
    {
        // TODO : load the path dynamically depending of the root URL.
        $pathString = 'en/magento';
        return $this->entityPathCache->get(
            $this->nameManager->encodeCacheKey('get_menu_' . $pathString),
            function (ItemInterface $item) use ($pathString) {
                $path = $this->pathRepository->findByPath($pathString);
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
