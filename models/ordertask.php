<?php defined('BILLINGMASTER') or die;

class OrderTask {

    const STAGE_ACC_STAT = 1; // выписка счета
    const STAGE_INSTALLMENT = 2; // рассрочка (создание)
    const STAGE_INSTALLMENT_ACC_STAT = 3; // выписка счета для рассрочки (по крону)
    const STAGE_UPSELL = 4; // апселлы
    const STAGE_ACC_PAY = 5; // оплата заказа

    const TASK_STATUS_NOT_PROCESSED = 0;
    const TASK_STATUS_TAKE2PROCESSING = 1;
    const TASK_STATUS_PROCESSED = 2;
    const TASK_STATUS_ERROR = 3;

    /**
     * @param int $status
     * @return array|bool
     */
    public static function getTasks($status = 0) {
        $db = Db::getConnection();
        $result = $db->query("SELECT * FROM ".PREFICS."order_tasks WHERE status = $status ORDER BY order_stage, task_id ASC");

        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * @param $order_id
     * @param null $order_stage
     * @return bool
     */
    public static function getTaskByOrderId($order_id, $order_stage = null) {
        $db = Db::getConnection();
        $query = "SELECT * FROM ".PREFICS.'order_tasks WHERE order_id = :order_id';
        $query .= $order_stage ? ' AND order_stage = :order_stage' : '';

        $result = $db->prepare($query);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        if ($order_stage) {
            $result->bindParam(':order_stage', $order_stage, PDO::PARAM_INT);
        }

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * @param $order_id
     * @param $order_stage
     * @param null $installment_id
     * @return bool
     */
    public static function addTask($order_id, $order_stage, $installment_id = null) {
        $db = Db::getConnection();
        $sql = 'INSERT INTO '.PREFICS.'order_tasks (order_id, order_stage, installment_id) 
                VALUES (:order_id, :order_stage, :installment_id)';

        $result = $db->prepare($sql);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result->bindParam(':order_stage', $order_stage, PDO::PARAM_INT);
        $result->bindParam(':installment_id', $installment_id, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * @param $task_id
     * @param $status
     * @return bool
     */
    public static function updTaskStatus($task_id, $status) {
        $date = time();
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'order_tasks SET status = :status, processed_date = :processed_date WHERE task_id = :task_id';

        $result = $db->prepare($sql);
        $result->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':processed_date', $date, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * @param $task_id
     * @param $products_ids
     * @param $status
     * @return bool
     */
    public static function updTaskData($task_id, $products_ids, $status) {
        $products_ids = $products_ids ? implode(',', $products_ids) : null;
        $date = time();
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'order_tasks SET products = :products, status = :status,
                processed_date = :processed_date WHERE task_id = :task_id';

        $result = $db->prepare($sql);
        $result->bindParam(':task_id', $task_id, PDO::PARAM_INT);
        $result->bindParam(':products', $products_ids, PDO::PARAM_STR);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':processed_date', $date, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * @param $tasks
     * @param $status
     */
    public static function updTasksStatus($tasks, $status) {
        foreach ($tasks as $task) {
            self::updTaskStatus($task['task_id'], $status);
        }
    }


    /**
     * ОБРАБОТКА ЗАДАЧ
     */
    public static function taskProcessing() {
        $settings = System::getSetting();
        $tasks = self::getTasks();

        if ($tasks) {
            self::updTasksStatus($tasks, self::TASK_STATUS_TAKE2PROCESSING);

            foreach ($tasks as $task) {
                $task_status = self::TASK_STATUS_PROCESSED;

                $order = Order::getOrder($task['order_id']);
                $order_items = $order ? Order::getOrderItems($task['order_id']) : null;

                if (!$order || !$order_items) {
                    self::updTaskStatus($task['task_id'], self::TASK_STATUS_ERROR);
                    continue;
                }

                $total_sum = Order::getOrderTotalSum($order['order_id']);
                $products_ids = $products_srv_names = [];
                $products_names = array_column($order_items, 'product_name');

                if (in_array($task['order_stage'], [self::STAGE_ACC_STAT, self::STAGE_INSTALLMENT_ACC_STAT, self::STAGE_ACC_PAY])) { // выписка счета/выписка счета для рассрочки/оплата счета
                    $order_info = unserialize(base64_decode($order['order_info']));
                    $partners_payouts = isset($order_info['aff_summ']) ? $order_info['aff_summ'] : 0;

                    $client = User::getUserDataByEmail($order['client_email'], null);
                    $client_id = $client ? $client['user_id'] : '';

                    foreach ($order_items as $order_item) {
                        $product = Product::getProductData($order_item['product_id']);
                        if (!$product) {
                            continue;
                        }

                        $products_ids[] = $product['product_id'];
                        $products_srv_names[] = $product['service_name'];

                        // ИНТЕГРАЦИЯ ACYMAILING - подписка на рассылку после создания заказа
                        if (!empty($product['acymailing'])) {
                            $acy_key = in_array($task['order_stage'], [self::STAGE_ACC_STAT, self::STAGE_INSTALLMENT_ACC_STAT]) ? 'acy_id' : 'acy_id2';
                            AcyMailing::sendData($product['acymailing'], $acy_key, $order['client_name'],
                                $order['client_email'], $product['product_name'], $order['order_date']
                            );
                        }

                        // ОТПРАВКА HTTP УВЕДОМЛЕНИЙ
                        $prod_notice_key = in_array($task['order_stage'], [self::STAGE_ACC_STAT, self::STAGE_INSTALLMENT_ACC_STAT])
                            ? ProductHttpNotice::EVENT_TYPE_ACC_STAT : ProductHttpNotice::EVENT_TYPE_ACC_PAY;
                        ProductHttpNotice::sendNotice($order, $product, $products_names, $total_sum, $settings, $prod_notice_key);
                    }

                    if ($products_ids) {
                        // РАСШИРЕНИЕ ExpertSender
                        if (System::CheckExtensension('expertsender', 1)) {
                            $exps_key = in_array($task['order_stage'], [self::STAGE_ACC_STAT, self::STAGE_INSTALLMENT_ACC_STAT])
                                ? ExpertSender::EVENT_TYPE_ACC_STAT : ExpertSender::EVENT_TYPE_ACC_PAY;
                            ExpertSender::addSubscriber($exps_key, $products_ids, $order['client_email'], $order['client_name']);
                        }

                        // РАСШИРЕНИЕ AmoCRM
                        if (System::CheckExtensension('amocrm', 1) && $task['order_stage'] != self::STAGE_INSTALLMENT_ACC_STAT) {
                            if ($task['order_stage'] == self::STAGE_ACC_STAT) {
                                $event_type = $event_type = $total_sum > 0 ? AmoCRM::EVENT_TYPE_ACC_STAT : AmoCRM::EVENT_TYPE_GIVE_FP;
                                $statistics_data = Order::getStatisticsData($order);
                                AmoCRM::addLead($event_type, $order, $total_sum, $products_names, $statistics_data);
                            } elseif($task['order_stage'] == self::STAGE_ACC_PAY) { // оплата заказа
                                $installment_map_data = $order['installment_map_id'] ? Order::getInstallmentMapData($order['installment_map_id']) : null;
                                $installment_data = $installment_map_data['installment_id'] ? Product::getInstallmentData($installment_map_data['installment_id']) : null;
                                AmoCRM::processes2PayOrder($order, $total_sum, $products_names, $installment_data,
                                    $installment_map_data, $partners_payouts
                                );
                            }
                        }

                        // РАСШИРЕНИЕ GetFunnels
                        if (System::CheckExtensension('getfunnels', 1)) {
                            $gf_key = in_array($task['order_stage'], [self::STAGE_ACC_STAT, self::STAGE_INSTALLMENT_ACC_STAT])
                                ? 'expected' : 'accepted';
                            GetFunnels::addDeal($order['client_email'], $order['client_name'], $order['client_phone'],
                                $order['order_id'], $total_sum, $products_names, $products_srv_names, $gf_key,
                                $order['partner_id']
                            );
                        }
                    } else {
                        $task_status = self::TASK_STATUS_ERROR;
                    }

                    // PostBacks
                    if ($order['partner_id']) {
                        $pb_key = in_array($task['order_stage'], [self::STAGE_ACC_STAT, self::STAGE_INSTALLMENT_ACC_STAT])
                            ? PostBacks::ACT_TYPE_CREATE_ORDER : PostBacks::ACT_TYPE_PAY_ORDER;
                        PostBacks::sendData($pb_key, $order['partner_id'], $order['client_name'], $order['client_email'],
                            $order['client_phone'], $client_id, $order['order_id'], $order['order_date'], $total_sum
                        );
                    }

                    // РАСШИРЕНИЕ FacebookAPI
                    if (System::CheckExtensension('facebookapi', 1)) {
                        Facebook::eventsend2pixel($order, $total_sum, $order['partner_id']);
                    }
                } elseif($task['order_stage'] == self::STAGE_INSTALLMENT) { // рассрочка
                    $installment_data = $task['installment_id'] ? Product::getInstallmentData($task['installment_id']) : null;
                    if ($installment_data) {
                        // РАСШИРЕНИЕ AmoCRM
                        if (System::CheckExtensension('amocrm', 1)) {
                            $order['installment_title'] = $installment_data['title'];
                            $installment_total = $total_sum + $installment_data['increase'];
                            AmoCRM::updLead(AmoCRM::EVENT_TYPE_INSTLMNT_SELECTED, $order, $installment_total);
                        }
                    } else {
                        $task_status = self::TASK_STATUS_ERROR;
                    }
                } elseif($task['order_stage'] == self::STAGE_UPSELL) { // апселлы
                    $acc_stat_task = self::getTaskByOrderId($order['order_id'], self::STAGE_ACC_STAT);
                    $acc_stat_products_ids = explode(',', $acc_stat_task['products']);

                    if (count($products_ids) > count($acc_stat_products_ids)) {
                        // РАСШИРЕНИЕ AmoCRM
                        if (System::CheckExtensension('amocrm', 1)) {
                            $event_type = $total_sum > 0 ? AmoCRM::EVENT_TYPE_ACC_STAT : AmoCRM::EVENT_TYPE_GIVE_FP;
                            $prod_names = array_column($order_items, 'product_name');
                            AmoCRM::updLead($event_type, $order, $total_sum, null, null, $prod_names);
                        }
                    }
                }

                self::updTaskData($task['task_id'], $products_ids, $task_status);
            }
        }
    }


    /**
     * ПРОВЕРКА ОШИБОК У ТАСКОВ ПО ЗАКАЗУ
     * @param $order_id
     * @return bool
     */
    public static function checkErrors2Order($order_id) {
        $db = Db::getConnection();
        $result = $db->query("SELECT COUNT(order_id) FROM ".PREFICS."order_tasks WHERE status = 3 AND order_id = $order_id");
        $data = $result->fetch();

        return $data[0] ? true : false;
    }
}