<?php defined('BILLINGMASTER') or die;


class Cart {

    /**
     * ДОБАВИТЬ ЗАКАЗ В БД
     * @param $name
     * @param $email
     * @param $phone
     * @param $index
     * @param $city
     * @param $address
     * @param $comment
     * @param $param
     * @param $partner_id
     * @param $date
     * @param $sale_id
     * @param $status
     * @param $type_id
     * @param $ip
     * @param $products
     * @param $utm
     * @return bool|mixed
     */
    public static function addCartOrder($name, $email, $phone, $index, $city, $address, $comment, $param, $partner_id,
                                        $date, $sale_id, $status, $type_id, $ip, $products, $utm)
    {
        $setting = System::getSetting();
        $db = Db::getConnection();
        // Получить ID рекламного канала
        $arr1 = explode(";", $param);
        $channel_id = $arr1[2];

        $order_info = [
            'userId_YM' => isset($_COOKIE['_ym_uid']) ? $_COOKIE['_ym_uid'] : null,
            'userId_GA' => isset($_COOKIE['_gid']) ? $_COOKIE['_gid'] : null,
            'roistat_visitor' => isset($_COOKIE['roistat_visit']) ? $_COOKIE['roistat_visit'] : null,
            'userId_FB' => isset($_COOKIE['_fbp']) ? $_COOKIE['_fbp'] : null,
            'userId_FBс' => isset($_COOKIE['_fbс']) ? $_COOKIE['_fbс'] : null,
        ];
        $order_info = array_filter($order_info, 'strlen') ? base64_encode(serialize($order_info)) : null;

        $sql = 'INSERT INTO '.PREFICS.'orders (order_date, client_name, client_email, client_phone, client_city, client_address,
                    client_index, client_comment, sale_id, partner_id, status, visit_param, channel_id, order_info, utm, ip) 
                VALUES (:order_date, :client_name, :client_email, :client_phone, :client_city, :client_address,
                    :client_index, :client_comment, :sale_id, :partner_id, :status, :visit_param, :channel_id, :order_info, :utm, :ip)';
        
        $result = $db->prepare($sql);
        $result->bindParam(':order_date', $date, PDO::PARAM_INT);
        $result->bindParam(':client_name', $name, PDO::PARAM_STR);
        $result->bindParam(':client_email', $email, PDO::PARAM_STR);
        
        $result->bindParam(':client_phone', $phone, PDO::PARAM_STR);
        $result->bindParam(':client_city', $city, PDO::PARAM_STR);
        $result->bindParam(':client_address', $address, PDO::PARAM_STR);
        $result->bindParam(':client_index', $index, PDO::PARAM_STR);
        $result->bindParam(':client_comment', $comment, PDO::PARAM_STR);
        $result->bindParam(':sale_id', $sale_id, PDO::PARAM_INT);
        $result->bindParam(':partner_id', $partner_id, PDO::PARAM_INT);
        $result->bindParam(':status', $status, PDO::PARAM_INT);
        $result->bindParam(':channel_id', $channel_id, PDO::PARAM_INT);
        $result->bindParam(':visit_param', $param, PDO::PARAM_STR);
        $result->bindParam(':order_info', $order_info, PDO::PARAM_STR);
        $result->bindParam(':utm', $utm, PDO::PARAM_STR);
        $result->bindParam(':ip', $ip, PDO::PARAM_STR);
        $result->execute();
        
        // Получить ID созданного заказа
        $result = $db->query("SELECT * FROM ".PREFICS."orders WHERE order_date = $date");
        $order_data = $result->fetch(PDO::FETCH_ASSOC);
        
        if (!empty($order_data)) {
            self::addOrderItems($order_data, $products, $setting);
        }

        return !empty($order_data) ? $order_data : false;
    }


    /**
     * ДОБАВИТЬ СОСТАВ ЗАКАЗА
     * @param $order_data
     * @param $products
     * @param $setting
     */
    public static function addOrderItems($order_data, $products, $setting) {
        $db = Db::getConnection();
        $number = 1;

        foreach($products as $product) {
            $price = Price::getPriceinCatalog($product['product_id']);
            $split_var = null;

            // КОРРЕКТИРОВКА СПЛИТ ТЕСТА для продуктов комплектаций
            $cookie_split = $setting['cookie'].'_split'; // Сформировали имя куки

            if (isset($_COOKIE["$cookie_split"]) && $product['base_id'] != 0) { // если продукт - это комплектация основного
                $cookie_arr = json_decode($_COOKIE["$cookie_split"], true);
                $base_id = $product['base_id']; // Получить ID базового
                $split_var = array_key_exists($base_id, $cookie_arr) ? intval($cookie_arr["$base_id"]) : null; // вариант описания
            }

            $cast = 'cart'; // Основной продукт с которого начался заказ
            $sql = 'INSERT INTO '.PREFICS.'order_items (order_id, product_id, type_id, number, price, status, cast, product_name, split_var ) 
                        VALUES (:order_id, :product_id, :type_id, :number, :price, 0, :cast, :product_name, :split_var)';

            $result = $db->prepare($sql);
            $result->bindParam(':order_id', $order_data['order_id'], PDO::PARAM_INT);
            $result->bindParam(':product_id', $product['product_id'], PDO::PARAM_INT);
            $result->bindParam(':type_id', $product['type_id'], PDO::PARAM_INT);
            $result->bindParam(':number', $number, PDO::PARAM_INT);
            $result->bindParam(':price', $price['real_price'], PDO::PARAM_INT);
            $result->bindParam(':cast', $cast, PDO::PARAM_STR);
            $result->bindParam(':product_name', $product['product_name'], PDO::PARAM_STR);
            $result->bindParam(':split_var', $split_var, PDO::PARAM_INT);
            $result->execute();

            $number++;
        }
    }
    
    // ДОБАВЛЕНИЕ ПРОДУКТА В КОРЗИНУ
    public static function AddProduct($id)
    {
        $productsInCart = array();
        
        // Если в корзине уже есть товары
        if (isset($_SESSION['cart'])) {
            // То запишем товары в массив вида [id] => кол-во
            $productsInCart = $_SESSION['cart'];
        }
        
        // Если товар в корзине, но был добавлен ещё раз - увеличиваем кол-во
        if (array_key_exists($id, $productsInCart)) {
            $productsInCart[$id] ++;
        } else {
            // Добавляем новый товар в корзину
            $productsInCart[$id] = 1;
        }
        
        $_SESSION['cart'] = $productsInCart;
        
        return self::countItems();
    } 
    
    
    
    
    // ПОДСЧЁТ ПРОДУКТОВ В КОРЗИНЕ
    public static function countItems()
    {
        if(isset($_SESSION['cart'])) return count($_SESSION['cart']);
        else return 0;
    }


    /**
     * ЕСТЬ ЛИ ПРОДУКТЫ В КОРЗИНЕ
     * @return bool|mixed
     */
    public static function getProducts()
    {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : false;
    }
}