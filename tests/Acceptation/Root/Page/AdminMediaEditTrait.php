<?php

namespace App\Tests\Acceptation\Root\Page;

use Exception;

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
        $pathField = $this->getElementByCssSelector('form input[name="path"]');
        $pathField->sendKeys('fr/linux/histoire-de-la-creation/pdp-7.jpeg');
        $imageField = $this->getElementByCssSelector('form input[name="image"]');
        $imageField->sendKeys('/root/data/tiroucubo/AdminMediaEdit/pdp-7.jpeg');
        $submitButton = $this->getElementByCssSelector('form input[type="submit"]');
        $submitButton->click();
        $notification = $this->getElementByCssSelector('.notification .error');
        $this->assertEquals(
            'Content type is not allowed.',
            $notification->getText(),
            'Not allowed media type are not saved.'
        );

        /**
         * Check correct uploaded type.
         */
        $client->request('GET', '/admin/media/edit');
        $pathField = $this->getElementByCssSelector('form input[name="path"]');
        $pathField->sendKeys('fr/linux/histoire-de-la-creation/pdp-7.webp');
        $imageField = $this->getElementByCssSelector('form input[name="image"]');
        $imageField->sendKeys('/root/data/tiroucubo/AdminMediaEdit/pdp-7.webp');
        $submitButton = $this->getElementByCssSelector('form input[type="submit"]');
        $submitButton->click();
        $client->request('GET', '/fr/linux/histoire-de-la-creation/pdp-7.webp');
        try {
            $this->getElementByCssSelector('html img');
        } catch (Exception $e) {
            $this->assertTrue(false, 'The new image was correctly uploaded.');
        }

        /**
         * Check upload on an already existing path.
         */
        $client->request('GET', '/admin/media/edit');
        $pathField = $this->getElementByCssSelector('form input[name="path"]');
        $pathField->sendKeys('fr/linux/histoire-de-la-creation/pdp-7.webp');
        $imageField = $this->getElementByCssSelector('form input[name="image"]');
        $imageField->sendKeys('/root/data/tiroucubo/AdminMediaEdit/pdp-7.webp');
        $submitButton = $this->getElementByCssSelector('form input[type="submit"]');
        $submitButton->click();
        $notification = $this->getElementByCssSelector('.notification .error');
        $this->assertEquals(
            'A media already exists on this path.',
            $notification->getText(),
            'Avoid to add several media to the same path.'
        );

        /**
         * Check upload without media file.
         */
        $client->request('GET', '/admin/media/edit');
        $pathField = $this->getElementByCssSelector('form input[name="path"]');
        $pathField->sendKeys('fr/linux/histoire-de-la-creation/pdp-7.webp');
        $submitButton = $this->getElementByCssSelector('form input[type="submit"]');
        $submitButton->click();
        $notification = $this->getElementByCssSelector('.notification .error');
        $this->assertEquals(
            'Media file can not be empty.',
            $notification->getText(),
            'Avoid to proceed the query if the media file is missing in the form.'
        );

        /**
         * Check upload without path.
         */
        $client->request('GET', '/admin/media/edit');
        $imageField = $this->getElementByCssSelector('form input[name="image"]');
        $imageField->sendKeys('/root/data/tiroucubo/AdminMediaEdit/pdp-7.webp');
        $submitButton = $this->getElementByCssSelector('form input[type="submit"]');
        $submitButton->click();
        $notification = $this->getElementByCssSelector('.notification .error');
        $this->assertEquals(
            'The media path is missing.',
            $notification->getText(),
            'Avoid to proceed the query if the media file is missing in the form.'
        );

        $this->resetDatabase();
    }
}