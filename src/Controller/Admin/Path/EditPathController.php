<?php

namespace App\Controller\Admin\Path;

use App\Controller\Admin\AbstractAdminController;
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
    private TranslatorInterface $translator;

    public function __construct(
        PathCreatorManager $pathCreatorManager,
        TranslatorInterface $translator
    ) {
        $this->pathCreatorManager = $pathCreatorManager;
        $this->translator = $translator;
    }

    /**
     * @Route("path/edit/{path}", name="admin_path_edit", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function display(Path $path = null) : Response
    {
        return $this->render('back/path/edit.html.twig', ['path' => $path, 'types' => $this->getTypes()]);
    }

    /**
     * @Route("path/edit/{path}", name="admin_path_edit_post", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     * TODO: add a delete action. ^^"
     */
    public function post(Request $request, Path $path = null) : Response
    {
        try {
            $this->checkCsrfToken($request);
            if (!$path) {
                $slug = $request->request->get('slug');
                if (!$slug) {
                    throw new Exception('Slug is missing.');
                }
                // TODO: if several path have to be created, intermediate ones will have default values. It means for
                // instance that if we create an "always visible" path, it's parent will be initialized with the default
                // "dynamic" type. It means that our always visible path will in fact remain invisible if it is empty
                // until we manually change the type of all its parents. It could thus be interesting if we implement a
                // way to force displaying of those intermediate paths.
                $path = $this->pathCreatorManager->createFromString($slug);
            }
            $path = $this->updatePath($path, $request);
            $request->getSession()->getFlashBag()->add('notice', $this->translator->trans('Path updated!'));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
        } finally {
            return $this->redirectToRoute('admin_path_edit', ['path' => $path->getId()]);
        }
    }

    protected function checkCsrfToken(Request $request) : void
    {
        if ($this->isCsrfTokenValid('admin-path-edit', $request->request->get('csrf_token'))) {
            return ;
        }
        throw new Exception('Invalid CSRF token.');
    }

    protected function updatePath(Path $path, Request $request) : Path
    {
        try {
            $this->getDoctrine()->getConnection()->beginTransaction();
            $template = $request->request->get('template');
            $title = $request->request->get('title');
            $type = $request->request->get('type');
            $missingFields = [];
            if (!$title) {
                $missingFields[] = 'title';
            }
            if (!$type) {
                $missingFields[] = 'type';
            }
            if (count($missingFields)) {
                throw new Exception('Missing fields: ' . implode(', ', $missingFields) . '.');
            }
            $path->setTitle($title);
            $path->setType($type);
            if ($template) {
                $path->setCustomTemplate($template);
            }
            $this->getDoctrine()->getManager()->persist($path);
            $this->getDoctrine()->getManager()->flush();
            $this->getDoctrine()->getConnection()->commit();
            return $path;
        } catch (Exception $e) {
            $this->getDoctrine()->getConnection()->rollback();
            throw $e;
        }
    }

    private function getTypes() : array
    {
        return [
            ['id' => Path::TYPE_DYNAMIC, 'label' => $this->translator->trans('Dynamic')],
            ['id' => Path::TYPE_ALWAYS_VISIBLE, 'label' => $this->translator->trans('Always visible')],
        ];
    }
}