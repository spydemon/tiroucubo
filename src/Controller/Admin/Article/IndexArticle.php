<?php

namespace App\Controller\Admin\Article;

use App\Controller\Admin\AbstractAdminController;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexArticle extends AbstractAdminController
{
    private ArticleRepository $articleRepository;

    public function __construct(
        ArticleRepository $articleRepository
    ) {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @Route("/article", name="admin_article_index")
     * @IsGranted("ROLE_ADMIN")
     */
    public function display(Request $request) : Response
    {
        $sortOrder = $this->getSortOrder($request);
        return $this->render(
            'back/article/index.html.twig',
            [
                'articles' => $this->getAllArticlesSortedBy($sortOrder)
            ]
        );
    }

    protected function getAllArticlesSortedBy(string $sortOrder) : array
    {
        switch ($sortOrder) {
            case 'creation_date':
                return $this->articleRepository->getAllArticlesSortedByCreationDate();
            case 'update_date':
                return $this->articleRepository->getAllArticlesSortedByUpdateDate();
            case 'path':
                return $this->articleRepository->getAllArticlesSortedByPath();
            default:
                return $this->articleRepository->getAllArticlesSortedById();
        }
    }

    protected function getSortOrder(Request $request) : string
    {
        static $allowedOrder = ['id', 'path', 'creation_date', 'update_date'];
        $order = $request->query->get('sort', 'id');
        return in_array($order, $allowedOrder) ? $order : 'id';
    }
}
