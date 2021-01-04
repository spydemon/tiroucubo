<?php

namespace App\Controller\Admin\User;

use App\Manager\User\PasswordManager;
use App\Controller\Admin\AbstractAdminController;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateController extends AbstractAdminController
{
    #[Route('/user/create')]
    public function display(ValidatorInterface $validator, PasswordManager $passwordManager) : Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setEmail('test@test.fr');
        $user->setPassword('password');
        $user->setRoles(['ROLE_ADMIN']);
        $passwordManager->setNewPassword($user, 'password');
        $entityManager->persist($user);
        $entityManager->flush();
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return new Response((string) $errors, 400);
        }
        return new Response('CreateController');
    }
}
