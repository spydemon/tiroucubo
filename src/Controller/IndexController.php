<?php

namespace App\Controller;

use App\Repository\ArticleVersionRepository;
use App\Repository\PathRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractBaseController
{
    private ArticleVersionRepository $articleVersionRepository;
    private PathRepository $pathRepository;
    private RequestStack $requestStack;
    private ?string $requestUri;

    public function __construct(
        ArticleVersionRepository $articleVersionRepository,
        PathRepository $pathRepository,
        RequestStack $requestStack
    ) {
        $this->articleVersionRepository = $articleVersionRepository;
        $this->pathRepository = $pathRepository;
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/{path}", name="default", requirements={"path"=".*"})
     */
    public function display(string $path = null) : Response
    {
        $this->requestUri = $path;
        $this->setLocale();
        if ($this->isRootUrlAsked()) {
            //TODO: handle redirection to preferred homepage.
        } elseif ($pathObject = $this->pathRepository->findByPath($this->requestUri)) {
            $articles = $pathObject->getArticles();
            $articlesToDisplay = [];
            foreach ($articles as $currentArticle) {
                $activeVersion = $this->articleVersionRepository->findActiveVersionForArticle($currentArticle);
                if (!is_null($activeVersion)) {
                    $articlesToDisplay[] = ['article' => $currentArticle, 'version' => $activeVersion];
                }
            }
            // If none of the articles to display for the given path has an active version, it mean that we actually
            // have nothing to display. We thus return a 404 instead of an empty page.
            if (count($articlesToDisplay) == 0) {
                throw new NotFoundHttpException();
            }
            // TODO: cache result
            return $this->render('front/index/articles.html.twig', [
                'path' => $pathObject,
                'articles' => $articlesToDisplay
            ]);
        } else {
            throw new NotFoundHttpException();
        }
        return $this->render('front/path.twig.html');
    }

    protected function isRootUrlAsked() : bool
    {
        static $rootUrl = ['', '/'];
        return in_array($this->requestUri, $rootUrl);
    }

    protected function setLocale() : void
    {
        static $existingLocales = ['fr', 'en'];
        $request = $this->requestStack->getMasterRequest();
        preg_match('#^/(.*?)/#', $request->getRequestUri(), $matches);
        if (isset($matches[1]) && in_array($matches[1], $existingLocales)) {
            $request->setLocale($matches[1]);
        }
    }
}
