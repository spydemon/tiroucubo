<?php

namespace App\Controller\Admin\Media;

use App\Entity\Media;
use App\Entity\Path;
use App\Entity\PathMedia;
use App\Form\AdminMediaEdit\FormData as AdminMediaEditFormData;
use App\Form\AdminMediaEdit\FormType as AdminMediaEditFormType;
use App\Form\AdminMediaEdit\FormDataFeeder as AdminMediaEditFormDataFeeder;
use App\Helper\TwigDefaultParameters;
use App\Manager\Path\PathCreatorManager;
use App\Controller\Admin\AbstractAdminController;
use App\Manager\Path\PathRemoverManager;
use App\Repository\PathMediaRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class EditController extends AbstractAdminController
{

    private AdminMediaEditFormDataFeeder $formDataFeeder;
    private PathCreatorManager $pathCreatorManager;
    private PathMediaRepository $pathMediaRepository;
    private PathRemoverManager $pathRemoverManager;
    private TranslatorInterface $translator;

    public function __construct(
        AdminMediaEditFormDataFeeder $formDataFeeder,
        PathCreatorManager $pathCreatorManager,
        PathMediaRepository $pathMediaRepository,
        PathRemoverManager $pathRemoverManager,
        TranslatorInterface $translator,
        TwigDefaultParameters $twigDefaultParameters
    ) {
        $this->formDataFeeder = $formDataFeeder;
        $this->pathCreatorManager = $pathCreatorManager;
        $this->pathMediaRepository = $pathMediaRepository;
        $this->pathRemoverManager = $pathRemoverManager;
        $this->translator = $translator;
        parent::__construct($twigDefaultParameters);
    }

    /**
     * @Route("/media/create", name="admin_media_create")
     * @Route("/media/edit/id/{media}", name="admin_media_edit")
     * @IsGranted("ROLE_ADMIN")
     * TODO: add a way to remove path on media.
     */
    public function display(Request $request, Media $media = null) : Response
    {
        $formData = new AdminMediaEditFormData();
        $editionMode = false;
        if (is_null($media)) {
            $media = new Media();
        } else {
            $this->formDataFeeder->feed($formData, $media);
            $editionMode = true;
        }
        $form = $this->createForm(AdminMediaEditFormType::class, $formData, ['edition_mode' => $editionMode]);
        try {
            $form->handleRequest($request);
            // This condition is true if a customer submit the form present in the page.
            if ($form->isSubmitted() && $form->isValid()) {
                $this->getDoctrine()->getConnection()->beginTransaction();
                $tmpFile = tmpfile();
                $mediaContent = $formData->getMedia();
                if ($mediaContent) {
                    fwrite($tmpFile, $mediaContent->getContent());
                    // The fseek is needed in order to write the integrity of the media in the database.
                    // Without it, the internal cursor of the resource will be at the end of it, and thus a null content
                    // would be saved.
                    fseek($tmpFile, 0);
                    $media->setContent($tmpFile);
                    $this->getDoctrine()->getManager()->persist($media);
                } elseif (!$editionMode) {
                    throw new Exception($this->translator->trans('The media is missing.'));
                }
                if (count($formData->getPath()) == 0) {
                    throw new Exception($this->translator->trans('At least one path is needed.'));
                }
                // If we are in the edition mode, we remove all already existing paths on the media in order to avoid
                // duplicates when we save new ones present in the form since all of them will be inside it. This
                // solution seems easier to implement than checking if they are still present in the form or not and
                // deleting only ones really removed and ignoring other.
                if ($editionMode) {
                    foreach ($this->pathMediaRepository->findPathsByMedia($media) as $pathMedia) {
                        $this->pathRemoverManager->removePath($pathMedia->getPath());
                    }
                }
                // We save new path that will reach the media.
                foreach ($formData->getPath() as $currentPath) {
                    $path = $this->pathCreatorManager->createFromString($currentPath);
                    $path->setType(Path::TYPE_MEDIA);
                    $pathMedia = new PathMedia;
                    $pathMedia->setMedia($media);
                    $pathMedia->setPath($path);
                    $this->getDoctrine()->getManager()->persist($path);
                    $this->getDoctrine()->getManager()->persist($pathMedia);
                }
                $this->getDoctrine()->getManager()->flush();
                $this->getDoctrine()->getConnection()->commit();
                $request->getSession()->getFlashBag()
                    ->add('notice', $this->translator->trans('The new media was correctly created!'));
            }
        } catch (UniqueConstraintViolationException $e) {
            $request->getSession()->getFlashBag()
                ->add('error', $this->translator->trans('A media already exists on this path.'));
            $this->getDoctrine()->getConnection()->rollback();
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
            $this->getDoctrine()->getConnection()->rollback();
        } finally {
            return $this->render('back/media/edit.html.twig', [
                'form' => $form->createView(),
                'data' => $formData
            ]);
        }
    }
}
