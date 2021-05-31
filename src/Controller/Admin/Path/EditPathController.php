<?php

namespace App\Controller\Admin\Path;

use App\Controller\Admin\AbstractAdminController;
use App\Repository\PathRepository;
use App\Form\AdminEditPath\FormData as AdminEditPathFormData;
use App\Form\AdminEditPath\FormType as AdminEditPathFormType;
use App\Helper\TwigDefaultParameters;
use App\Manager\Path\PathCreatorManager;
use App\Entity\Path;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditPathController extends AbstractAdminController
{

    private PathCreatorManager $pathCreatorManager;
    private PathRepository $pathRepository;
    private TranslatorInterface $translator;

    public function __construct(
        PathCreatorManager $pathCreatorManager,
        PathRepository $pathRepository,
        TranslatorInterface $translator,
        TwigDefaultParameters $twigDefaultParameters
    ) {
        $this->pathCreatorManager = $pathCreatorManager;
        $this->pathRepository = $pathRepository;
        $this->translator = $translator;
        return parent::__construct($twigDefaultParameters);
    }

    /**
     * @Route("path/edit/{path}", name="admin_path_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function display(Request $request, Path $path = null) : Response
    {
        $editionMode = false;
        try {
            $formData = new AdminEditPathFormData();
            if ($path) {
                $formData->feed($path);
                $editionMode = true;
            }
            $form = $this->createForm(AdminEditPathFormType::class, $formData, ['edition_mode' => $editionMode]);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getConnection()->beginTransaction();
                $path = $formData->getId()
                    ? $this->pathRepository->find($formData->getId())
                    : $this->pathCreatorManager->createFromString($formData->getSlug());
                $path->setCustomTemplate($formData->getCustomTemplate());
                $path->setTitle($formData->getTitle());
                $path->setType($formData->getType());
                $this->getDoctrine()->getManager()->persist($path);
                $this->getDoctrine()->getManager()->flush();
                $this->getDoctrine()->getConnection()->commit();
                $request->getSession()->getFlashBag()->add('notice', $this->translator->trans('Path updated!'));
            }
        } catch (Exception $e) {
            $this->getDoctrine()->getConnection()->rollback();
            $form = $this->createForm(AdminEditPathFormType::class, $path);
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
        } finally {
            return $this->render(
                'back/path/edit.html.twig',
                ['form' => $form->createView(), 'path' => $path]
            );
        }
    }
}