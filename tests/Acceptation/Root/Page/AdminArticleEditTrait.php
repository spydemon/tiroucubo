<?php

namespace App\Tests\Acceptation\Root\Page;

use App\Tests\Exception\ElementNotExpectedException;
use Exception;
use Facebook\WebDriver\WebDriverKeys;

trait AdminArticleEditTrait
{
    private string $workingArticleEditionPath = 'admin/article/edit/4';
    private string $workingArticleReadingPath = 'fr/magento/new/path';

    public function testEmptyFieldsUpdate()
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $this->getBrowser()->request('GET', "/{$this->workingArticleEditionPath}");
        $this->updateArticleContent('', '', '', '', '');
        $notification = $this->getElementByCssSelector('.notification .error');
        $this->assertEquals(
            'Missing fields: title, summary, path, content, commit_message.',
            $notification->getText(),
            'The notification saying that the article was correctly updated is here.'
        );
    }

    public function testInvalidSlug()
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $this->getBrowser()->request('GET', "/{$this->workingArticleEditionPath}");
        $this->updateArticleContent(
            'New title',
            'fr/magento/new/path error',
            '<p>My new summary</p>',
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
            $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
            $this->getBrowser()->request('GET', "/{$this->workingArticleEditionPath}");
            $newCommitMessageContent = 'New version released from the testSuccessfulUpdate test!';
            $this->updateArticleContent(
                'New title',
                $this->workingArticleReadingPath,
                '<p>My new summary</p>',
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
                'New title',
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
                'My new content',
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

    public function testArticleCreation()
    {
        try {
            /**
             * Creation of a new article.
             */
            $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
            $this->getBrowser()->request('GET', "/admin/article");
            $createLink = $this->getElementByCssSelector('#action-article-create');
            $createLink->click();
            $this->assertEquals(
                $this->getAppUrl('/admin/article/edit'),
                $this->getBrowser()->getCurrentURL(),
                'The link leading to the article creation page is working.'
            );
            $this->updateArticleContent(
                'This is a completely new article!',
                'fr/magento/installation/new-article',
                'This is the summary of my new article',
                '<p>This is the content of my new article</p>',
                'First commit for my new article!'
            );
            $notification = $this->getElementByCssSelector('.notification .notice');
            $this->assertEquals(
                'Article created!',
                $notification->getText(),
                'The article seems correctly created.'
            );
            $this->assertRegExp(
                "#^{$this->getAppUrl('/admin/article/edit')}/\d+?#",
                $this->getBrowser()->getCurrentURL(),
                'After the creation, we are redirected on the edition page of the article.'
            );

            /**
             * We check that it is not visible since it is activated.
             */
            $this->getBrowser()->request('GET', '/fr/magento/installation/composer');
            try {
                $this->getElementByLinkText('new-article');
                $this->fail('The new-article link is not present in the menu since the article does not have any activated version.');
            } catch (ElementNotExpectedException $e) {
            }
            $this->getBrowser()->request('GET', '/fr/magento/installation/new-article');
            $this->checkResponseIsA404();

            /**
             * Activation of a version of the article.
             */
            $this->getBrowser()->request('GET', '/admin/article/edit/8');
            $activationLink = $this->getElementByCssSelector('.admin-article-edit table.version .active .is-not-active');
            $activationLink->click();
            $notification = $this->getElementByCssSelector('.notification .notice');
            $this->assertRegExp(
                '/The \w{8} version of the article is now enabled!/',
                $notification->getText(),
                'The notification saying that the version was enabled is here.'
            );

            /**
             * Check that the article is now visible on the front.
             */
            $this->getBrowser()->request('GET', '/fr/magento/installation/composer');
            $link = $this->getElementByLinkText('new-article');
            $link->click();
            $this->assertEquals(
                $this->getBrowser()->getCurrentURL(),
                $this->getAppUrl('/fr/magento/installation/new-article'),
                'The new article is visible in the front.'
            );
            $firstParagraph = $this->getElementByCssSelector('article p:first-of-type');
            $this->assertEquals(
                $firstParagraph->getText(),
                'This is the content of my new article',
                'The new article is visible on the front.'
            );
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
        $title = $this->getElementByCssSelector('form #title');
        $path = $this->getElementByCssSelector('form #path');
        $summary = $this->getElementByCssSelector('form #summary');
        $content = $this->getElementByCssSelector('form #content');
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
