<?php

namespace App\Tests\Integration\TabletPortrait;

use App\Tests\Integration\IntegrationAbstract;

abstract class TabletPortraitAbstract extends IntegrationAbstract
{
    protected function getBrowserHeight() : int
    {
        return 1024;
    }

    protected function getBrowserWidth() : int
    {
        return 768;
    }
}
