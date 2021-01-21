<?php

namespace App\Controller\Fragment;

use App\Entity\User;
use App\Manager\User\RoleManager;
use App\Repository\PathTranslationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class Navigation extends AbstractController
{
    private RequestStack $requestStack;
    private RoleManager $roleManager;
    private PathTranslationRepository $pathTranslationRepository;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        PathTranslationRepository $pathTranslationRepository,
        RequestStack $requestStack,
        RoleManager $roleManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->pathTranslationRepository = $pathTranslationRepository;
        $this->requestStack = $requestStack;
        $this->roleManager = $roleManager;
        $this->tokenStorage = $tokenStorage;
    }

    public function display() : Response
    {
        return $this->render(
            'fragment/_navigation.html.twig',
            [
                'user_admin' => $this->hasUserRole(User::USER_ROLE_ADMIN),
                'user_authenticated' => $this->hasUserRole(User::USER_ROLE_AUTHENTICATED),
                'translated_variants' => $this->getPageTranslatedVariant(),
            ]
        );
    }

    protected function hasUserRole(string $role) : bool
    {
        $token = $this->tokenStorage->getToken();
        if (is_null($token)) {
            return false;
        }
        $user = $token->getUser();
        if (is_null($user)) {
            return false;
        }
        return $this->roleManager->hasRole($user, $role);
    }

    protected function getPageTranslatedVariant() : array
    {
        $baseUrl = $this->requestStack->getMasterRequest()->getRequestUri();
        $baseLocale = $this->requestStack->getMasterRequest()->getLocale();
        $pathTranslation = null;
        $variants = [];
        if ($baseLocale == 'en') {
            $pathTranslation = $this->pathTranslationRepository->findByPathEn($baseUrl);
            if ($pathTranslation && $frTranslation = $pathTranslation->getPathFr()) {
                $target = $frTranslation;
            } else {
                $target = '/fr';
            }
            $variants[] = [
                'locale' => 'fr',
                'url' => $target
            ];
        } elseif ($baseLocale == 'fr') {
            $pathTranslation = $this->pathTranslationRepository->findByPathFr($baseUrl);
            if ($pathTranslation && $enTranslation = $pathTranslation->getPathEn()) {
                $target = $enTranslation;
            } else {
                $target = '/en';
            }
            $variants[] = [
                'locale' => 'en',
                'url' => $target,
            ];
        }
        return $variants;
    }
}
