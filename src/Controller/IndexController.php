<?php

namespace App\Controller;

use App\Manager\Path\PathRendererManager;
use App\Repository\PathRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractBaseController
{
    private PathRendererManager $pathRendererManager;
    private PathRepository $pathRepository;
    private RequestStack $requestStack;
    private ?string $requestUri;

    public function __construct(
        PathRendererManager $pathRendererManager,
        PathRepository $pathRepository,
        RequestStack $requestStack
    ) {
        $this->pathRendererManager = $pathRendererManager;
        $this->pathRepository = $pathRepository;
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/{path}", name="default", requirements={"path"=".*"})
     * TODO: SEO : index only final pages.
     * TODO: extend the displaying of articles directly owned by the path?
     */
    public function display(string $path = null) : Response
    {
        $this->requestUri = $path;
        $this->setLocale();
        if ($this->isRootUrlAsked()) {
            $locale = $this->getPreferredLocaleFromBrowser();
            return $this->redirect($locale);
        } elseif ($pathObject = $this->pathRepository->findByPath($this->requestUri)) {
            return $this->pathRendererManager->render($pathObject);
        } else {
            throw new NotFoundHttpException();
        }
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
        preg_match('#^/(.*?)(/|\z)#', $request->getRequestUri(), $matches);
        if (isset($matches[1]) && in_array($matches[1], $existingLocales)) {
            $request->setLocale($matches[1]);
        }
    }

    /**
     * Here, we get the preferred language set by the customer browser and sent to us through the "Accept-Language"
     * HTTP header. We only accept the "en" and the "fr" one. If none are set, we return "en" as fallback since its the
     * first element in "getPreferredLanguage" parameter array.
     */
    protected function getPreferredLocaleFromBrowser() : string
    {
        return $this->requestStack->getMasterRequest()->getPreferredLanguage(['en', 'fr']);
    }
}
