<?php

namespace App\Tests\Acceptation\Root\Page;

use App\Tests\Exception\ElementNotExpectedException;

trait IndexTrait
{
    public function testRootUrlRedirection()
    {
        $this->changeBrowserLang('fr');
        $this->goToUrl($this->getAppUrl(''));
        $this->assertEquals(
            $this->getAppUrl('/fr'),
            $this->getBrowser()->getCurrentURL(),
            'We are redirected to the french homepage if a browser set with the french as preferred language loads the root page of the website.'
        );
        $content = $this->getElementByCssSelector('.one-column h1');
        $this->assertEquals(
            'Bienvenu sur mon blog !',
            $content->getText(),
            'We are correctly displaying the home_fr twig template.'
        );
        // This test was added since previous regular expression was not matching homepages URL.
        $content = $this->getElementByCssSelector('.item.lang');
        $this->assertEquals(
            '[en]',
            $content->getText(),
            'Language selector works correctly on the French homepage.'
        );
        $this->changeBrowserLang('it');
        $this->goToUrl($this->getAppUrl(''));
        $this->assertEquals(
            $this->getAppUrl('/en'),
            $this->getBrowser()->getCurrentURL(),
            'We are redirected to the english homepage if a browser set with a non supported language loads the root page of the website.'
        );
        $this->changeBrowserLang('en');
        $this->goToUrl($this->getAppUrl(''));
        $this->assertEquals(
            $this->getAppUrl('/en'),
            $this->getBrowser()->getCurrentURL(),
            'We are redirected to the english homepage if a browser set with the english language loads the root page of the website.'
        );
        $content = $this->getElementByCssSelector('.one-column h1');
        $this->assertEquals(
            'Welcome on my blog!',
            $content->getText(),
            'We are correctly displaying the home_en twig template.'
        );
    }

    public function testPathArticleDisplayingTest()
    {
        $this->goToUrl($this->getAppUrl('/fr/magento/installation/configuration-docker'));
        $element = $this->getElementByCssSelector('article > p.article-content-identifier');
        $this->assertEquals(
            'docker-configuration fr',
            $element->getText(),
            'Article is correctly displayed in path action.'
        );
    }

    public function testNonFinalPathArticleDisplaying()
    {
        /**
         * Test that a non-final path is displaying articles summaries and links to them.
         */
        $this->goToUrl($this->getAppUrl('/fr/magento'));
        $articlesTitles = $this->getAllElementsByCssSelector('h2 a');
        if (count($articlesTitles) < 3) {
            $this->fail('Some articles seems missing on the /fr/magento page.');
        }
        $this->assertEquals(
            'Tout à propos des clients',
            $articlesTitles[0]->getText(),
            "The title of the first article in the /fr/magento path is correctly displayed."
        );
        $this->assertEquals(
            'Composer',
            $articlesTitles[1]->getText(),
            "The title of the first article in the /fr/magento path is correctly displayed."
        );
        $this->assertEquals(
            'Configuration Docker',
            $articlesTitles[2]->getText(),
            "The title of the second article in the /fr/magento path is correctly displayed."
        );
        $firstSummary = $this->getElementByCssSelector('article:first-of-type p:first-of-type');
        $this->assertEquals(
            'Résumé de l\'article qui explique tout à propos des clients.',
                $firstSummary->getText(),
            'We are correctly displaying summary content.'
        );

        /**
         * Test that the link to the article is working and that it is correctly displayed on them.
         */
        $articlesTitles[0]->click();
        $this->assertEquals(
            $this->getAppUrl('/fr/magento/utilisation-du-cms/tout-a-propos-des-clients'),
            $this->getBrowser()->getCurrentURL(),
            'Link to the path displaying the detail of the article is working.'
        );
        $articleContent = $this->getElementByCssSelector('article p:first-of-type');
        $this->assertEquals(
            'Contenu de l\'article qui explique tout sur les clients.',
            $articleContent->getText(),
            'Link to the final path displaying the body of the article is working.'
        );
    }

    public function testHomepageLastArticleSummaryDisplaying()
    {
        $this->goToUrl('/fr');
        $lastArticleTitles = $this->getAllElementsByCssSelector('.main-content .article-list .article h3');
        $expectedTitles = ['Tout à propos des clients', 'Composer', 'L\'histoire de la création de Linux', 'Configuration Docker'];
        for ($i = 0; $i <= 3; $i++) {
            $this->assertEquals(
                $expectedTitles[$i],
                $lastArticleTitles[$i]->getText(),
                "Title of summary article $i on the French home is correct."
            );
        }
    }

    public function testHomepageCustomLayoutUse()
    {
        $this->goToUrl('/fr');
        try {
            $this->getElementByCssSelector('body .one-column');
        } catch (ElementNotExpectedException $e) {
            $this->fail('Custom layout to path seems working.');
        }
        $this->assertTrue(true, 'The test did not fail.');
    }

    public function testPathArticlePreview()
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $this->goToUrl('/admin/article/edit/6');
        $allPreviews = $this->getAllElementsByCssSelector('td .action-preview');
        $allPreviews[1]->click();
        $url= $this->getBrowser()->getCurrentURL();
        // We have to remove $1 from the URL in order to be able to use it in the "goToUrl" method.
        // We remove $3 in order to obtain the URL without the "preview" HTTP GET parameter.
        $urlWithoutPreview = preg_replace('~
            (^https?://.*?)  # $1 will be the "http(s)://<website_domain>" present at the begginning of the URL.
            (/.*)            # $2 will be the URI of the resource in the website.
            (\?.*)$          # $3 will be GET parameters.
            ~x',
            '$2',
            $url
        );
        $urlWithPreview = preg_replace(
            '~
                (^https?://.*?) # $1 will be the "http(s)://<website_domain>" present at the begginning of the URL.
                (/.*)           # $2 will be the rest of the URL.
                $~x',
            '$2',
            $url
        );

        /**
         * Test preview displayed for an user with correct rights.
         */
        $articleContent = $this->getElementByCssSelector('article p');
        $this->assertEquals(
            'Content in the second version of the article.',
            $articleContent->getText(),
            'The preview displaying works for admin users.'
        );

        /**
         * Ensure that the activated version is not the same that the one we preview.
         */
        $this->goToUrl($urlWithoutPreview);
        $articleContent = $this->getElementByCssSelector('article p');
        $this->assertNotEquals(
            'Content in the second version of the article.',
            $articleContent->getText(),
            'The preview displaying works for admin users.'
        );

        /**
         * Test that previews are not displayed to anonymous users.
         */
        $logoutLink = $this->getElementByLinkText('Déconnexion');
        $logoutLink->click();
        $this->goToUrl($urlWithPreview);
        $this->checkResponseIsA404();
    }

    public function testMetadataDisplaying()
    {
        $this->goToUrl('/en/magento/installation/docker-configuration');
        $metaAuthor = $this->getElementByCssSelector('head meta[name="author"]');
        $this->assertEquals(
            'Administrator',
            $metaAuthor->getAttribute('content'),
            'Meta author name is correctly displayed.'
        );
        $metaHtml = $this->getElementByCssSelector('html');
        $this->assertEquals(
            'en',
            $metaHtml->getAttribute('lang'),
            'Meta lang value is correctly displayed.'
        );
    }
}
