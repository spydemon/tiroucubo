<?php

namespace App\Tests\Integration\TabletPortrait;

use App\Tests\Integration\IntegrationAbstract;

abstract class TabletPortraitAbstract extends IntegrationAbstract
{
    protected function getBrowserWidth() : int
    {
        return 768;
    }
}
