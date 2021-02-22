<?php

namespace App\Tests\Acceptation\Root\Page;

use Exception;
use Facebook\WebDriver\WebDriverKeys;

trait AdminArticleEditTrait
{
    private string $workingArticleEditionPath = 'admin/article/edit/4';
    private string $workingArticleReadingPath = 'fr/magento/new/path';

    public function testEmptyFieldsUpdate()
    {
        $this->updateArticleContent('', '', '', '', '');
        $notification = $this->getElementByCssSelector('.notification .error');
        $this->assertEquals(
            // With the current implementation of the text editor, it is not possible to send empty fields, they will always have at least "<p></p>."
            // 'Missing fields: title, summary, path, content.',
            'Missing fields: title, path, commit_message.',
            $notification->getText(),
            'The notification saying that the article was correctly updated is here.'
        );
    }

    public function testInvalidSlug()
    {
        $this->updateArticleContent(
            'New title',
            'fr/magento/new/path error',
            'My new summary',
            '<p>My new content</p>',
            'Version added by the testInvalidSlug test!'
        );
        $notification = $this->getElementByCssSelector('.notification .error');
        $this->assertEquals(
            'Slug contains invalid characters.',
            $notification->getText(),
            'The slug validity checker seems to work.'
        );
    }

    public function testSuccessfulUpdate()
    {
        try {
            /**
             * Creation of a new version of the article.
             */
            $newCommitMessageContent = 'New version released from the testSuccessfulUpdate test!';
            $this->updateArticleContent(
                'New title',
                $this->workingArticleReadingPath,
                'My new summary',
                '<p>My new content</p>',
                $newCommitMessageContent
            );
            $notification = $this->getElementByCssSelector('.notification .notice');
            $this->assertEquals(
                'Article updated!',
                $notification->getText(),
                'The notification saying that the article was correctly updated is here.'
            );
            $newCommitMessage = $this->getElementByCssSelector('.admin-article-edit table .is-displayed .commit');
            $this->assertEquals(
                $newCommitMessageContent,
                $newCommitMessage->getText(),
                'The new version is the selected one by default and its commit message is saved.'
            );

            /**
             * Check that the displayed version on the front is still the original one.
             */
            $this->getBrowser()->request('GET', "/{$this->workingArticleReadingPath}");
            $resultTitle = $this->getElementByCssSelector('h1');
            $resultContent = $this->getElementByCssSelector('article p:first-of-type');
            $this->assertEquals(
                'path',
                $resultTitle->getText(),
                'The updated title of the new article page is correctly set.'
            );
            $this->assertEquals(
                'composer fr',
                $resultContent->getText(),
                'Old version is still displayed in front since new one was not enabled.'
            );

            /**
             * Enabled the new version of the article.
             */
            $this->getBrowser()->request('GET', "/{$this->workingArticleEditionPath}");
            $newVersionActivationLink = $this->getElementByCssSelector('.admin-article-edit table.version .active .is-not-active');
            $newVersionActivationLink->click();
            $notification = $this->getElementByCssSelector('.notification .notice');
            $this->assertRegExp(
                '/The \w{8} version of the article is now enabled!/',
                $notification->getText(),
                'The notification saying that the version was enabled is here.'
            );

            /**
             * Check that the displayed version on the front is now the new one.
             */
            $this->getBrowser()->request('GET', "/{$this->workingArticleReadingPath}");
            $resultContent = $this->getElementByCssSelector('article p:first-of-type');
            $this->assertEquals(
                '<p>My new content</p>',
                $resultContent->getText(),
                'New version of the article is now displayed on the front-end.'
            );

            /**
             * Disable all version of the article.
             */
            $this->getBrowser()->request('GET', "/{$this->workingArticleEditionPath}");
            $newVersionActivationLink = $this->getElementByCssSelector('.admin-article-edit table.version .active .is-active');
            $newVersionActivationLink->click();
            $notification = $this->getElementByCssSelector('.notification .notice');
            $this->assertRegExp(
                '/The \w{8} version of the article was disabled. The article is now invisible on the front-end./',
                $notification->getText(),
                'The notification saying that the version was disabled is here.'
            );

            /**
             * Check that the displayed version on the front is now the new one.
             */
            $this->getBrowser()->request('GET', "/{$this->workingArticleReadingPath}");
            $this->checkResponseIsA404();

            $this->resetDatabase();
        } catch (Exception $e) {
            $this->resetDatabase();
            throw $e;
        }
    }

    protected function updateArticleContent(
        string $titleValue,
        string $pathValue,
        string $summaryValue,
        string $contentValue,
        string $commitMessage
    ) {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $this->getBrowser()->request('GET', "/{$this->workingArticleEditionPath}");
        $title = $this->getElementByCssSelector('form #title');
        $path = $this->getElementByCssSelector('form #path');
        $summary = $this->getElementByCssSelector('form #summary .ProseMirror');
        $content = $this->getElementByCssSelector('form #content .ProseMirror');
        $commit = $this->getElementByCssSelector('form #commit_message');
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
        $commit->sendKeys($commitMessage);
        $submit->click();
    }
}
