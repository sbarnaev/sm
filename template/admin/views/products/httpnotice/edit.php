<div class="uk-modal-dialog uk-modal-dialog-3">
    <div class="userbox modal-userbox-3">
        <form method="POST" action="/admin/products/edithttpnotice/<?=$notice['notice_id'];?>">
            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
            <input type="hidden" name="product_id" value="<?=$product['product_id'];?>">
            <input type="hidden" name="product_type" value="<?=$product['type_id'];?>">
            
            <div class="admin_top admin_top-flex">
                <div>
                    <h3 class="traning-title mb-0"><?php echo $notice['notice_name'];?></h3>
                    <p class="mt-0">ID: <?php echo $notice['notice_id'];?></p>
                </div>
                
                <ul class="nav_button">
                    <li>
                        <input type="submit" name="edit" value="Сохранить" class="button save button-white font-bold">
                    </li>
                    <li class="nav_button__last">
                        <a class="button red-link uk-modal-close uk-close" href="#close">Закрыть</a>
                    </li>
                </ul>
            </div>

            <div class="admin_form">
                <h4 class="mt-30">Основное</h4>
                <div class="row-line">
                    <div class="col-1-2">
                        <p class="width-100"><label title="Название уведомления">Название:</label>
                            <input type="text" name="name" value="<?=$notice['notice_name'];?>" placeholder="Название" required="required">
                        </p>
                        
                        <p class="width-100">
                            <label>Тип отправки (POST/GET):</label>
                            <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="send_type" type="radio" value="1" <?php if ($notice['send_type'] == 1) echo ' checked';?>><span>POST</span></label>
                                <label class="custom-radio"><input name="send_type" type="radio" value="2" <?php if ($notice['send_type'] == 2) echo ' checked';?>><span>GET</span></label>
                            </span>
                        </p>

                        <p class="width-100">
                            <label class="custom-chekbox-wrap" for="is_send_utm_edit">
                                <input type="checkbox" id="is_send_utm_edit" name="is_send_utm" value="1"<?php if($notice['is_send_utm']) echo ' checked="checked"'; ?>>
                                <span class="custom-chekbox"></span>Передавать utm-метки
                            </label>
                        </p>
                    </div>
                    
                    <div class="col-1-2">
                        <p class="width-100"><label title="Адрес сайта, куда будут отправляться данные">Адрес сайта:</label>
                            <input type="text" name="url" value="<?=$notice['notice_url'];?>" placeholder="Адрес сайта" required="required">
                        </p>
    
                        <p class="width-100">
                            <label>Момент отправки:</label>
                            <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="send_time_type" type="radio" value="1"<?php if($notice['send_time_type'] == 1) echo ' checked="checked"';?>><span>Перед оплатой заказа</span></label>
                                <label class="custom-radio"><input name="send_time_type" type="radio" value="2"<?php if($notice['send_time_type'] == 2) echo ' checked="checked"';?>><span>После оплаты заказа</span></label>
                            </span>
                        </p>
                    </div>
                </div>
                
                <h4 class="mt-30">Имена переменных</h4>
                <div class="row-line">
                    <div class="col-1-2">
                        <p class="width-100"><label title="Имя клиента">Имя клиента:</label>
                            <input type="text" name="vars[name]" value="<?=$notice_vars['name'];?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Телефон клиента">Телефон клиента:</label>
                            <input type="text" name="vars[phone]" value="<?=$notice_vars['phone'];?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Город проживания клиента">Город проживания клиента:</label>
                            <input type="text" name="vars[city]" value="<?=$notice_vars['city'];?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Индекс клиента">Индекс клиента:</label>
                            <input type="text" name="vars[index]" value="<?=$notice_vars['index'];?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Ссылка на профиль клиента в ВК">Ссылка на профиль клиента в ВК:</label>
                            <input type="text" name="vars[vk_url]" value="<?=$notice_vars['vk_url'];?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="ID пользователя">ID пользователя:</label>
                            <input type="text" name="vars[user_id]" value="<?=isset($notice_vars['user_id']) ? $notice_vars['user_id'] : '';?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Дата заказа">Дата заказа:</label>
                            <input type="text" name="vars[order_date]" value="<?=$notice_vars['order_date'];?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Статус заказа">Статус заказа:</label>
                            <input type="text" name="vars[order_status]" value="<?=isset($notice_vars['order_status']) ? $notice_vars['order_status'] : '';?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="ID продукта">ID продукта:</label>
                            <input type="text" name="vars[product_id]" value="<?=isset($notice_vars['product_id']) ? $notice_vars['product_id'] : '';?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Ссылка на скачивание продукта">Ссылка на скачивание продукта:</label>
                            <input type="text" name="vars[product_link]" value="<?=isset($notice_vars['product_link']) ? $notice_vars['product_link'] :'';?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Цена продукта">Цена продукта:</label>
                            <input type="text" name="vars[product_price]" value="<?=isset($notice_vars['product_price']) ? $notice_vars['product_price'] : '';?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Ссылка на обложку">Ссылка на обложку:</label>
                            <input type="text" name="vars[product_cover]" value="<?=isset($notice_vars['product_cover']) ? $notice_vars['product_cover'] : '';?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="ClientID YM">ClientID YM:</label>
                            <input type="text" name="vars[userId_YM]" value="<?=isset($notice_vars['userId_YM']) ? $notice_vars['userId_YM'] : '';?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Roistat visit">Roistat visit:</label>
                            <input type="text" name="vars[roistat_visitor]" value="<?=isset($notice_vars['roistat_visitor']) ? $notice_vars['roistat_visitor'] : '';?>" placeholder="">
                        </p>
                    </div>

                    <div class="col-1-2">
                        <p class="width-100"><label title="Фамилия клиента">Фамилия клиента:</label>
                            <input type="text" name="vars[surname]" value="<?=isset($notice_vars['surname']) ? $notice_vars['surname'] : '';?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Email клиента">Email клиента:</label>
                            <input type="text" name="vars[email]" value="<?=$notice_vars['email'];?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Адрес клиента">Адрес клиента:</label>
                            <input type="text" name="vars[addres]" value="<?=$notice_vars['addres'];?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Комментарий клиента к заказу">Комментарий клиента:</label>
                            <input type="text" name="vars[comment]" value="<?=$notice_vars['comment'];?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="ID заказа">ID заказа:</label>
                            <input type="text" name="vars[order_id]" value="<?=isset($notice_vars['order_id']) ? $notice_vars['order_id'] : '';?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Состав заказа">Состав заказа:</label>
                            <input type="text" name="vars[order_products]" value="<?=isset($notice_vars['order_products']) ? $notice_vars['order_products'] : '';?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Сумма заказа">Сумма заказа:</label>
                            <input type="text" name="vars[summ]" value="<?=$notice_vars['summ'];?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Название продукта">Название продукта:</label>
                            <input type="text" name="vars[product_name]" value="<?=isset($notice_vars['product_name']) ? $notice_vars['product_name'] : '';?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Категория продукта">Категория продукта:</label>
                            <input type="text" name="vars[product_category]" value="<?=isset($notice_vars['product_category']) ? $notice_vars['product_category'] : '';?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="Секретный ключ API">Секретный ключ API:</label>
                            <input type="text" name="vars[secret]" value="<?=$notice_vars['secret'];?>" placeholder="">
                        </p>

                        <p class="width-100"><label title="ClientID GA">ClientID GA:</label>
                            <input type="text" name="vars[userId_GA]" value="<?=isset($notice_vars['userId_GA']) ? $notice_vars['userId_GA'] : '';?>" placeholder="">
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>