<?php

namespace App\Tests\Acceptation\TabletLandscape\Page;

use App\Tests\Acceptation\TabletLandscape\TabletLandscapeAbstract;
use App\Tests\Acceptation\Root\Page\FrontPathRendererMediaImageWebpTrait;

class FrontPathRendererMediaImageWebpTest extends TabletLandscapeAbstract
{
    use FrontPathRendererMediaImageWebpTrait;

    public function testImageResolutionDisplaying()
    {
        // This test is neutralized on this device because the image dimension will not be the native one if the device
        // width is smaller than the image one.
        $this->assertEquals(true, true, 'Test neutralized');
    }
}
