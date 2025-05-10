<?php

namespace esas\hutkigrosh\wrappers;

use esas\hutkigrosh\lang\TranslatorBM;
use Exception;
use Throwable;
use esas\hutkigrosh\wrappers\ConfigurationWrapperBM;


/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 13.03.2018
 * Time: 14:51
 */
class OrderWrapperBM extends OrderWrapper {
    private $order;
    private $delivery;
    private $order_items;
    private $configurationWrapper;
    private $bill_id;
    private $status;
    private $products;


    /**
     * OrderWrapperBM constructor.
     * @param $order
     * @param ConfigurationWrapper|null $configurationWrapper
     * @throws Exception
     */
    public function __construct($order, ConfigurationWrapper $configurationWrapper = null)
    {
        parent::__construct(new TranslatorBM());

        if (!empty($order['order_id'])) {
            $this->order = $order;
            if (!empty($order['ship_method_id'])) {
                $this->delivery = \System::getShipMethod(intval($order['ship_method_id']));
            }

            $this->order_items = \Order::getOrderItems(intval($order['order_id']));
            if (!$this->order_items) {
                throw new Exception("Items no found");
            }
            $this->configurationWrapper = $configurationWrapper;
        } else {
            throw new Exception("Can not get order id!");
        }
    }

    /**
     * Уникальный заказ в рамках CMS
     * @return string
     */
    public function getOrder() {
        return $this->order;
    }

    /**
     * Уникальный номер заказ в рамках CMS
     * @return string
     */
    public function getOrderId() {
        return $this->order['order_id'];
    }

    public function getOrderNumber() {
        return $this->order['order_date'];
    }

    /**
     * Полное имя покупателя
     * @return string
     */
    public function getFullName() {
        if (!empty($this->order['client_name'])) {
            return $this->order['client_name'];
        } else {
            return '';
        }
    }

    /**
     * Мобильный номер покупателя для sms-оповещения
     * (если включено администратором)
     * @return string
     */
    public function getMobilePhone()
    {
        if (!empty($this->order['client_phone'])) {
            return $this->order['client_phone'];
        } else {
            return '';
        }
    }

    /**
     * Email покупателя для email-оповещения
     * (если включено администратором)
     * @return string
     */
    public function getEmail() {
        if (!empty($this->order['client_email'])) {
            return $this->order['client_email'];
        } else {
            $this->logger->error("Can not get email from order. Using empty!");
            return '';
        }
    }

    /**
     * Физический адрес покупателя
     * @return string
     */
    public function getAddress() {
        if (!empty($this->order['client_address'])) {
            return $this->order['client_address'];
        } else {
            return '';
        }
    }

    /**
     * Общая сумма товаров в заказе
     * @return string
     */
    public function getAmount() {
        $amount = 0;
        foreach ($this->order_items as $item) {
            $amount += $item['price'];
        }

        if (!empty($this->delivery) && $this->delivery['tax'] != 0) {
            $amount += $this->delivery['tax'];
        }

        if (!empty($amount)) {
            return $amount;
        } else {
            $this->logger->error("Can not get amount from order. Using 0!");
            return $amount;
        }
    }

    /**
     * Валюта заказа (буквенный код)
     * @return string
     */
    public function getCurrency() {
        $currency = $this->configurationWrapper->getCurrency();

        if (!empty($currency)) {
            return $currency;
        } else {
            $this->logger->error("Can not get currency from order. Using BYN!");
            return "BYN";
        }
    }

    /**
     * Массив товаров в заказе
     * @return \esas\hutkigrosh\wrappers\OrderProductWrapper[]
     */
    public function getProducts() {
        if ($this->products != null) {
            return $this->products;
        }

        if (!empty($this->order_items)) {
            foreach ($this->order_items as $order_item) {
                $this->products[] = new OrderProductWrapperBM($order_item);
            }
        } else {
            $this->logger->error("Can not get products from order. Using empty list!");
            return [];
        }

        return $this->products;
    }

    /**
     * BillId (идентификатор хуткигрош) успешно выставленного счета
     * @return mixed
     */
    public function getBillId() {
        return $this->bill_id;
    }

    /**
     * Текущий статус заказа в CMS
     * @return mixed
     */
    public function getStatus() {
        return $this->order['status'];
    }

    /**
     * Обновляет статус заказа в БД
     * @param $newStatus
     * @return mixed|void
     */
    public function updateStatus($newStatus) {
        if (!empty($newStatus) && $this->getStatus() != $newStatus) {
            $this->status = $newStatus;
        }
    }

    /**
     * Сохраняет привязку billid к заказу
     * @param $billId
     * @return mixed|void
     */
    public function saveBillId($billId) {
        $this->bill_id = $billId;
    }
}