<?php

namespace esas\hutkigrosh\lang;

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 10.07.2018
 * Time: 11:45
 */
class TranslatorBM extends TranslatorImpl
{
    private $locale = null;

    public function getLocale()
    {
        $this->locale = "ru_RU";
    }
}