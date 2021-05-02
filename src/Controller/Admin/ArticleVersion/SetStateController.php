<?php

namespace App\Controller\Admin\ArticleVersion;

use App\Controller\Admin\AbstractAdminController;
use App\Helper\TwigDefaultParameters;
use App\Entity\ArticleVersion;
use App\Repository\ArticleVersionRepository;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SetStateController extends AbstractAdminController
{
    private ArticleVersionRepository $articleVersionRepository;
    private TranslatorInterface $translator;

    public function __construct(
        ArticleVersionRepository $articleVersionRepository,
        TranslatorInterface $translator,
        TwigdefaultParameters $twigDefaultParameters
    ) {
        $this->articleVersionRepository = $articleVersionRepository;
        $this->translator = $translator;
        return parent::__construct($twigDefaultParameters);
    }

    /**
     * @Route("article_version/activate/{version}", name="admin_article_version_activate", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function activate(Request $request, ArticleVersion $version) : RedirectResponse
    {
        try {
            if ($version->getActive() == true) {
                throw new Exception($this->translator->trans(
                    'The version {slug} was already enabled.',
                    ['slug' => $version->getSlug()]
                ));
            }
            $this->articleVersionRepository->activeVersion($version);
            $request->getSession()->getFlashBag()->add('notice', $this->translator->trans(
                'The {slug} version of the article is now enabled!',
                ['slug' => $version->getSlug()]
            ));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
        } finally {
            return $this->redirectToRoute('admin_article_edit', ['article' => $version->getArticle()->getId()]);
        }
    }

    /**
     * @Route("article_version/deactivate/{version}", name="admin_article_version_deactivate", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     * TODO: handle the front displaying of article without any version active.
     */
    public function deactivate(Request $request, ArticleVersion $version): RedirectResponse
    {
        try {
            if ($version->getActive() == false) {
                throw new Exception($this->translator->trans(
                    'The version {slug} was already disabled.',
                    ['slug' => $version->getSlug()]
                ));
            }
            $version->setActive(false);
            $this->getDoctrine()->getManager()->persist($version);
            $this->getDoctrine()->getManager()->flush();
            $request->getSession()->getFlashBag()->add('notice', $this->translator->trans(
                'The {slug} version of the article was disabled. The article is now invisible on the front-end.',
                ['slug' => $version->getSlug()]
            ));
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
        } finally {
            return $this->redirectToRoute('admin_article_edit', ['article' => $version->getArticle()->getId()]);
        }
    }
}
