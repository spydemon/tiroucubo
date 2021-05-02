<?php

namespace App\Controller\Admin\Article;

use App\Controller\Admin\AbstractAdminController;
use App\Helper\TwigDefaultParameters;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class IndexArticle extends AbstractAdminController
{
    private ArticleRepository $articleRepository;
    private TranslatorInterface $translator;

    public function __construct(
        ArticleRepository $articleRepository,
        TranslatorInterface $translator,
        TwigDefaultParameters $twigDefaultParameters
    ) {
        $this->articleRepository = $articleRepository;
        $this->translator = $translator;
        $this->setPageTitle($this->translator->trans('Article management'));
        return parent::__construct($twigDefaultParameters);
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
