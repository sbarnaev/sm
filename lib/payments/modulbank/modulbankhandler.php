<?php

require_once (dirname(__FILE__) . '/lib/fpayments.php');

use FPayments\PaymentForm;
use FPayments\FormError;
use FPayments\ReceiptItem;

class ModulbankHandler
{
    private $params;
    private $settings;
    private $order;
    private $amount;
    private $success_url;
    private $fail_url;
    private $cancel_url;
    private $callback_url;
    private $payment_form;

    public function __construct($params, $settings, $order, $total)
    {
        $this->params = $params;
        $this->settings = $settings;
        $this->order = $order;
        $this->amount = number_format($total, 2, '.', '');
        $this->ship_method = !empty($order['ship_method_id']) ? System::getShipMethod($order['ship_method_id']) : null;;
        $this->success_url = $settings['script_url'] . '/payments/modulbank/success.php';
        $this->fail_url = $settings['script_url'] . '/payments/modulbank/fail.php';
        $this->cancel_url = '';
        $this->callback_url = $settings['script_url'] . '/payments/modulbank/result.php';

        $this->payment_form = new PaymentForm(
            trim($this->params['merchant_id']),
            trim($this->params['secret_key']),
            $this->params['test_mode'],
            '',
            'Billing Master v. ' .  CURR_VER
        );
    }

    private function getFormFields()
    {
        $values = $this->payment_form->compose(
            $this->amount,
            $this->params['currency'],
            $this->order['order_id'],
            $this->order['client_email'],
            $this->order['client_name'],
            $this->order['client_phone'],
            $this->success_url,
            $this->fail_url,
            $this->cancel_url,
            $this->callback_url,
            '',
            'Оплата заказа №'.$this->order['order_date'],
            $this->order['client_email'],
            $this->getReceiptItems(),
            '',
            '',
            $this->params['payment_mode'] == 'hold' ? true : false
        );

        return $values;
    }

    public function getFormData () {
        try {
            return array (
                'fields' => $this->getFormFields(),
                'url' => $this->payment_form->get_url()
            );
        } catch (FormError $e) {
            return array('error' => $e->getMessage());
        }
    }

    public function getReceiptItems()
    {
        $order_items = Order::getOrderItems($this->order['order_id']);
        if (!$order_items) exit('Items no found');

        $items_sum = 0;
        $receipt_items = array();

        foreach ($order_items as $order_item) {
            $receipt_items[] = new ReceiptItem(
                $order_item['product_name'],
                $order_item['price'],
                1,
                $this->params['tax_type'],
                $this->params['SNO'],
                $this->params['payment_object'],
                'full_prepayment'
            );
        }

        if ($this->ship_method && $this->ship_method['tax'] != 0) {
            $receipt_items[] = new ReceiptItem(
                $this->ship_method['ship_desc'],
                $this->ship_method['tax'],
                1,
                $this->params['tax_type'],
                $this->params['SNO'],
                'service',
                'full_prepayment'
            );
        }

        return $receipt_items;
    }
}
