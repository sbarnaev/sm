<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 10.08.2018
 * Time: 12:21
 */

namespace esas\hutkigrosh;


class ConfigurationFields
{
    const SHOP_NAME = 'site_name';
    const LOGIN = 'login';
    const PASSWORD = 'pass';
    const ERIP_ID = 'erip_id';
    const SANDBOX = 'sandbox';
    const ALFACLICK_BUTTON = 'alfaclick';
    const WEBPAY_BUTTON = 'webpay';
    const EMAIL_NOTIFICATION = 'notification_email';
    const SMS_NOTIFICATION = 'notification_sms';
    const COMPLETION_TEXT = 'hutkigrosh_completion_text';
    const ERIP_PATH = 'hutkigrosh_erip_path';
    const PAYMENT_METHOD_NAME = 'payment_method_name';
    const PAYMENT_METHOD_DETAILS = 'payment_method_details';
    const BILL_STATUS_PENDING = 'bill_status_pending';
    const BILL_STATUS_PAYED = 'bill_status_payed';
    const BILL_STATUS_FAILED = 'bill_status_failed';
    const BILL_STATUS_CANCELED = 'bill_status_canceled';
    const DUE_INTERVAL = 'account_time';
    const CURRENCY = 'currency';
}