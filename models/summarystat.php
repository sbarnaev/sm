<?php defined('BILLINGMASTER') or die;


class SummaryStat extends Stat {

    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО ПОКУПАТЕЛЕЙ
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public static function getCountPayUsers($start_date, $end_date) {
        $db = Db::getConnection();
        $query = "SELECT COUNT(DISTINCT u.user_id) FROM ".PREFICS."users AS u
                  LEFT JOIN ".PREFICS."orders AS o ON o.client_email = u.email
                  WHERE ".($start_date ? "u.reg_date >= $start_date AND " : '')." u.reg_date < $end_date
                  AND o.summ > 0 AND o.status = 1";

        $result = $db->query($query);
        $count = $result->fetch(PDO::FETCH_ASSOC);

        return $count[0];
    }


    /**
     * ПОЛУЧИТЬ СУММУ ПРОДАЖ, КОТОРЫЕ ПРИНЕСЛИ ПАРТНЕРЫ
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public static function getSumSalesFromPartners($start_date, $end_date) {
        $db = Db::getConnection();
        $query = "SELECT SUM(oi.price) FROM ".PREFICS."order_items AS oi
                  LEFT JOIN ".PREFICS."orders AS o ON o.order_id = oi.order_id
                  WHERE o.partner_id IS NOT NULL AND o.partner_id > 0 AND o.summ > 0 AND o.status = 1
                  AND ".($start_date ? "o.payment_date >= $start_date AND " : '')."o.payment_date < $end_date";

        $result = $db->query($query);
        $data = $result->fetch();

        return $data[0];
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО И СУММУ СЧЕТОВ (НЕОПЛАЧЕННЫХ/ОПЛАЧЕННЫХ)
     * @param $start_date
     * @param $end_date
     * @param bool $is_payed
     * @return mixed
     */
    public static function getCountAndSumInvoices($start_date, $end_date, $is_payed = false) {
        $key = $is_payed ? 'o.payment_date' : 'o.order_date';
        $db = Db::getConnection();
        $query = "SELECT COUNT(o.order_id) AS invoices, SUM(oi.price) AS sum_invoices FROM ".PREFICS."orders AS o
                  LEFT JOIN ".PREFICS."order_items AS oi ON oi.order_id = o.order_id
                  WHERE ".($start_date ? "$key >= $start_date AND " : '')."$key < $end_date
                  AND o.summ > 0 AND o.status ".($is_payed ? '= 1' : 'IN (0,1)');

        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return $data;
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО ПЕРВИЧНЫХ/ВТОРИЧНЫХ СЧЕТОВ
     * @param $start_date
     * @param $end_date
     * @param bool $is_first
     * @return mixed
     */
    public static function getCountFirstOrRepeatInvoices($start_date, $end_date, $is_first = true) {
        $db = Db::getConnection();
        $query = "SELECT COUNT(DISTINCT o.order_id) FROM ".PREFICS."orders AS o
                  LEFT JOIN ".PREFICS."order_items AS oi ON oi.order_id = o.order_id
                  LEFT JOIN ".PREFICS."products AS p ON p.product_id = oi.product_id
                  WHERE ".($start_date ? "o.order_date >= $start_date AND " : '')."o.order_date < $end_date 
                  AND o.summ > 0 AND o.status = 1 AND ".
                  ($is_first ? 'p.to_resale <> 1 AND o.is_recurrent <> 1' : '(p.to_resale = 1 OR o.is_recurrent = 1)');

        $result = $db->query($query);
        $count = $result->fetch(PDO::FETCH_ASSOC);

        return  $count[0];
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО ПОЛЬЗОВАТЕЛЕЙ С ПОДПИСКОЙ
     * @param $start_date
     * @param $end_date
     * @param $is_active
     * @return mixed
     */
    public static function getCountUsersWithSubs($start_date, $end_date, $is_active = false) {
        $db = Db::getConnection();
        $query = "SELECT COUNT(DISTINCT user_id) FROM ".PREFICS."member_maps
                  WHERE".($start_date ? " begin >= $start_date AND" : '')." begin < $end_date"
                  .($is_active ? ' AND status = 1' : '');

        $result = $db->query($query);
        $count = $result->fetch();

        return $count[0];
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ ДЛЯ ОБЩЕЙ СТАТИСТИКИ
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public static function getCommonStatistics($start_date, $end_date) {
        $count_and_sum_invoices = self::getCountAndSumInvoices($start_date, $end_date);
        $count_and_sum_sales = self::getCountAndSumInvoices($start_date, $end_date, true);

        $data = [
            'invoices' => $count_and_sum_invoices['invoices'],
            'sum_invoices' => $count_and_sum_invoices['sum_invoices'],
            'sales' => $count_and_sum_sales['invoices'],
            'sum_sales' => $count_and_sum_sales['sum_invoices'],
            'sum_sales_from_partners' => self::getSumSalesFromPartners($start_date, $end_date),
        ];

        return $data;
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО И СУММУ ПРОДАЖ ДЛЯ МЕМБЕРШИП
     * @param $start_date
     * @param $end_date
     * @return mixed
     */
    public static function getCountAndSumSales2Member($start_date, $end_date)
    {
        $db = Db::getConnection();
        $query = "SELECT COUNT(o.order_id) AS count, SUM(oi.price) AS sum FROM ".PREFICS."orders AS o
                  LEFT JOIN ".PREFICS."order_items AS oi ON oi.order_id = o.order_id
                  LEFT JOIN ".PREFICS."products AS p ON p.product_id = oi.product_id
                  WHERE ".($start_date ? "o.payment_date >= $start_date AND " : '')."o.payment_date < $end_date 
                  AND o.summ > 0 AND o.status = 1 AND p.type_id = 3";

        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return $data;
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО И СУММУ ПЕРВИЧНЫХ/ВТОРИЧНЫХ ПРОДАЖ ДЛЯ МЕМБЕРШИП
     * @param $start_date
     * @param $end_date
     * @param bool $is_first
     * @return mixed
     */
    public static function getCountAndSumFirstOrRepeatSales2Member($start_date, $end_date, $is_first = true) {
        $db = Db::getConnection();
        $query = "SELECT COUNT(o.order_id) AS count, SUM(oi.price) AS sum FROM ".PREFICS."orders AS o
                  LEFT JOIN ".PREFICS."order_items AS oi ON oi.order_id = o.order_id
                  LEFT JOIN ".PREFICS."products AS p ON p.product_id = oi.product_id
                  WHERE ".($start_date ? "o.payment_date >= $start_date AND " : '')."o.payment_date < $end_date 
                  AND o.summ > 0 AND o.status = 1 AND p.type_id = 3 AND ".
                  ($is_first ? 'p.to_resale <> 1 AND o.is_recurrent <> 1' : '(p.to_resale = 1 OR o.is_recurrent = 1)');

        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return  $data;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ СТАТИСТИКИ ДЛЯ МЕМБЕРШИП
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public static function getMemberStatistics($start_date, $end_date) {
        $data1 = self::getCountAndSumSales2Member($start_date, $end_date);
        $data2 = self::getCountAndSumFirstOrRepeatSales2Member($start_date, $end_date);
        $data3 = self::getCountAndSumFirstOrRepeatSales2Member($start_date, $end_date, false);

        $data = [
            'sales' => $data1['count'],
            'sum_sales' => $data1['sum'],
            'first_sales' => $data2['count'],
            'first_sales_sum' => $data2['sum'],
            'repeat_sales' => $data3['count'],
            'repeat_sales_sum' => $data3['sum'],
            'users_with_active_subs' => self::getCountUsersWithSubs($start_date, $end_date, true),
        ];

        return $data;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ СТАТИСТИКИ ПО КАТЕГОРИЯМ
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public static function getCategoryStatistics($start_date, $end_date) {
        $count_and_sum_sales = self::getCountAndSumInvoices($start_date, $end_date, true);

        $db = Db::getConnection();
        $query = "SELECT pc.cat_name, pc.cat_id, COUNT(o.order_id) AS sales, SUM(oi.price) AS sum FROM ".PREFICS."orders AS o
                  LEFT JOIN ".PREFICS."order_items AS oi ON oi.order_id = o.order_id
                  LEFT JOIN ".PREFICS."products AS p ON p.product_id = oi.product_id
                  LEFT JOIN ".PREFICS."product_category AS pc ON pc.cat_id = p.cat_id
                  WHERE".($start_date ? " o.payment_date >= $start_date AND" : '')." o.payment_date < $end_date
                  AND o.status = 1 AND o.summ > 0 GROUP BY pc.cat_id ORDER BY pc.cat_id DESC";

        $result = $db->query($query);
        $cat_data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $cat_id = $row['cat_id'] ?: 0;
            $cat_data[$cat_id] = $row;
        }

        $data = [
            'sales' => $count_and_sum_sales['invoices'],
            'sum_sales' => $count_and_sum_sales['sum_invoices'],
            'cat_data' => $cat_data
        ];

        return $data;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ СТАТИСТИКИ ПО КЛИЕНТАМ
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public static function getClientsStatistics($start_date, $end_date) {
        $count_and_sum_sales = self::getCountAndSumInvoices($start_date, $end_date, true);
        $data = [
            'sales' => $count_and_sum_sales['invoices'],
            'users' => User::countUsers($start_date, $end_date),
            'clients' => User::countUsers($start_date, $end_date, true),
            'users_with_active_subs' => self::getCountUsersWithSubs($start_date, $end_date, true),
        ];

        return $data;
    }


    /**
     * ПОЛУЧИТЬ ДАННЫЕ СТАТИСТИКИ ПО РАССРОЧКАМ
     * @param $start_date
     * @param $end_date
     * @return array
     */
    public static function getInstallmentStatistics($start_date, $end_date) {
        $db = Db::getConnection();
        $query = "SELECT COUNT(o.order_id) AS count, SUM(oi.price) AS sum FROM ".PREFICS."orders AS o
                  LEFT JOIN ".PREFICS."order_items AS oi ON oi.order_id = o.order_id
                  WHERE o.status = 1 AND o.installment_map_id > 0 AND ".($start_date ? "o.payment_date >= $start_date AND " : '')
                  ."o.payment_date < $end_date";
        $result = $db->query($query);
        $sales = $result->fetch(PDO::FETCH_ASSOC);

        $query = "SELECT COUNT(DISTINCT im.id) AS count, SUM(oi.price) AS sum FROM ".PREFICS."installment_map AS im
                  LEFT JOIN ".PREFICS."orders AS o ON o.installment_map_id = im.id
                  LEFT JOIN ".PREFICS."order_items AS oi ON oi.order_id = o.order_id
                  WHERE o.status = 1 AND im.status = 1 AND ".($start_date ? "im.create_date >= $start_date AND " : '')."im.create_date < $end_date";
        $result = $db->query($query);
        $new_sales = $result->fetch(PDO::FETCH_ASSOC);
        $sum_sales_paid = $sales['sum'] - $new_sales['sum'];

        $query = "SELECT COUNT(id) as count, SUM(summ) AS sum FROM ".PREFICS."installment_map WHERE create_date < $end_date
                  AND status = 1".($start_date ? " AND create_date >= $start_date" : '');
        $result = $db->query($query);
        $total_obligations = $result->fetch(PDO::FETCH_ASSOC);


        $expired = null;
        $sum_sales_not_paid = 0;
        if (!$start_date) {
            $query = "SELECT SUM(summ) AS sum FROM ".PREFICS."installment_map WHERE create_date < $end_date AND status = 1";
            $result = $db->query($query);
            $common_sum = $result->fetch(PDO::FETCH_ASSOC);
            $sum_sales_not_paid = $common_sum['sum'];
        } elseif(date("m-Y", $start_date) == date("m-Y")) {
            $install_pays = Product::getSummFromInstallmentCurrMonth($start_date, $end_date);
            if ($install_pays) {
                foreach ($install_pays as $pay){
                    $installment = Product::getInstallmentData($pay['installment_id']);
                    $pay_item = ($pay['summ'] / 100) * $installment['other_pay'];
                    $sum_sales_not_paid += $pay_item;
                }
            }
            $sum_sales_not_paid += $sum_sales_paid;

            $query = "SELECT COUNT(id) as count, SUM(summ) AS sum FROM ".PREFICS."installment_map 
                      WHERE create_date < $end_date AND status = 1 AND create_date >= $start_date AND next_pay < $start_date";
            $result = $db->query($query);
            $data = $result->fetch(PDO::FETCH_ASSOC);
            $expired = $data['count'] ? $data : null;
        } else {
            $query = "SELECT SUM(oi.price) AS sum FROM ".PREFICS."installment_map AS im
                      LEFT JOIN ".PREFICS."orders AS o ON o.installment_map_id = im.id
                      LEFT JOIN ".PREFICS."order_items AS oi ON oi.order_id = o.order_id
                      WHERE o.status = 0 AND im.status = 1 AND ".($start_date ? "im.create_date >= $start_date AND " : '')."im.create_date < $end_date";
            $result = $db->query($query);
            $data = $result->fetch(PDO::FETCH_ASSOC);
            $sum_sales_not_paid = $data['sum'] ?: null;
        }


        $data = [
            'sales' => $sales, // продаж
            'new_sales' => $new_sales, // новых рассрочек/создано обязательств
            'total_obligations' => $total_obligations, // Всего обязательств
            'expired' => $expired, // просрочили
            'sum_sales_paid' => $sum_sales_paid, // фактически оплачено (по старым обязательствам)
            'sum_sales_not_paid' => $sum_sales_not_paid, // должны оплатить
        ];

        return $data;
    }
}
