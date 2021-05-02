<?php

namespace App\PathRenderer;

use App\Entity\Path;
use App\Entity\User;
use App\Repository\ArticleVersionRepository;
use App\Repository\PathRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class Article implements PathRendererInterface
{

    private ArticleVersionRepository $articleVersionRepository;
    private AuthorizationCheckerInterface $authorizationChecker;
    private ContainerInterface $container;
    private PathRepository $pathRepository;
    private RequestStack $requestStack;

    public function __construct(
        ArticleVersionRepository $articleVersionRepository,
        AuthorizationCheckerInterface $authorizationChecker,
        ContainerInterface $container,
        PathRepository $pathRepository,
        RequestStack $requestStack
    ) {
        $this->articleVersionRepository = $articleVersionRepository;
        $this->container = $container;
        $this->pathRepository = $pathRepository;
        $this->requestStack = $requestStack;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function render(Path $path) : Response
    {
        $articles = $this->pathRepository->findActiveArticlesRecursivelyForPath($path);
        $articlesToDisplay = [];
        $forcedVersion = $this->requestStack->getMasterRequest()->query->get('version');
        if ($forcedVersion) {
            $isUserAllowed = $this->authorizationChecker->isGranted(User::USER_ROLE_ADMIN);
            if (!$isUserAllowed) {
                throw new NotFoundHttpException();
            }
        }
        foreach ($articles as $currentArticle) {
            $displayedVersion = $forcedVersion
                ? $this->articleVersionRepository->findOneBy(['slug' => $forcedVersion])
                : $this->articleVersionRepository->findActiveVersionForArticle($currentArticle);
            if (!is_null($displayedVersion)) {
                $url = $this->pathRepository->getUrlForPath($currentArticle->getPath());
                $articlesToDisplay[] = [
                    'article' => $currentArticle,
                    'url' => $url,
                    'version' => $displayedVersion
                ];
            }
        }
        // If none of the articles to display for the given path has an active version, it mean that we actually
        // have nothing to display. We thus return a 404 instead of an empty page.
        if (count($articlesToDisplay) == 0) {
            throw new NotFoundHttpException();
        }
        // TODO: cache result
        $template = $path->getCustomTemplate() ?? 'front/path/default.html.twig';
        $html = $this->container->get('twig')->render($template, [
            'path' => $path,
            'articles' => $articlesToDisplay
        ]);
        $response = new Response();
        $response->setContent($html);
        return $response;
    }
}