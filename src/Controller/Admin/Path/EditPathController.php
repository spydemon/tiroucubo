<?php

namespace App\Controller\Admin\Path;

use App\Controller\Admin\AbstractAdminController;
use App\Entity\Path;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditPathController extends AbstractAdminController
{

    private TranslatorInterface $translator;

    public function __construct(
        TranslatorInterface $translator
    ) {
        $this->translator = $translator;
    }

    /**
     * @Route("/path/edit/{path}", name="admin_path_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function display(Path $path) : Response
    {
        return $this->render('back/path/edit.html.twig', ['path' => $path, 'types' => $this->getTypes()]);
    }

    private function getTypes() : array
    {
        return [
            ['id' => Path::TYPE_DYNAMIC, 'label' => $this->translator->trans('Dynamic')],
            ['id' => Path::TYPE_ALWAYS_VISIBLE, 'label' => $this->translator->trans('Always visible')],
        ];
    }
}