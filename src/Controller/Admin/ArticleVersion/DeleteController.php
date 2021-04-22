<?php

namespace App\Controller\Admin\ArticleVersion;

use App\Controller\Admin\AbstractAdminController;
use App\Entity\ArticleVersion;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DeleteController extends AbstractAdminController
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translatorInterface)
    {
        $this->translator = $translatorInterface;
    }

    /**
     * @Route("article_version/delete/{version}/csrf/{csrf}", name="admin_article_version_delete", methods={"GET"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, ArticleVersion $version, string $csrf) : RedirectResponse
    {
        try {
            if (!$this->isCsrfTokenValid('admin-article-version-delete', $csrf)) {
                throw new Exception($this->translator->trans('Invalid CSRF token.'));
            }
            if ($version->getActive()) {
                throw new Exception('An active article version can not be deleted.');
            }
            $this->getDoctrine()->getManager()->remove($version);
            $this->getDoctrine()->getManager()->flush();
            $request->getSession()->getFlashBag()->add(
                'notice',
                $this->translator->trans('The version {slug} was deleted.', ['slug' => $version->getSlug()])
            );
        } catch (Exception $e) {
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
        } finally {
            return $this->redirectToRoute('admin_article_edit', ['article' => $version->getArticle()->getId()]);
        }

    }
}