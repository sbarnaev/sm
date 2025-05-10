<?php defined('BILLINGMASTER') or die;?>

<div id="modal_add_product" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-3 p-0">
        <div class="userbox modal-userbox-3">
            <form action="/admin/orders/addproduct/<?=$order['order_id'];?>" method="POST" enctype="multipart/form-data">
                <div class="admin_top admin_top-flex">
                    <div class="admin_top-inner">
                        <div>
                            <h3 class="traning-title mb-0">Добавить товар к заказу</h3>
                        </div>
                    </div>

                    <ul class="nav_button">
                        <li><input type="submit" name="add_product" value="Добавить" class="button save button-white font-bold"></li>
                        <li class="nav_button__last">
                            <a class="button uk-modal-close uk-close modal-nav_button__close" href="#close"><i class="icon-close"></i></a>
                        </li>
                    </ul>
                </div>

                <div class="admin_form">
                    <div class="row-line">
                        <div class="col-1-2">
                            <p class="width-100"><label>Продукт:</label>
                                <select name="product_id" id="add_product2order">
                                    <?php $product_list = Product::getProductListOnlySelect();
                                    if ($product_list):
                                        foreach ($product_list as $product):?>
                                            <option value="<?=$product['product_id'];?>" data-price="<?=(int)$product['price'];?>"><?=$product['product_name'];?></option>
                                            <?php if($product['service_name']):?>
                                                <option disabled="disabled" class="service-name">(<?=$product['service_name'];?>)</option>
                                            <?php endif;
                                        endforeach;
                                    endif;?>
                                </select>
                            </p>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100"><label>Стоимость:</label>
                                <input type="text" name="product_price" id="edit_price2order_product" value="<?=$product_list ? (int)$product_list[0]['price'] : '';?>">
                            </p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#add_product2order').change(function() {
          let price = $(this).find('option:selected').data('price');
          $('#edit_price2order_product').val(price);
        });
    });
</script>