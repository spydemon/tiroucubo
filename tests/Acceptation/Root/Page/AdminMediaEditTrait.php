<?php

namespace App\Tests\Acceptation\Root\Page;

use Exception;
use Facebook\WebDriver\WebDriverKeys;

trait AdminMediaEditTrait
{

    public function testCreation()
    {
        $this->loginCustomer('admin@tiroucubo.local', 'pa$$word');
        $client = $this->getBrowser();

        /**
         * Check incorrect uploaded type.
         */
        $client->request('GET', '/admin/media/edit');
        $this->addPath('fr/linux/histoire-de-la-creation/pdp-7.jpeg');
        $imageField = $this->getElementByCssSelector('#form_media');
        $imageField->sendKeys('/root/data/tiroucubo/AdminMediaEdit/pdp-7.jpeg');
        $submitButton = $this->getElementByCssSelector('#form_submit');
        $submitButton->click();
        //TODO: the selector will be updated when forms will be stylized.
        $notification = $this->getElementByCssSelector('form div ul li');
        $this->assertEquals(
            'Content type is not allowed. Allowed ones are: image/webp',
            $notification->getText(),
            'Not allowed media type are not saved.'
        );

        /**
         * Check correct uploaded type.
         */
        $client->request('GET', '/admin/media/edit');
        $this->addPath('fr/linux/histoire-de-la-creation/pdp-7.webp');
        $this->addPath('en/linux/history-of-its-creation/pdp-7.webp');
        $imageField = $this->getElementByCssSelector('#form_media');
        $imageField->sendKeys('/root/data/tiroucubo/AdminMediaEdit/pdp-7.webp');
        $submitButton = $this->getElementByCssSelector('#form_submit');
        $submitButton->click();
        $client->request('GET', '/fr/linux/histoire-de-la-creation/pdp-7.webp');
        try {
            $this->getElementByCssSelector('html img');
        } catch (Exception $e) {
            $this->assertTrue(false, 'The new image was correctly uploaded.');
        }
        $client->request('GET', '/en/linux/history-of-its-creation/pdp-7.webp');
        try {
            $this->getElementByCssSelector('html img');
        } catch (Exception $e) {
            $this->assertTrue(false, 'The new image was correctly uploaded.');
        }

        /**
         * Check upload on an already existing path.
         */
        $client->request('GET', '/admin/media/edit');
        $this->addPath('fr/linux/histoire-de-la-creation/pdp-7.webp');
        $imageField = $this->getElementByCssSelector('#form_media');
        $imageField->sendKeys('/root/data/tiroucubo/AdminMediaEdit/pdp-7.webp');
        $submitButton = $this->getElementByCssSelector('#form_submit');
        $submitButton->click();
        $notification = $this->getElementByCssSelector('.notification .error');
        $this->assertEquals(
            'A media already exists on this path.',
            $notification->getText(),
            'Avoid to add several media to the same path.'
        );

        /**
         * Check mandatory fields.
         */
        $client->request('GET', '/admin/media/edit');
        $this->addPath('');
        $this->addPath('');
        $this->getElementByCssSelector('form #form_path_2[required="required"]');
        $this->getElementByCssSelector('form #form_path_3[required="required"]');
        $this->getElementByCssSelector('form #form_media[required="required"]');
        $this->assertTrue(true, 'Required fields has the "required" flag.');

        /**
         * Check that at least a path is provided.
         */
        $client->request('GET', '/admin/media/edit');
        $imageField = $this->getElementByCssSelector('#form_media');
        $imageField->sendKeys('/root/data/tiroucubo/AdminMediaEdit/pdp-7.webp');
        $submitButton = $this->getElementByCssSelector('#form_submit');
        $submitButton->click();
        $notification = $this->getElementByCssSelector('form ul li');
        $this->assertEquals(
            'At least one path is needed.',
            $notification->getText(),
            'An error is thrown if no path are specified.'
        );

        $this->resetDatabase();
    }

    protected function addPath(string $path) : void
    {
        $addPathButton = $this->getElementByCssSelector('button.add-path');
        $addPathButton->click();
        $pathInputs = $this->getAllElementsByCssSelector('ul#path_list li input');
        $lastPathInput = array_pop($pathInputs);
        // This sendKeys will press ctrl+A and the backspace, meaning we are cleaning the content of the input field.
        $lastPathInput->sendKeys(WebDriverKeys::CONTROL . 'A' . WebDriverKeys::BACKSPACE);
        $lastPathInput->sendKeys($path);
    }
}