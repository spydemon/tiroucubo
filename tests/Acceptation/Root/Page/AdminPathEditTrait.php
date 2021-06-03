<?php

namespace App\Tests\Acceptation\Root\Page;

use App\Tests\Exception\ElementNotExpectedException;
use Facebook\WebDriver\WebDriverKeys;

trait AdminPathEditTrait
{
    public function testPathEdition()
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $client = $this->getBrowser();
        $client->request('GET', '/en/magento/installation');
        $currentTitle = $this->getElementByCssSelector('h1');
        // This fetch will trigger an exception if the css selector is not found.
        $this->getElementByCssSelector('.three-columns');
        $this->assertEquals(
            'Installation',
            $currentTitle->getText(),
            'Original path title is correct.'
        );
        // We directly load the page that manage the "en/magento/installation" path.
        $client->request('GET', '/admin/path/edit/3');
        $this->updatePathContent(
            null,
            'Installation new title',
            '',
            'Always'
        );
        $client->request('GET', '/en/magento/installation');
        $currentTitle = $this->getElementByCssSelector('h1');
        $this->assertEquals(
            'Installation new title',
            $currentTitle->getText(),
            'Path title was updated.'
        );
        // We directly load the page that manage the "en/magento/installation" path.
        $client->request('GET', '/admin/path/edit/3');
        $this->updatePathContent(
            null,
            'Installation new title',
            'front/path/home_en.html.twig',
            'Always'
        );
        $client->request('GET', '/en/magento/installation');
        // This fetch will trigger an exception if the css selector is not found.
        $this->getElementByCssSelector('.one-column');
        $this->resetDatabase();
    }

    public function testPathCreation()
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');

        /**
         * Creation of the new path.
         */
        $client = $this->getBrowser();
        $client->request('GET', '/en/magento/new');
        $this->checkResponseIsA404();
        $client->request('GET', '/admin/path/edit');
        $this->updatePathContent(
            '/en/magento/new',
            'New test path',
            '',
            'Always'
        );

        /**
         * Check "Always visible" path type.
         */
        $client->request('GET', '/en/magento');
        $newPath = $this->getElementByLinkText('New test path');
        $newPath->click();
        $this->checkResponseIsA404();

        /**
         * Check "Dynamic" path type.
         */
        $client->request('GET', '/admin/path/edit/31');
        $this->updatePathContent(
            null,
            null,
            null,
            'Dynamic'
        );
        $client->request('GET', '/en/magento');
        try {
            $this->getElementByLinkText('New test path');
            $this->fail('The new path is not displayed in the menu if it is of "dynamic" type.');
        } catch (ElementNotExpectedException $e) {
            // The expected behavior is that the previous "getElementByLinkTest" throw an exception here.
        }

        /**
         * Check article switch in the path.
         */
        $client->request('GET', '/admin/article/edit/1');
        $pathInput = $this->getElementByCssSelector('form #path');
        $pathInput->sendKeys(WebDriverKeys::CONTROL . 'A' . WebDriverKeys::BACKSPACE);
        $pathInput->sendKeys('en/magento/new');
        $commitInput = $this->getElementByCssSelector('form #commit_message');
        $commitInput->sendKeys('Version created by the AdminPathEditTrait::testPathCreation test.');
        $submitButton = $this->getElementByCssSelector('form input[type="submit"]');
        $submitButton->click();
        $activeButton = $this->getAllElementsByCssSelector('table.version td.active');
        $activeButton[1]->click();
        $client->request('GET', '/en/magento');
        $newPath = $this->getElementByLinkText('New test path');
        $newPath->click();
        $pageTitle = $this->getElementByCssSelector('h1');
        $this->assertEquals(
            'Docker configuration',
            $pageTitle->getText(),
            'The article was moved in our new path.'
        );
        $this->resetDatabase();
    }

    public function testInvalidPathCreation()
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $client = $this->getBrowser();

        $client->request('GET', '/admin/path/edit');
        $this->updatePathContent(
            '/en/magento/new~',
            'New test path',
            '',
            'Always'
        );
        //TODO: the selector will be updated when forms will be stylized.
        $error = $this->getElementByCssSelector('form div div ul li');
        $this->assertEquals(
            'The "/en/magento/new~" complete path contains invalid characters.',
            $error->getText(),
            'Invalid path are marked as error on the form.'
        );
    }

    protected function updatePathContent(
        ?string $slugContent = null,
        ?string $titleContent = null,
        ?string $customTemplateContent = null,
        ?string $typeContent = null
    ) : void {
        if ($slugContent) {
            $slug = $this->getElementByCssSelector('#form_slug');
            // This sendKeys will press ctrl+A and the backspace, meaning we are cleaning the content of the input field.
            $slug->sendKeys(WebDriverKeys::CONTROL . 'A' . WebDriverKeys::BACKSPACE);
            $slug->sendKeys($slugContent);
        }
        if ($titleContent) {
            $title = $this->getElementByCssSelector('#form_title');
            $title->sendKeys(WebDriverKeys::CONTROL . 'A' . WebDriverKeys::BACKSPACE);
            $title->sendKeys($titleContent);
        }
        if ($customTemplateContent) {
            $customTemplate = $this->getElementByCssSelector('#form_custom_template');
            $customTemplate->sendKeys(WebDriverKeys::CONTROL . 'A' . WebDriverKeys::BACKSPACE);
            $customTemplate->sendKeys($customTemplateContent);
        }
        if ($typeContent) {
            // Type input is from "option" type. We thus have to click on it and to write starting characters of
            // the label of the option to select. The browser will thus update the selected option.
            $type = $this->getElementByCssSelector('#form_type');
            $type->click();
            $type->sendKeys($typeContent);
            $type->sendKeys(WebDriverKeys::ENTER);
        }
        $submit = $this->getElementByCssSelector('#form_submit');
        $submit->click();
    }
}
