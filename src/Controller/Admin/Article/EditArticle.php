<?php

namespace App\Controller\Admin\Article;

use App\Entity\Article;
use App\Controller\Admin\AbstractAdminController;
use App\Manager\Path\PathCreatorManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Exception;

/**
 * TODO: handle translations.
 */
class EditArticle extends AbstractAdminController
{

    private PathCreatorManager $pathCreatorManager;

    public function __construct(
        PathCreatormanager $pathCreatorManager
    ) {
        $this->pathCreatorManager = $pathCreatorManager;
    }

    /**
     * @Route("article/edit/{article}", name="admin_article_edit", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function display(Article $article) : Response
    {
        return $this->render('back/article/edit.html.twig', ['article' => $article]);
    }

    /**
     * @Route("article/edit/{article}", name="admin_article_edit_post", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function post(Article $article, Request $request) : Response
    {
        try {
            $this->checkCsrfToken($request);
            $this->updateArticle($article, $request);
            $request->getSession()->getFlashBag()->add('notice', 'Article updated!');
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
        } finally {
            return $this->redirectToRoute('admin_article_edit', ['article' => $article->getId()]);
        }
    }

    protected function checkCsrfToken(Request $request) : void
    {
        if ($this->isCsrfTokenValid('admin-article-edit', $request->request->get('csrf_token'))) {
            return ;
        }
        throw new Exception('Invalid CSRF token.');
    }

    protected function updateArticle(Article $article, Request $request) : void
    {
        $this->getDoctrine()->getConnection()->beginTransaction();
        try {
            $title = $request->request->get('title');
            $summary = $request->request->get('summary');
            $path = $request->request->get('path');
            $content = $request->request->get('content');
            $missingFields = [];
            if (!$title) {
                $missingFields[] = 'title';
            }
            if (!$summary) {
                $missingFields[] = 'summary';
            }
            if (!$path) {
                $missingFields[] = 'path';
            }
            if (!$content) {
                $missingFields[] = 'content';
            }
            if (count($missingFields)) {
                throw new Exception('Missing fields: ' . implode(', ', $missingFields) . '.');
            }
            $article->setContent($content);
            $path = $this->pathCreatorManager->createFromString($path);
            $article->setPath($path);
            $article->setSummary($summary);
            $article->setTitle($title);
            $this->getDoctrine()->getManager()->persist($article);
            $this->getDoctrine()->getManager()->flush();
            $this->getDoctrine()->getConnection()->commit();
        } catch (Exception $e) {
            $this->getDoctrine()->getConnection()->rollback();
            throw $e;
        }
    }
}
