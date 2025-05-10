<?

class TinkoffMessage
{
    private static $messages = [
        "SALE_TINKOFF_TITLE" => "Тинькофф Банк",
        'SALE_TINKOFF_DESCRIPTION' => 'https://oplata.tinkoff.ru/',

        "SALE_TINKOFF_TERMINAL_ID_NAME" => "Терминал",
        "SALE_TINKOFF_TERMINAL_ID_DESCR" => "Терминал доступен в Личном кабинете https://oplata.tinkoff.ru/",

        "SALE_TINKOFF_SHOP_SECRET_WORD_NAME" => "Пароль",
        "SALE_TINKOFF_SHOP_SECRET_WORD_DESCR" => "Пароль доступен в Личном кабинете https://oplata.tinkoff.ru/",

        'SALE_TINKOFF_TAXATION_NAME' => 'Система налогообложения',
        'SALE_TINKOFF_TAXATION_DESCR' => 'Выберите систему налогообложения для Вашего магазина',
        'SALE_TINKOFF_TAXATION_OSN' => 'Oбщая СН',
        'SALE_TINKOFF_TAXATION_USN_IMCOME' => 'Упрощенная СН (доходы)',
        'SALE_TINKOFF_TAXATION_USN_IMCOME_OUTCOME' => 'Упрощенная СН (доходы минус расходы)',
        'SALE_TINKOFF_TAXATION_ENVD' => 'Единый налог на вмененный доход',
        'SALE_TINKOFF_TAXATION_ESN' => 'Единый сельскохозяйственный налог',
        'SALE_TINKOFF_TAXATION_PATENT' => 'Патентная СН',

        'SALE_TINKOFF_LANGUAGE_NAME' => 'Язык платежной формы',
        'SALE_TINKOFF_LANGUAGE_DESCR' => 'Выберите язык платежной формы для Вашего магазина',
        'SALE_TINKOFF_LANGUAGE_RU' => 'Русский',
        'SALE_TINKOFF_LANGUAGE_EN' => 'Английский',

        'SALE_TINKOFF_ENABLE_TAXATION_NAME' => 'Передавать данные для формирования чека',
        'SALE_TINKOFF_ENABLE_TAXATION_DESCR' => 'Данные чека будут передаваться в онлайн-кассу',
        'SALE_TINKOFF_YES' => 'Да',
        'SALE_TINKOFF_NO' => 'Нет',

        'SALE_TINKOFF_DELIVERY_TAXATION_NAME' => 'Ставка налога для доставки',
        'SALE_TINKOFF_DELIVERY_TAXATION_DESCR' => 'Параметр необходим для добавления информации о доставке в чек. Доставка добавляется в чек отдельной позицией.',
        'SALE_TINKOFF_VAT_NONE' => 'Без НДС',
        'SALE_TINKOFF_VAT_ZERO' => 'НДС 0%',
        'SALE_TINKOFF_VAT_REDUCED' => 'НДС 10%',
        'SALE_TINKOFF_VAT_STANDARD' => 'НДС 18%',
        'SALE_TINKOFF_VAT_TWENTY' => 'НДС 20%',

        'SALE_TINKOFF_PAYBUTTON_NAME' => 'Оплатить',

        'SALE_TINKOFF_UNAVAILABLE' => 'Запрос к платежному сервису был отправлен некорректно. Проверьте настройки',

        'SALE_TINKOFF_EMAIL_NAME' => 'Email пользователя',
        'SALE_TINKOFF_EMAIL_DESCR' => 'Электронная почта, которую указал в заказе пользователь',

        'SALE_TINKOFF_ORDER_INFO' => "Информация о заказе",
        'SALE_TINKOFF_ORDER_PAYMENT' => "Оплата заказа",

        'SALE_TINKOFF_SUCCESS' => "успешно",
        'SALE_TINKOFF_FAIL' => "не успешно",
        'SALE_TINKOFF_FAIL_TEXT' => "Заказ с номером %s не найден",
        'SALE_TINKOFF_SUCCESS_TEXT' => "Заказ с номером %s оплачен %s <br/> Состояние заказа можно узнать на <a href=\"%s\">странице заказа</a>",

        //
        'SALE_TINKOFF_CONNECT_ERROR' => "Не удалось соединиться с платёжным сервисом.",
        'SALE_TINKOFF_SUM_ERROR' => 'Сумма заказа не сходится. Ответ сервиса: %s',
        'SALE_TINKOFF_TOKEN_ERROR' => 'Токены не совпадают. Запрос сервиса: %s',
        'SALE_TINKOFF_STATUS_ERROR' => 'Статус заказа не определён. Чтобы запросить статус вызовите метод getStatus',
        'SALE_TINKOFF_QUERY_ERROR' => 'Не удалось отправить запрос',
        'SALE_TINKOFF_PAYMENT_CANCELED' => 'оплата заказа отменена',

        'SALE_TINKOFF_FIO_NAME' => 'Ф.И.О пользователя',
        'SALE_TINKOFF_FIO_DESCR' => '',
        'SALE_TINKOFF_PHONE_NAME' => 'Телефон пользователя',
        'SALE_TINKOFF_PHONE_DESCR' => '',

        'SALE_TINKOFF_TAX_ERROR' => 'Не удалось получить данные о налоге на товар. Проверьте настройки.',
        'SALE_TINKOFF_TAXATION_ERROR' => 'Не удалось получить данные о системе налогообложения. Проверьте настройки.',
        'SALE_TINKOFF_TAX_DELIVERY_ERROR' => 'Не удалось получить данные о налоге на доставку. Проверьте настройки.',

        "SALE_TINKOFF_PAYMENT_METHOD_NAME" => "Признак способа расчёта",
        "SALE_TINKOFF_PAYMENT_METHOD_FULL_PREPAYMENT" => "Предоплата 100%",
        "SALE_TINKOFF_PAYMENT_METHOD_PREPAYMENT" => "Предоплата",
        "SALE_TINKOFF_PAYMENT_METHOD_ADVANCE" => "Аванc",
        "SALE_TINKOFF_PAYMENT_METHOD_FULL_PAYMENT" => "Полный расчет",
        "SALE_TINKOFF_PAYMENT_METHOD_PARTIAL_PAYMENT" => "Частичный расчет и кредит",
        "SALE_TINKOFF_PAYMENT_METHOD_CREDIT" => "Передача в кредит",
        "SALE_TINKOFF_PAYMENT_METHOD_CREDIT_PAYMENT" => "Оплата кредита",

        "SALE_TINKOFF_PAYMENT_OBJECT_NAME" => "Признак предмета расчёта",
        "SALE_TINKOFF_PAYMENT_METHOD_COMMODITY" => "Товар",
        "SALE_TINKOFF_PAYMENT_METHOD_EXCISE" => "Подакцизный товар",
        "SALE_TINKOFF_PAYMENT_METHOD_JOB" => "Работа",
        "SALE_TINKOFF_PAYMENT_METHOD_SERVICE" => "Услуга",
        "SALE_TINKOFF_PAYMENT_METHOD_GAMBLING_BET" => "Ставка азартной игры",
        "SALE_TINKOFF_PAYMENT_METHOD_GAMBLING_PRIZE" => "Выигрыш азартной игры",
        "SALE_TINKOFF_PAYMENT_METHOD_LOTTERY" => "Лотерейный билет",
        "SALE_TINKOFF_PAYMENT_METHOD_LOTTERY_PRIZE" => "Выигрыш лотереи",
        "SALE_TINKOFF_PAYMENT_METHOD_INTELLECTUAL_ACTIVITY" => "Предоставление результатов интеллектуальной деятельности",
        "SALE_TINKOFF_PAYMENT_METHOD_PAYMENT" => "Платеж",
        "SALE_TINKOFF_PAYMENT_METHOD_AGENT_COMMISSION" => "Агентское вознаграждение",
        "SALE_TINKOFF_PAYMENT_METHOD_PARTIAL_COMPOSITE" => "Составной предмет расчета",
        "SALE_TINKOFF_PAYMENT_METHOD_ANOTHER" => "Иной предмет расчета",

        "SALE_TINKOFF_EMAIL_COMPANY_NAME" => "Email компании",
    ];

    public static function getMessage($key) {
        return isset(self::$messages[$key]) ? self::$messages[$key] : '';
    }
}


