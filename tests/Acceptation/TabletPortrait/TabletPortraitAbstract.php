<?php

namespace App\Tests\Acceptation\TabletPortrait;

use App\Tests\Acceptation\AcceptationAbstract;

abstract class TabletPortraitAbstract extends AcceptationAbstract
{
    protected function getBrowserWidth() : int
    {
        return 768;
    }
}
