<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;

class ShowController extends UserRepository
{
    /**
     * @Route("/user/show/{id}")
     */
    public function display(User $user)
    {
        $test = 1;
    }
}
