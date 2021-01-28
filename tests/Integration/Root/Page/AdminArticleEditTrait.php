<?php

namespace App\Tests\Integration\Root\Page;

use Facebook\WebDriver\WebDriverKeys;

trait AdminArticleEditTrait
{
    public function testEmptyFieldsUpdate()
    {
        $this->updateArticleContent('', '', '', '');
        $notification = $this->getElementByCssSelector('.notification .error');
        $this->assertEquals(
            'Missing fields: title, summary, path, content.',
            $notification->getText(),
            'The notification saying that the article was correctly updated is here.'
        );
    }

    public function testSuccessfulUpdate()
    {
        $this->markTestIncomplete(
            'Need to be able to rollback database between each tests before activating this test.'
        );
        return;
        $this->updateArticleContent(
            'New title',
            'fr/magento/new/path',
            'My new summary',
            '<p>My new content</p>'
        );
        $notification = $this->getElementByCssSelector('.notification .notice');
        $this->assertEquals(
            'Article updated!',
            $notification->getText(),
            'The notification saying that the article was correctly updated is here.'
        );
        $this->getBrowser()->request('GET', '/fr/magento/new/path');
        $resultTitle = $this->getElementByCssSelector('h1');
        $resultContent = $this->getElementByCssSelector('article p');
        $this->assertEquals(
            'path',
            $resultTitle->getText(),
            'The updated title of the new article page is correctly set.'
        );
        $this->assertEquals(
            'My new content',
            $resultContent->getText(),
            'The updated content of the new article page is correctly set.'
        );
    }

    protected function updateArticleContent(string $titleValue, string $pathValue, string $summaryValue, string $contentValue)
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $this->getBrowser()->request('GET', '/admin/article/edit/4');
        $title = $this->getElementByCssSelector('form #title');
        $path = $this->getElementByCssSelector('form #path');
        $summary = $this->getElementByCssSelector('form #summary');
        $content = $this->getElementByCssSelector('form #content');
        $submit = $this->getElementByCssSelector('form input[type="submit"]');
        // This sendKeys will press ctrl+A and the backspace, meaning we are cleaning the content of the input field.
        $title->sendKeys(WebDriverKeys::CONTROL . 'A' . WebDriverKeys::BACKSPACE);
        $title->sendKeys($titleValue);
        $path->sendKeys(WebDriverKeys::CONTROL . 'A' . WebDriverKeys::BACKSPACE);
        $path->sendKeys($pathValue);
        $summary->sendKeys(WebDriverKeys::CONTROL . 'A' . WebDriverKeys::BACKSPACE);
        $summary->sendKeys($summaryValue);
        $content->sendKeys(WebDriverKeys::CONTROL . 'A' . WebDriverKeys::BACKSPACE);
        $content->sendKeys($contentValue);
        $submit->click();
    }
}
