<?php

namespace App\Helper;

class TwigDefaultParameters
{
    public function setDefaultParameters(array $pageParameters) : array
    {
        // TODO: define those default parameters automatically.
        $defaultParameters = [
            'page' => [
                'author' => 'Administrator',
                'lang' => 'en',
            ],
            'website' => [
                'title' => 'Tiroucubo'
            ]
        ];
        return array_replace_recursive($defaultParameters, $pageParameters);
    }
}