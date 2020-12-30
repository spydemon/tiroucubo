<?php

namespace App\Controller\Fragment;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class Notification extends AbstractController
{
    public function display(Request $request) : Response
    {
        // We won't display flash messages if the customer has no session in order to avoid to create them only for this
        // action.
        if ($request->hasPreviousSession()) {
            return $this->render('fragment/_notification.html.twig');
        }
        return new Response();
    }
}
