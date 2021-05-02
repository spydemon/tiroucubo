<?php

namespace App\Controller;

use App\Helper\TwigDefaultParameters;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * This class should be inherited from each controller that render an entire page (that are not a fragment).
 */
abstract class AbstractBaseController extends AbstractController
{
    private TwigDefaultParameters $twigDefaultParameters;
    private ?string $pageTitle = null;

    public function __construct(TwigDefaultParameters $twigDefaultParameters)
    {
        $this->twigDefaultParameters = $twigDefaultParameters;
    }

    protected function getPageTitle() : ?string
    {
        return $this->pageTitle;
    }

    protected function setPageTitle(string $title) : void
    {
        $this->pageTitle = $title;
    }

    protected function render(string $view, array $parameters = [], Response $response = null): Response
    {
        $parameters['page']['title'] = $this->getPageTitle();
        $parameters = $this->twigDefaultParameters->setDefaultParameters($parameters);
        return parent::render($view, $parameters, $response);
    }
}
