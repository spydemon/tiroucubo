<?php

namespace App\Tests\Acceptation\Root\Page;

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
}
