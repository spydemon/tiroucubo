<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * This class should be inherited from each controller that render an entire page (that are not a fragment).
 */
abstract class AbstractBaseController extends AbstractController
{
    private ?string $pageTitle = null;

    protected function addDefaultParameters(array $baseParameters) : array
    {
        // TODO: define those default parameters automatically.
        $defaultParameters = [
            'page' => [
                'author' => 'Administrator',
                'lang' => 'en',
                'title' => $this->getPageTitle()
            ],
            'website' => [
                'title' => 'Spyzone'
            ]
        ];
        return array_replace_recursive($defaultParameters, $baseParameters);
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
        $parameters = $this->addDefaultParameters($parameters);
        return parent::render($view, $parameters, $response);
    }
}
