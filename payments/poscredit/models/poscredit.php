<?php defined('BILLINGMASTER') or die;

class PosCredit {

    const STATUSES_TO_PROCESS = [0, 1, 3]; // 0 - новая заявка, 1 - в обработке, 6 - договор авторизован
    const STATUSES = [
        0 => 'новая заявка',
        1 => '<nobr>в обработке</nobr>',
        2 => '<nobr>в кредите</nobr> отказано',
        3 => 'кредит предоставлен',
        4 => 'ошибочный ввод',
        5 => 'отказ клиента',
        6 => 'договор авторизован',
        7 => 'договор подписан',
    ];
    const CLIENT_STATUSES = [
        1 => 'Клиент ушел не оформив заявку до конца',
        2 => 'Клиенту отказали в кредите предложенные банки',
        3 => 'Клиент сам отказался от кредита после получения решения',
        4 => 'Заявка ушла в обработку кредитным инспекторам или перешла на ручной ввод',
        5 => 'Получено одобрение по заявке, но клиент не подтвердил выбор банка',
        6 => 'Клиент завершил оформление заявки и подтвердил выбор банка',
    ];


    /**
     * @param $order_id
     * @param $profile_id
     * @param $client_status
     * @return bool
     */
    public function addOrder($order_id, $profile_id, $client_status = null) {
        $db = Db::getConnection();
        $query = 'INSERT INTO '.PREFICS.'poscredit_orders (order_id, profile_id, client_status, status, bank) 
                  VALUES (:order_id, :profile_id, :client_status, 0, "")';
        $result = $db->prepare($query);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
        $result->bindParam(':client_status', $client_status, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * @param $order_id
     * @return bool
     */
    public function getOrderWithoutProfile($order_id) {
        $db = Db::getConnection();
        $sql = 'SELECT * FROM '.PREFICS.'poscredit_orders WHERE order_id = :order_id 
                AND profile_id IS NULL ORDER BY id DESC LIMIT 1';
        $result = $db->prepare($sql);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    public function countOrders($order_id, $profile_id) {
        $db = Db::getConnection();
        $sql = 'SELECT COUNT(id) FROM '.PREFICS.'poscredit_orders WHERE order_id = :order_id AND profile_id = :profile_id';
        $result = $db->prepare($sql);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
        $result->execute();
        $data = $result->fetch();

        return $data[0];
    }


    /**
     * СОХРАНИТЬ СТАТУС КЛИЕНТА ПО ЗАЯВКЕ
     * @param $order_id
     * @param $client_status
     * @return bool
     */
    public function saveStatusClient($order_id, $client_status) {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'poscredit_orders SET client_status = :client_status
                WHERE order_id = :order_id ORDER BY id DESC LIMIT 1';
        $result = $db->prepare($sql);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result->bindParam(':client_status', $client_status, PDO::PARAM_INT);

        return $result->execute();
    }


    /**
     * @param $id
     * @param $profile_id
     * @param int $status возможные статусы:
     * 0 - новая заявка;
     * 1 - в обработке;
     * 2 - в кредите отказано;
     * 3 - кредит предоставлен;
     * 4 - ошибочный ввод;
     * 5 - отказ клиента;
     * 6 - договор авторизован;
     * 7 - договор подписан клиентом;
     * @param string $bank
     * @return bool
     */
    public function updData($id, $profile_id, $status = 0, $bank = '') {
        $db = Db::getConnection();
        $sql = 'UPDATE '.PREFICS.'poscredit_orders SET profile_id = :profile_id, status = :status, bank = :bank WHERE id = :id';
        $result = $db->prepare($sql);
        $result->bindParam(':id', $id, PDO::PARAM_INT);
        $result->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':bank', $bank, PDO::PARAM_STR);

        return $result->execute();
    }

    /**
     * @param $order_id
     * @param null $profile_id
     * @return bool
     */
    public function getStatus($order_id, $profile_id = null) {
        $db = Db::getConnection();
        $sql = 'SELECT status FROM '.PREFICS.'poscredit_orders WHERE order_id = :order_id';
        $sql .= $profile_id ? ' AND profile_id = :profile_id' : ' ORDER BY id DESC LIMIT 1';
        $result = $db->prepare($sql);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        if ($profile_id) {
            $result->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
        }

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data['status'] : false;
    }


    /**
     * @param $status
     * @return mixed
     */
    public function getStatusText($status) {
        $statuses = self::STATUSES;

        return isset($statuses[$status]) ? $statuses[$status] : 'не определен';
    }


    /**
     * @param $client_status
     * @return mixed|string
     */
    public static function getClientStatusText($client_status) {
        $client_statuses = self::CLIENT_STATUSES;

        return isset($client_statuses[$client_status]) ? $client_statuses[$client_status] : 'не определен';
    }


    /**
     * @param $order_id
     * @param $profile_id
     * @return bool|mixed
     */
    public function getOrder($order_id, $profile_id) {
        $db = Db::getConnection();
        $sql = 'SELECT * FROM '.PREFICS.'poscredit_orders WHERE order_id = :order_id';
        $sql .= $profile_id ? ' AND profile_id = :profile_id' : ' ORDER BY id DESC LIMIT 1';
        $result = $db->prepare($sql);
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        if ($profile_id) {
            $result->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
        }

        $result->execute();
        $data = $result->fetch(PDO::FETCH_ASSOC);

        return !empty($data) ? $data : false;
    }


    /**
     * @param $order_id
     * @return bool
     */
    public static function orderExists($order_id) {
        $db = Db::getConnection();
        $result = $db->prepare('SELECT COUNT(id) FROM '.PREFICS.'poscredit_orders WHERE order_id = :order_id');
        $result->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $result->execute();
        $count = $result->fetch();

        return $count[0] ? true : false;
    }


    /**
     * @param $order_id
     * @return array|bool
     */
    public function getOrders($order_id) {
        $db = Db::getConnection();
        $result = $db->query('SELECT * FROM '.PREFICS."poscredit_orders WHERE order_id = $order_id");
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }



    /**
     * @return array|bool
     */
    public function getOrders2SaveStatus() {
        $db = Db::getConnection();
        $str_statuses = implode(',', self::STATUSES_TO_PROCESS);
        $result = $db->query('SELECT * FROM '.PREFICS."poscredit_orders WHERE status IN ($str_statuses)");
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        return !empty($data) ? $data : false;
    }


    /**
     * @param $e
     */
    public function writeError($e) {
        $code = $e->code;
        $desc = $e->description;
        $error = date('d.m.Y H:i:s', time()) . " Code: $code,  Description: $desc";
        file_put_contents(__DIR__ . '/../log/errors.log', PHP_EOL . $error, FILE_APPEND);
    }
}