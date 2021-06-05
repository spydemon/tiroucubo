<?php

namespace App\Controller\Admin\Article;

use App\Controller\Admin\AbstractAdminController;
use App\Entity\Article;
use App\Form\AdminArticleEdit\FormType as AdminArticleEditFormType;
use App\Form\AdminArticleEdit\FormData as AdminArticleEditFormData;
use App\Helper\TwigDefaultParameters;
use App\Repository\ArticleVersionRepository;
use App\Manager\Path\PathCreatorManager;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditArticle extends AbstractAdminController
{
    private ArticleVersionRepository $articleVersionRepository;
    private PathCreatorManager $pathCreatorManager;
    private TranslatorInterface $translator;

    public function __construct(
        ArticleVersionRepository $articleVersionRepository,
        PathCreatormanager $pathCreatorManager,
        TranslatorInterface $translator,
        TwigDefaultParameters $twigDefaultParameters
    ) {
        $this->articleVersionRepository = $articleVersionRepository;
        $this->pathCreatorManager = $pathCreatorManager;
        $this->translator = $translator;
        $this->setPageTitle($this->translator->trans('Article edition'));
        return parent::__construct($twigDefaultParameters);
    }

    /**
     * @Route("article/edit/{article}", name="admin_article_edit")
     * @IsGranted("ROLE_ADMIN")
     */
    public function display(Request $request, Article $article = null) : Response
    {
        $version = null;
        try {
            $formData = new AdminArticleEditFormData();
            if (!is_null($article)) {
                //TODO: add a way to edit other versions than only the last one.
                $version = $this->articleVersionRepository->findLastVersionForArticle($article);
                $formData->feed($article, $version);
            }
            $form = $this->createForm(AdminArticleEditFormType::class, $formData);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                if (is_null($article)) {
                    $article = $this->createArticle($formData);
                    $request->getSession()->getFlashBag()
                        ->add('notice', $this->translator->trans('Article created!'));
                } else {
                    $this->updateArticle($article, $formData);
                    $request->getSession()->getFlashBag()
                        ->add('notice', $this->translator->trans('Article updated!'));
                }
                // This redirection is needed in order to load the edition form of the created article or updated data.
                return $this->redirectToRoute('admin_article_edit', ['article' => $article->getId()]);
            }
            return $this->render(
                'back/article/edit.html.twig',
                [
                    'article' => $article,
                    'version' => $version,
                    'form' => $form->createView()
                ]
            );
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
            $form = $this->createForm(AdminArticleEditFormType::class);
            return $this->render(
                'back/article/edit.html.twig',
                [
                    'article' => $article,
                    'version' => $version,
                    'form' => $form->createView()
                ]
            );
        }
    }

    protected function createArticle(AdminArticleEditFormData $formData) : Article
    {
        $article = new Article();
        $this->updateArticle($article, $formData);
        return $article;
    }

    protected function updateArticle(Article $article, AdminArticleEditFormData $formData) : void
    {
        $this->getDoctrine()->getConnection()->beginTransaction();
        $version = $this->articleVersionRepository->createNewVersionForArticle($article);
        try {
            $title = $formData->getTitle();
            $version->setContent($formData->getBody());
            $version->setSummary($formData->getSummary());
            $version->setCommitMessage($formData->getCommit());
            $path = $this->pathCreatorManager->createFromString($formData->getPath());
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
