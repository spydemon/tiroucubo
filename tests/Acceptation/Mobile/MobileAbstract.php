<?php

namespace App\Tests\Acceptation\Mobile;

use App\Tests\Acceptation\AcceptationAbstract;

class MobileAbstract extends AcceptationAbstract
{

    protected function getBrowserWidth() : int
    {
        return 360;
    }
}
