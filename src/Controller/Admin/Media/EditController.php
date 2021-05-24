<?php

namespace App\Controller\Admin\Media;

use App\Entity\Media;
use App\Entity\Path;
use App\Entity\PathMedia;
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

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator, TwigDefaultParameters $twigDefaultParameters)
    {
        $this->translator = $translator;
        parent::__construct($twigDefaultParameters);
    }

    /**
     * @Route("/media/edit", name="admin_media_edit", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function display() : Response
    {
        return $this->render('back/media/edit.html.twig');
    }

    /**
     * @Route("/media/edit/{media}", name="admin_media_edit_post", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * TODO: a media could be assigned to several paths.
     * TODO: implementation of the update of an already existing media.
     */
    public function post(PathCreatorManager $pathCreatorManager, Request $request, Media $media = null): Response
    {
        try {
            $this->checkCsrfToken($request);
            $content = $this->getMediaContent();
            $pathString = $request->request->get('path');
            if (!$pathString) {
                throw new Exception($this->translator->trans('The media path is missing.'));
            }
            if (is_null($media)) {
                $media = new Media;
                //TODO: set the process in a manager?
                $this->getDoctrine()->getConnection()->beginTransaction();
                $path = $pathCreatorManager->createFromString($pathString);
                $path->setType(Path::TYPE_MEDIA);
                $media->setContent($content);
                $pathMedia = new PathMedia;
                $pathMedia->setMedia($media);
                $pathMedia->setPath($path);
                $this->getDoctrine()->getManager()->persist($path);
                $this->getDoctrine()->getManager()->persist($media);
                $this->getDoctrine()->getManager()->persist($pathMedia);
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
            $mediaId = is_null($media) ? null : $media->getId();
            return $this->redirectToRoute('admin_media_edit', ['media' => $mediaId]);
        }
    }

    protected function checkCsrfToken(Request $request) : void
    {
        if ($this->isCsrfTokenValid('admin-media-edit', $request->request->get('csrf_token'))) {
            return ;
        }
        throw new Exception('Invalid CSRF token.');
    }

    protected function getMediaContent()
    {
        $contentPath = @$_FILES['image']['tmp_name'];
        if (!$contentPath) {
            throw new Exception($this->translator->trans('Media file can not be empty.'));
        }
        return fopen($contentPath, 'r');
    }
}
