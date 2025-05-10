<?php

namespace esas\hutkigrosh\controllers;

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 11:55
 */
use esas\hutkigrosh\lang\TranslatorBM;
use esas\hutkigrosh\wrappers\ConfigurationWrapperBM;
use esas\hutkigrosh\wrappers\OrderWrapperBM;

class ControllerNotifyBM extends ControllerNotify
{
    private $is_status_payed = false;


    /**
     * ControllerNotifyBM constructor.
     * @param ConfigurationWrapperBM $configurationWrapper
     */
    public function __construct(ConfigurationWrapperBM $configurationWrapper)
    {
        parent::__construct($configurationWrapper, new TranslatorBM());
        $configurationWrapper = $configurationWrapper;
    }

    /**
     * По локальному идентификатору заказа возвращает wrapper
     * @param $orderNumber
     * @return OrderWrapperBM|mixed
     * @throws \Exception
     */
    public function getOrderWrapperByOrderNumber($orderNumber)
    {
        $order = \Order::getOrderDataByID($orderNumber, 0);
        return new OrderWrapperBM($order, $this->configurationWrapper);
    }

    public function onStatusPayed()
    {
        parent::onStatusPayed();
        $this->is_status_payed = true;
    }

    public function isStatusPayed() {
        return $this->is_status_payed;
    }

    public function getOrderWrapper() {
        return $this->localOrderWrapper;
    }
}