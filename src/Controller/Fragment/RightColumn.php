<?php

namespace App\Controller\Fragment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class RightColumn extends AbstractController
{
    public function display() : Response
    {
        return $this->render('fragment/_right-column.html.twig');
    }
}
