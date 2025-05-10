<?php

namespace esas\hutkigrosh\wrappers;

use esas\hutkigrosh\ConfigurationFields;
use esas\hutkigrosh\lang\TranslatorBM;

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 13.03.2018
 * Time: 14:44
 */
class ConfigurationWrapperBM extends ConfigurationWrapper
{
    private $params;
    private $settings;

    /**
     * ConfigurationWrapperJoomshopping constructor.
     * @throws \BM\Main\ArgumentNullException
     * @throws \BM\Main\ArgumentOutOfRangeException
     */
    public function __construct($params, $settings)
    {
        parent::__construct(new TranslatorBM());
        //получаем параметры платежной системы
        $this->params = $params;
        $this->settings = $settings;
    }

    /**
     * Произольно название интернет-мазагина
     * @return string
     */
    public function getShopName()
    {
        return $this->getSetting([ConfigurationFields::SHOP_NAME]);
    }

    /**
     * Имя пользователя для доступа к системе ХуткиГрош
     * @return string
     */
    public function getHutkigroshLogin()
    {
        return $this->getOption(ConfigurationFields::LOGIN, true);
    }

    /**
     * Пароль для доступа к системе ХуткиГрош
     * @return string
     */
    public function getHutkigroshPassword()
    {
        return $this->getOption(ConfigurationFields::PASSWORD, true);
    }

    /**
     * Включен ли режим песчоницы
     * @return boolean
     */
    public function isSandbox()
    {
        return $this->checkOn(ConfigurationFields::SANDBOX);
    }

    /**
     * Уникальный идентификатор услуги в ЕРИП
     * @return string
     */
    public function getEripId()
    {
        return $this->getOption(ConfigurationFields::ERIP_ID, true);
    }

    /**
     * Включена ля оповещение клиента по Email
     * @return boolean
     */
    public function isEmailNotification()
    {
        return false;
    }

    /**
     * Включена ля оповещение клиента по Sms
     * @return boolean
     */
    public function isSmsNotification()
    {
        return false;
    }

    public function getCompletionText()
    {
        return $this->translator->getConfigFieldDefault(ConfigurationFields::COMPLETION_TEXT);
    }

    /**
     * Какой статус присвоить заказу после успешно выставления счета в ЕРИП (на шлюз Хуткигрош_
     * @return string
     */
    public function getBillStatusPending()
    {
        return 1;
    }

    /**
     * Какой статус присвоить заказу после успешно оплаты счета в ЕРИП (после вызова callback-а шлюзом ХуткиГрош)
     * @return string
     */
    public function getBillStatusPayed()
    {
        return 2;
    }

    /**
     * Какой статус присвоить заказу в случаче ошибки выставления счета в ЕРИП
     * @return string
     */
    public function getBillStatusFailed()
    {
        return 3;
    }

    /**
     * Какой статус присвоить заказу после успешно оплаты счета в ЕРИП (после вызова callback-а шлюзом ХуткиГрош)
     * @return string
     */
    public function getBillStatusCanceled()
    {
        return 4;
    }

    private function checkOn($key)
    {
        $value = $this->getOption($key);
        return $value == '1' || $value == "true";
    }

    /**
     * Описание системы ХуткиГрош, отображаемое клиенту на этапе оформления заказа
     * @return string
     *
     */
    public function getPaymentMethodDetails()
    {
        // TODO: Implement getPaymentMethodDescription() method.
    }

    /**
     * Необходимо ли добавлять кнопку "выставить в Alfaclick"
     * @return boolean
     */
    public function isAlfaclickButtonEnabled()
    {
        return $this->checkOn(ConfigurationFields::ALFACLICK_BUTTON);
    }

    /**
     * Необходимо ли добавлять кнопку "оплатить картой"
     * @return boolean
     */
    public function isWebpayButtonEnabled()
    {
        return $this->checkOn(ConfigurationFields::WEBPAY_BUTTON);
    }

    public function getOption($key, $warn = false)
    {
        $value = trim(htmlspecialchars($this->params[$key]));

        if ($warn)
            return $this->warnIfEmpty($value, $key);
        else
            return $value;
    }

    public function getSetting($key, $warn = false)
    {
        $value = trim(htmlspecialchars($this->settings[$key]));

        if ($warn)
            return $this->warnIfEmpty($value, $key);
        else
            return $value;
    }

    /**
     * Название системы ХуткиГрош, отображаемое клиенту на этапе оформления заказа
     * @return string
     */
    public function getPaymentMethodName()
    {
        // TODO: Implement getPaymentMethodName() method.
    }

    /**
     * Какой срок действия счета после его выставления (в днях)
     * @return string
     */
    public function getDueInterval()
    {
        return $this->getNumeric($this->getOption(ConfigurationFields::DUE_INTERVAL, true), "1");
    }

    /***
     * В некоторых CMS не получается в настройках хранить html, поэтому использует текст итогового экрана по умолчанию,
     * в который проставлятся значение ERIPPATh
     * @return string
     */
    public function getEripPath()
    {
        return '';
    }

    public function getCurrency()
    {
        return $this->getOption(ConfigurationFields::CURRENCY);
    }
}