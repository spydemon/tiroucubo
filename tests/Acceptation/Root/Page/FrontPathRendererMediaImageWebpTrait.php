<?php

namespace App\Tests\Acceptation\Root\Page;

trait FrontPathRendererMediaImageWebpTrait
{
    public function testImageResolutionDisplaying()
    {
        $client = $this->getBrowser();
        $client->request('GET', '/en/pictures/mir-test.webp');
        $image = $this->getElementByCssSelector('html img');
        $imageSize = $image->getSize();
        $imageDimension = "{$imageSize->getWidth()}x{$imageSize->getHeight()}";
        $this->assertEquals(
            '1280x720',
            $imageDimension,
            'The image has the expected dimension.'
        );
    }
}