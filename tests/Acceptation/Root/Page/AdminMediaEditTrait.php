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
        $pathField = $this->getElementByCssSelector('#form_path');
        $pathField->sendKeys('fr/linux/histoire-de-la-creation/pdp-7.jpeg');
        $imageField = $this->getElementByCssSelector('#form_media');
        $imageField->sendKeys('/root/data/tiroucubo/AdminMediaEdit/pdp-7.jpeg');
        $submitButton = $this->getElementByCssSelector('#form_submit');
        $submitButton->click();
        //TODO: the selector will be updated when forms will be stylized.
        $notification = $this->getElementByCssSelector('form div div ul li');
        $this->assertEquals(
            'Content type is not allowed. Allowed ones are: image/webp',
            $notification->getText(),
            'Not allowed media type are not saved.'
        );

        /**
         * Check correct uploaded type.
         */
        $client->request('GET', '/admin/media/edit');
        $pathField = $this->getElementByCssSelector('#form_path');
        $pathField->sendKeys('fr/linux/histoire-de-la-creation/pdp-7.webp');
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

        /**
         * Check upload on an already existing path.
         */
        $client->request('GET', '/admin/media/edit');
        $pathField = $this->getElementByCssSelector('#form_path');
        $pathField->sendKeys('fr/linux/histoire-de-la-creation/pdp-7.webp');
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
        $this->getElementByCssSelector('form #form_path[required="required"]');
        $this->getElementByCssSelector('form #form_media[required="required"]');
        $this->assertTrue(true, 'Required fields has the "required" flag.');

        $this->resetDatabase();
    }
}