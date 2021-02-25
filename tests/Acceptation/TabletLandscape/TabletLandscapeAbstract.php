<?php

namespace App\Tests\Acceptation\TabletLandscape;

use App\Tests\Acceptation\AcceptationAbstract;

class TabletLandscapeAbstract extends AcceptationAbstract
{
    protected function getBrowserWidth() : int
    {
        return 1024;
    }
}
