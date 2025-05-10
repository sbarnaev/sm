<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 12:38
 */

namespace esas\hutkigrosh\controllers;


use esas\hutkigrosh\lang\TranslatorBM;
use esas\hutkigrosh\wrappers\ConfigurationWrapperBM;
use esas\hutkigrosh\wrappers\OrderWrapper;

class ControllerWebpayFormBM extends ControllerWebpayForm
{
    public function __construct(ConfigurationWrapperBM $configurationWrapper)
    {
        parent::__construct($configurationWrapper, new TranslatorBM());
    }

    /**
     * Основная часть URL для возврата с формы webpay (чаще всего current_url)
     * @return string
     */
    public function getReturnUrl(OrderWrapper $orderWrapper)
    {
        $url = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/payments/hutkigrosh/pay.php";
        return $url;
    }
}