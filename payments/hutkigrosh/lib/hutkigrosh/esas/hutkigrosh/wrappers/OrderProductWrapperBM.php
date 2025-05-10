<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 14.03.2018
 * Time: 17:08
 */

namespace esas\hutkigrosh\wrappers;

use esas\hutkigrosh\lang\TranslatorBM;

class OrderProductWrapperBM extends OrderProductWrapper
{
    private $basketItem;
    /**
     * OrderProductWrapperJoomshopping constructor.
     * @param $product
     */
    public function __construct($product)
    {
        parent::__construct(new TranslatorBM());
        $this->basketItem = $product;
    }

    /**
     * Артикул товара
     * @return string
     */
    public function getInvId()
    {
        return $this->basketItem['order_item_id'];
    }

    /**
     * Название или краткое описание товара
     * @return string
     */
    public function getName()
    {
        return $this->basketItem['product_name'];
    }

    /**
     * Количество товара в корзине
     * @return mixed
     */
    public function getCount()
    {
        return 1;
    }

    /**
     * Цена за единицу товара
     * @return mixed
     */
    public function getUnitPrice()
    {
        return $this->basketItem['price'];
    }
}