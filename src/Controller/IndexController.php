<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractBaseController
{
    /**
     * @return Response
     * @param string|null $path
     * @Route("/{path}", name="default", requirements={"path"=".*"})
     */
    public function display(string $path = null) : Response
    {
        /**
         * TODO: display the home page and each resource based on path.
         */
        return new Response('Path : ' . $path);
    }
}
