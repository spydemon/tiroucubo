<?php

namespace App\Tests\Integration\Root\Page;

trait IndexTrait
{
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
}
