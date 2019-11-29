<?php

namespace App\Helpers;

class Common
{
    public static function getUniqueString(): string
    {
        return md5(uniqid());
    }
}