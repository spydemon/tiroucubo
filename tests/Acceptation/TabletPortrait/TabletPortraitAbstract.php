<?php

namespace App\Tests\Acceptation\TabletPortrait;

use App\Tests\Acceptation\IntegrationAbstract;

abstract class TabletPortraitAbstract extends IntegrationAbstract
{
    protected function getBrowserWidth() : int
    {
        return 768;
    }
}
