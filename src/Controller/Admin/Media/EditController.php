<?php

namespace App\Controller\Admin\Media;

use App\Entity\Media;
use App\Entity\Path;
use App\Entity\PathMedia;
use App\Form\AdminMediaEdit\FormData as AdminMediaEditFormData;
use App\Form\AdminMediaEdit\FormType as AdminMediaEditFormType;
use App\Helper\TwigDefaultParameters;
use App\Manager\Path\PathCreatorManager;
use App\Controller\Admin\AbstractAdminController;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class EditController extends AbstractAdminController
{

    private PathCreatorManager $pathCreatorManager;
    private TranslatorInterface $translator;

    public function __construct(
        PathCreatorManager $pathCreatorManager,
        TranslatorInterface $translator,
        TwigDefaultParameters $twigDefaultParameters
    ) {
        $this->pathCreatorManager = $pathCreatorManager;
        $this->translator = $translator;
        parent::__construct($twigDefaultParameters);
    }

    /**
     * @Route("/media/edit/{media}", name="admin_media_edit")
     * @IsGranted("ROLE_ADMIN")
     * TODO: implementation of the update of an already existing media.
     */
    public function display(Request $request, Media $media = null) : Response
    {
        $formData = new AdminMediaEditFormData();
        if (!is_null($media)) {
            //TODO $formData->feed($media);
        }
        $form = $this->createForm(AdminMediaEditFormType::class, $formData);
        try {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $media = new Media;
                $tmpFile = tmpfile();
                fwrite($tmpFile, $formData->getMedia()->getContent());
                // The fseek is needed in order to write the integrity of the media in the database.
                // Without it, the internal cursor of the resource will be at the end of it, and thus a null content
                // would be saved.
                fseek($tmpFile, 0);
                $this->getDoctrine()->getConnection()->beginTransaction();
                $media->setContent($tmpFile);
                $this->getDoctrine()->getManager()->persist($media);
                if (count($formData->getPath()) == 0) {
                    throw new Exception($this->translator->trans('At least one path is needed.'));
                }
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
                'form' => $form->createView()
            ]);
        }
    }
}
