<?php defined('BILLINGMASTER') or die;

class Installment {


    /**
     * СОЗДАТЬ РАССРОЧКУ
     * @param $name
     * @param $status
     * @param $first_pay
     * @param $other_pay
     * @param $max_periods
     * @param $period_freq
     * @param $installment_rules
     * @param $sort
     * @param $approve
     * @param $letters
     * @param $sms
     * @param $notif
     * @param $expired
     * @param $installment_desc
     * @param $sanctions
     * @param $minimal
     * @param $fields
     * @param $prepayment
     * @param $date_second_payment
     * @return bool
     */
    public static function addInstalment($name, $status, $first_pay, $other_pay, $max_periods, $period_freq, $installment_rules,
        $sort, $approve, $letters, $sms, $notif, $expired, $installment_desc, $sanctions,
        $minimal, $fields, $prepayment, $date_second_payment)
    {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'installment_tune (title, enable, first_pay, other_pay, max_periods, period_freq,
                    installment_rules, sort, approve, letters, sms, notif, expired, installment_desc, sanctions, minimal,
                    fields, prepayment, date_second_payment) 
                VALUES (:title, :enable, :first_pay, :other_pay, :max_periods, :period_freq, :installment_rules, :sort,
                    :approve, :letters, :sms, :notif, :expired, :installment_desc, :sanctions, :minimal, :fields,
                    :prepayment, :date_second_payment)';

        $result = $db->prepare($sql);
        $result->bindParam(':title', $name, PDO::PARAM_STR);
        $result->bindParam(':enable', $status, PDO::PARAM_INT);
        $result->bindParam(':first_pay', $first_pay, PDO::PARAM_STR);
        $result->bindParam(':other_pay', $other_pay, PDO::PARAM_STR);
        $result->bindParam(':max_periods', $max_periods, PDO::PARAM_INT);
        $result->bindParam(':period_freq', $period_freq, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':installment_rules', $installment_rules, PDO::PARAM_STR);
        $result->bindParam(':approve', $approve, PDO::PARAM_INT);
        $result->bindParam(':letters', $letters, PDO::PARAM_STR);
        $result->bindParam(':sms', $sms, PDO::PARAM_STR);
        $result->bindParam(':notif', $notif, PDO::PARAM_STR);
        $result->bindParam(':expired', $expired, PDO::PARAM_INT);
        $result->bindParam(':installment_desc', $installment_desc, PDO::PARAM_STR);
        $result->bindParam(':sanctions', $sanctions, PDO::PARAM_INT);
        $result->bindParam(':minimal', $minimal, PDO::PARAM_INT);
        $result->bindParam(':fields', $fields, PDO::PARAM_STR);
        $result->bindParam(':prepayment', $prepayment, PDO::PARAM_INT);
        $result->bindParam(':date_second_payment', $date_second_payment, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * ИЗМЕНИТЬ РАССРОЧКУ
     * @param $id
     * @param $name
     * @param $status
     * @param $first_pay
     * @param $other_pay
     * @param $installment_rules
     * @param $sort
     * @param $approve
     * @param $letters
     * @param $sms
     * @param $notif
     * @param $expired
     * @param $installment_desc
     * @param $sanctions
     * @param $minimal
     * @param $increase
     * @param $fields
     * @param $period_freq
     * @param $prepayment
     * @param $date_second_payment
     * @return bool
     */
    public static function editInstalment($id, $name, $status, $first_pay, $other_pay, $installment_rules, $sort, $approve,
        $letters,$sms, $notif, $expired, $installment_desc, $sanctions, $minimal, $increase,
        $fields, $period_freq, $prepayment, $date_second_payment)
    {

        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS."installment_tune SET title = :title, enable = :enable, first_pay = :first_pay, other_pay = :other_pay,
                installment_rules = :installment_rules, sort = :sort, approve = :approve, letters = :letters, sms = :sms, notif = :notif,
                expired = :expired, installment_desc = :installment_desc, sanctions = :sanctions, minimal = :minimal, increase = :increase,
                fields = :fields, period_freq = :period_freq, prepayment = :prepayment, date_second_payment = :date_second_payment
                WHERE id = :id";

        $result = $db->prepare($sql);
        $result->bindParam(':title', $name, PDO::PARAM_STR);
        $result->bindParam(':enable', $status, PDO::PARAM_INT);
        $result->bindParam(':first_pay', $first_pay, PDO::PARAM_STR);
        $result->bindParam(':other_pay', $other_pay, PDO::PARAM_STR);
        $result->bindParam(':period_freq', $period_freq, PDO::PARAM_INT);
        $result->bindParam(':sort', $sort, PDO::PARAM_INT);
        $result->bindParam(':installment_rules', $installment_rules, PDO::PARAM_STR);
        $result->bindParam(':approve', $approve, PDO::PARAM_INT);
        $result->bindParam(':letters', $letters, PDO::PARAM_STR);
        $result->bindParam(':sms', $sms, PDO::PARAM_STR);
        $result->bindParam(':sms', $sms, PDO::PARAM_STR);
        $result->bindParam(':notif', $notif, PDO::PARAM_STR);
        $result->bindParam(':expired', $expired, PDO::PARAM_INT);
        $result->bindParam(':installment_desc', $installment_desc, PDO::PARAM_STR);
        $result->bindParam(':sanctions', $sanctions, PDO::PARAM_INT);
        $result->bindParam(':minimal', $minimal, PDO::PARAM_INT);
        $result->bindParam(':increase', $increase, PDO::PARAM_INT);
        $result->bindParam(':fields', $fields, PDO::PARAM_STR);
        $result->bindParam(':prepayment', $prepayment, PDO::PARAM_INT);
        $result->bindParam(':date_second_payment', $date_second_payment, PDO::PARAM_INT);
        $result->bindParam(':id', $id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * @param $data
     * @param $total
     * @return array
     */
    public static function getPays($data, $total) {
        $increase_pay = $data['increase'] > 0 ? round($data['increase'] / $data['max_periods']) : 0;
        $first_pay = round(($total / 100) * $data['first_pay']) + $increase_pay;
        $other_pay = round(($total / 100) * $data['other_pay']) + $increase_pay;

        return compact(['first_pay', 'other_pay']);
    }


    /**
     * ОБНОВИТЬ КОЛ_ВО ПЛАТЕЖЕЙ ПО РАССРОЧКЕ
     * @param $map_id
     * @param $pay_actions
     * @return bool
     */
    public static function updateIntallmentMapPayActions($map_id, $pay_actions)
    {
        $db = Db::getConnection();  
        $sql = 'UPDATE '.PREFICS.'installment_map SET pay_actions = :pay_actions WHERE id = '.$map_id;
        $result = $db->prepare($sql);
        $result->bindParam(':pay_actions', $pay_actions, PDO::PARAM_STR);

        return $result->execute();
    }


    /**
     * ПОЛУЧИТЬ ОБЩУЮ СУММУ ЗАКАЗА
     * @param $installment_data
     * @param $installment_map_data
     * @return mixed
     */
    public static function getTotalSumOrder($installment_data, $installment_map_data) {
        return $installment_map_data['summ'] - $installment_data['increase'];
    }


    /**
     * @param $pay_actions
     * @param $map
     * @return float
     */
    public static function getSum2Pay($pay_actions, $map) {
        $count = count($pay_actions);
        $pay_sum = array_sum(array_column($pay_actions, 'summ')); // считаем сколько уже заплачено
        $pay_periods = $map['max_periods'] - $count;
        $sum_to_pay = round(($map['summ'] - $pay_sum)/ $pay_periods);

        return $sum_to_pay;
    }


    /**
     * ОБНОВИТЬ КАРТУ РАССРОЧЕК ПРИ ПЛАТЕЖАХ
     * @param $order
     * @param $installment_data
     * @param $installment_map_data
     * @param $ahead
     * @param $order_sum
     * @return bool|mixed
     */
    public static function updateInstallMap($order, $installment_data, $installment_map_data, $ahead, $order_sum)
    {
        $db = Db::getConnection();
        $pay_actions = []; //Массив для записи платежей

        if (!empty($installment_map_data)) {
            $pay_actions = unserialize(base64_decode($installment_map_data['pay_actions'])); // Посчитать сколько было платежей
            $count_pays = !empty($pay_actions) ? count($pay_actions) : 0;
            $update_date = $count_pays == 0 ? time() : $installment_map_data['create_date'];
            $user = User::getUserDataByEmail($order['client_email']);
            $now_pay = $count_pays + 1; // номер платежа на сегодняшний день

            self::actions2Payments($order, $installment_map_data, $count_pays, $now_pay, $user, $ahead);


            // Вычислить какой это платёж по счёту, если последний, то закрываем рассрочку, если нет, то составляем массив для записи в pay_action
            // и вычисляем дату след. платежа
            $next_order = 0; // обнуляем № заказа для след. напоминания
            $pay_actions[$now_pay]['summ'] = $order_sum;
            $pay_actions[$now_pay]['date'] = time();

            if ($now_pay == $installment_map_data['max_periods'] || $ahead == true) { // если платёж последний
                $next_pay = $next_sum = 0;
                Order::endInstallment($order, $installment_map_data, 1); // завершить оплаченную рассрочку
                $status = 2; // завершаем рассрочку
            } else {
                $next_pay = self::getNextPayDate($installment_data, $installment_map_data['create_date'],
                    $installment_map_data['second_pay'], $now_pay
                );
                $next_sum = self::getSum2Pay($pay_actions, $installment_map_data);
                $status = 1; // идут платежи
            }

            $second_pay = $now_pay == 1 ? $next_pay : null;
            $pay_actions = base64_encode(serialize($pay_actions));

            $sql = 'UPDATE '.PREFICS.'installment_map SET next_pay = :next_pay, pay_actions = :pay_actions,
                    status = :status, next_order = :next_order, notif = 0, ahead_id = 0, after_notif = 0,
                    create_date = :create_date'.($second_pay ? ', second_pay = :second_pay' : '').' WHERE id = :id';
            $result = $db->prepare($sql);
            $result->bindParam(':id', $installment_map_data['id'], PDO::PARAM_INT);
            $result->bindParam(':next_pay', $next_pay, PDO::PARAM_INT);
            $result->bindParam(':status', $status, PDO::PARAM_INT);
            $result->bindParam(':pay_actions', $pay_actions, PDO::PARAM_STR);
            $result->bindParam(':next_order', $next_order, PDO::PARAM_INT);
            $result->bindParam(':create_date', $update_date, PDO::PARAM_INT);
            if ($second_pay) {
                $result->bindParam(':second_pay', $second_pay, PDO::PARAM_INT);
            }
            $result = $result->execute();

            if ($result) {
                self::savePaymentMapData($order['installment_map_id'], $order['order_id'], $now_pay, $order_sum,
                    $next_pay, $next_sum, $status
                );

                return Order::getInstallmentMapData($installment_map_data['id']);
            }
        }

        exit('Error instalment update');
    }


    /**
     * @param $order
     * @param $installment_map_data
     * @param $count_pays
     * @param $now_pay
     * @param $user
     * @param $ahead
     * @return bool
     */
    public static function actions2Payments($order, $installment_map_data, $count_pays, $now_pay, $user, $ahead) {
        $order_items = Order::getOrderItems($order['order_id']);
        if (!$order_items) {
            return false;
        }

        $write = false;
        foreach($order_items as $item) {
            // Получить данные для действий
            $after_pay_actions = Product::getGroupAfterInstallPay($item['product_id']);
            $after_pay = unserialize(base64_decode($after_pay_actions[0]['actions']));
            $installment_id = $installment_map_data['installment_id'];

            if (!$ahead) { // записать группу после платежа, если обычный платёж
                if ($installment_map_data['status'] == 9) { // если была просрочка, вернуть обратно группы
                    // получить кол-во платежей и добавить группы
                    for($x = 1; $x <= $count_pays; $x++) {
                        if (isset($after_pay[$installment_id][$x])) {
                            $group_id = $after_pay[$installment_id][$x];
                            if ($user && $group_id != 0) {
                                $write = User::WriteUserGroup($user['user_id'], $group_id);
                            }
                        }
                    }
                }

                if (isset($after_pay[$installment_id][$now_pay])) {
                    $group_id = $after_pay[$installment_id][$now_pay];
                    if ($user && $group_id != 0) {
                        $write = User::WriteUserGroup($user['user_id'], $group_id);
                    }
                }
            } else { // записать группу после платежа, если досрочно
                if ($installment_map_data['status'] == 9) { // если была просрочка, вернуть обратно группы
                    // получить кол-во платежей и добавить группы
                    for($x = 1; $x <= $count_pays; $x++) {
                        if (isset($after_pay[$installment_id][$x])) {
                            $group_id = $after_pay[$installment_id][$x];
                            if ($user && $group_id != 0) {
                                $write = User::WriteUserGroup($user['user_id'], $group_id);
                            }
                        }
                    }
                }


                $ost_pay = $installment_map_data['max_periods'] - $count_pays;
                for ($x = $now_pay; $x <= $installment_map_data['max_periods']; $x++) {
                    if (isset($after_pay[$installment_id][$x])) {
                        $group_id = $after_pay[$installment_id][$x];
                        if ($user && $group_id != 0) {
                            $write = User::WriteUserGroup($user['user_id'], $group_id);
                        }
                    }
                }
            }
        }

        return $write;
    }


    /**
     * ПОЛУЧИТЬ КОЛИЧЕСТВО ПЛАТЕЖЕЙ
     * @param $pay_actions
     * @return int
     */
    public static function getCountPays($pay_actions) {
        if ($pay_actions) {
            $pay_actions = unserialize(base64_decode($pay_actions));

            return !empty($pay_actions) ? count($pay_actions) : 0;
        }

        return 0;
    }


    /**
     * ПОЛУЧИТЬ ДАТУ СЛЕДУЮЩЕГО ПЛАТЕЖА
     * @param $installment_data
     * @param $create_date
     * @param $count_pays
     * @param $second_pay
     * @return float|int|mixed
     */
    public static function getNextPayDate($installment_data, $create_date, $second_pay, $count_pays) {
        if ($count_pays == 1 && $installment_data['date_second_payment']) { // первый платеж с датой для второго платежа
            $next_pay = $installment_data['date_second_payment'];
        } else {
            $period_freq = $installment_data['period_freq'] * 86400; // Дни в секунды
            if ($second_pay && $count_pays > 1) {
                $next_pay = $second_pay + ($period_freq * ($count_pays - 1)); // + XX - 1 мес
            } else {
                $next_pay = $create_date + ($period_freq * $count_pays); // + XX мес
            }
        }

        return $next_pay;
    }


    /**
     * ПОЛУЧИТЬ ID ЗАКАЗА ПО ПЕРВОМУ ПЛАТЕЖУ
     * @param $installment_map_id
     * @return bool
     */
    public static function getFirstPayOrderId($installment_map_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT order_id FROM ".PREFICS."orders WHERE installment_map_id = $installment_map_id ORDER BY order_id ASC LIMIT 1");
        $data = $result->fetch();

        return !empty($data) ? $data[0] : false;
    }


    /**
     * ЗАПИСАТЬ В КАРТУ РАССРОЧЕК
     * @param $order_id
     * @param $total
     * @param $instalment_map_status
     * @param $installment_data
     * @param $email
     * @param $next_pay
     * @param $pay_actions
     * @param $client_data
     * @param $create_date
     * @return bool
     */
    public static function writeInstalmentMap($order_id, $total, $instalment_map_status, $installment_data, $email,
        $next_pay, $pay_actions, $client_data, $create_date)
    {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(id) FROM ".PREFICS."installment_map WHERE order_id = $order_id");
        $count = $result->fetch();
        if ($count[0] > 0) {
            return false;
        }

        $sql = 'INSERT INTO '.PREFICS.'installment_map (order_id, summ, start_summ, status, max_periods, email, next_pay, pay_actions, client_data, create_date, installment_id ) 
                VALUES (:order_id, :summ, :start_summ, :status, :max_periods, :email, :next_pay, :pay_actions, :client_data, :create_date, :installment_id)';

        $result = $db->prepare($sql);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result->bindParam(':summ', $total, PDO::PARAM_INT);
        $result->bindParam(':start_summ', $total, PDO::PARAM_INT);
        $result->bindParam(':status', $instalment_map_status, PDO::PARAM_INT);
        $result->bindParam(':max_periods', $installment_data['max_periods'], PDO::PARAM_INT);
        $result->bindParam(':email', $email, PDO::PARAM_STR);
        $result->bindParam(':next_pay', $next_pay, PDO::PARAM_INT);
        $result->bindParam(':pay_actions', $pay_actions, PDO::PARAM_STR);
        $result->bindParam(':client_data', $client_data, PDO::PARAM_STR);
        $result->bindParam(':create_date', $create_date, PDO::PARAM_INT);
        $result->bindParam(':installment_id', $installment_data['id'], PDO::PARAM_INT);
        $result = $result->execute();

        $query = "SELECT id FROM ".PREFICS."installment_map WHERE order_id = $order_id AND create_date = $create_date AND email = '$email' LIMIT 1";
        $result = $db->query($query);
        $data = $result->fetch(PDO::FETCH_ASSOC);

        if (!empty($data)) {
            self::addPaymentMapData($data['id'], $order_id, $next_pay, 1, $total, $instalment_map_status);
        }

        return !empty($data) ? $data['id'] : false;
    }


    /**
     * @param $installment_map_id
     * @param $order_id
     * @param $number_pay
     * @param $sum
     * @param $next_pay
     * @param $next_sum
     * @param $status
     */
    public static function savePaymentMapData($installment_map_id, $order_id, $number_pay, $sum, $next_pay, $next_sum, $status) {
        self::updPaymentMapData($installment_map_id, $order_id, $number_pay, $sum, $status);
        if ($status != 2) {
            self::addPaymentMapData($installment_map_id, 0, $next_pay, $number_pay + 1, $next_sum, $status);
        }
    }


    /**
     * @param $installment_map_id
     * @param $order_id
     * @param $pay_date
     * @param $number_pay
     * @param $sum
     * @param $status
     * @return bool
     */
    public static function addPaymentMapData($installment_map_id, $order_id, $pay_date, $number_pay, $sum, $status) {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'installment_map_payments (installment_map_id, order_id, pay_date, number_pay, sum, status) 
                VALUES (:installment_map_id, :order_id, :pay_date, :number_pay, :sum, :status)';

        $result = $db->prepare($sql);
        $result->bindParam(':installment_map_id', $installment_map_id, PDO::PARAM_INT);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result->bindParam(':pay_date', $pay_date, PDO::PARAM_INT);
        $result->bindParam(':number_pay', $number_pay, PDO::PARAM_INT);
        $result->bindParam(':sum', $sum, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * @param $installment_map_id
     * @param $order_id
     * @param $number_pay
     * @param $sum
     * @param $status
     */
    public static function updPaymentMapData($installment_map_id, $order_id, $number_pay, $sum, $status) {
        $db = Db::getConnection();
        $paid_date = time();
        $sql = 'UPDATE '.PREFICS.'installment_map_payments SET order_id = :order_id, paid_date = :paid_date,
                sum = :sum, status = :status WHERE installment_map_id = :installment_map_id AND number_pay = :number_pay';
        $result = $db->prepare($sql);
        $result->bindParam(':installment_map_id', $installment_map_id, PDO::PARAM_INT);
        $result->bindParam(':paid_date', $paid_date, PDO::PARAM_INT);
        $result->bindParam(':sum', $sum, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result->bindParam(':number_pay', $number_pay, PDO::PARAM_INT);

        $result->execute();
    }


    /**
     * СОХРАНИТЬ РАССРОЧКИ ДЛЯ ПРОДУКТА
     * @param $product_id
     * @param $installments
     * @return false|PDOStatement
     */
    public static function saveInstallments2Product($product_id, $installments) {
        $db = Db::getConnection();
        $res = self::delInstallments2Product($product_id);
        if ($res && $installments) {
            $values = 'VALUES ';
            foreach ($installments as $key => $installment_id) {
                $values .= ($key > 0 ? ',' : '')."($product_id, $installment_id)";
            }

            $res = $db->query('INSERT INTO '.PREFICS."installments_to_products (product_id, installment_id) $values");
        }

        return $res;
    }


    /**
     * ПОЛУЧИТЬ РАССРОЧКИ ДЛЯ ПРОДУКТА
     * @param $product_id
     * @param int $enable
     * @return array|bool
     */
    public static function getInstallments2Product($product_id, $enable = 1) {
        $db = Db::getConnection();
        $query = 'SELECT it.* FROM '.PREFICS.'installment_tune AS it
                  INNER JOIN installments_to_products AS itp ON itp.installment_id = it.id
                  WHERE it.product_id = :product_id  AND it.enable = :enable';
        $result = $db->prepare($query);
        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $result->bindParam(':enable', $enable, PDO::PARAM_INT);
        $result->execute();

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ РАССРОЧКИ ДЛЯ ПРОДУКТОВ
     * @param $products_ids
     * @param $with_prepayment
     * @param $total
     * @param int $enable
     * @return array
     */
    public static function getInstallments2Products($products_ids, $with_prepayment, $total, $enable = 1) {
        $products_ids_str = implode(',', $products_ids);
        $count_products = count($products_ids);
        $db = Db::getConnection();
        $query = 'SELECT it.* FROM '.PREFICS.'installments_to_products AS itp
                  LEFT JOIN '.PREFICS."installment_tune AS it ON it.id = itp.installment_id
                  WHERE it.enable = :enable AND it.prepayment = :prepayment AND it.minimal <= :total
                  AND itp.product_id IN ($products_ids_str)
                  GROUP BY itp.installment_id HAVING COUNT(itp.installment_id) >= $count_products ORDER BY it.sort ASC";
        $result = $db->prepare($query);
        $result->bindParam(':enable', $enable, PDO::PARAM_INT);
        $result->bindParam(':prepayment', $with_prepayment, PDO::PARAM_INT);
        $result->bindParam(':total', $total, PDO::PARAM_INT);
        $result->execute();

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        };

        return $data ? $data : false;
    }


    /**
     * ПОЛУЧИТЬ ID РАССРОЧЕК ДЛЯ ПРОДУКТА
     * @param $product_id
     * @return array|bool
     */
    public static function getInstallmentsIds2Product($product_id) {
        $db = Db::getConnection();
        $result = $db->prepare('SELECT installment_id FROM '.PREFICS.'installments_to_products WHERE product_id = :product_id');
        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $result->execute();

        $data = [];
        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row['installment_id'];
        }

        return !empty($data) ? $data : false;
    }


    /**
     * @param $product_id
     * @return bool
     */
    public static function delInstallments2Product($product_id) {
        $db = Db::getConnection();
        $result = $db->prepare('DELETE FROM '.PREFICS.'installments_to_products WHERE product_id = :product_id');
        $result->bindParam(':product_id', $product_id, PDO::PARAM_INT);

        return $result->execute();
    }
}
