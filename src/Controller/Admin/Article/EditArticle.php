<?php

namespace App\Controller\Admin\Article;

use App\Entity\Article;
use App\Repository\ArticleVersionRepository;
use App\Controller\Admin\AbstractAdminController;
use App\Manager\Path\PathCreatorManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * TODO: handle translations.
 */
class EditArticle extends AbstractAdminController
{
    private ArticleVersionRepository $articleVersionRepository;
    private PathCreatorManager $pathCreatorManager;
    private TranslatorInterface $translator;

    public function __construct(
        ArticleVersionRepository $articleVersionRepository,
        PathCreatormanager $pathCreatorManager,
        TranslatorInterface $translator
    ) {
        $this->articleVersionRepository = $articleVersionRepository;
        $this->pathCreatorManager = $pathCreatorManager;
        $this->translator = $translator;
        $this->setPageTitle($this->translator->trans('Article edition'));
    }

    /**
     * @Route("article/edit/{article}", name="admin_article_edit", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function display(Request $request, Article $article = null) : Response
    {
        $version = null;
        if (!is_null($article)) {
            $versionSlug = $request->query->get('version');
            if ($versionSlug) {
                $version = $this->articleVersionRepository->findVersionByArticleAndSlug($article, $versionSlug);
                if (is_null($version)) {
                    $request->getSession()->getFlashBag()->add(
                        'error',
                        $this->translator->trans(
                            'The {slug} version of the article is not existing.',
                            ['slug' => $versionSlug]
                        )
                    );
                }
            }
            if (is_null($version)) {
                $version = $this->articleVersionRepository->findLastVersionForArticle($article);
            }
        }
        return $this->render(
            'back/article/edit.html.twig',
            [
                'article' => $article,
                'version' => $version
            ]
        );
    }

    /**
     * @Route("article/edit/{article}", name="admin_article_edit_post", methods={"POST"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function post(Request $request, Article $article = null) : Response
    {
        try {
            $this->checkCsrfToken($request);
            if (is_null($article)) {
                $article = $this->createArticle($request);
                $request->getSession()->getFlashBag()->add('notice', $this->translator->trans('Article created!'));
            } else {
                $this->updateArticle($article, $request);
                $request->getSession()->getFlashBag()->add('notice', $this->translator->trans('Article updated!'));
            }
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

    protected function createArticle(Request $request) : Article
    {
        $article = new Article();
        $this->updateArticle($article, $request);
        return $article;
    }

    protected function updateArticle(Article $article, Request $request) : void
    {
        $this->getDoctrine()->getConnection()->beginTransaction();
        $version = $this->articleVersionRepository->createNewVersionForArticle($article);
        try {
            $title = $request->request->get('title');
            $summary = $request->request->get('summary');
            $path = $request->request->get('path');
            $content = $request->request->get('content');
            $commitMessage = $request->request->get('commit_message');
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
            if (!$commitMessage) {
                $missingFields[] = 'commit_message';
            }
            if (count($missingFields)) {
                throw new Exception('Missing fields: ' . implode(', ', $missingFields) . '.');
            }
            $version->setContent($content);
            $version->setSummary($summary);
            $version->setCommitMessage($commitMessage);
            $path = $this->pathCreatorManager->createFromString($path);
            $article->setPath($path);
            $article->setTitle($title);
            $this->getDoctrine()->getManager()->persist($article);
            $this->getDoctrine()->getManager()->persist($version);
            $this->getDoctrine()->getManager()->flush();
            $this->getDoctrine()->getConnection()->commit();
        } catch (Exception $e) {
            $this->getDoctrine()->getConnection()->rollback();
            throw $e;
        }
    }
}
