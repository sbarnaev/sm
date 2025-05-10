<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php');
$script_url = $setting['script_url'];
$planes = Member::getPlanes();
if ($product['manager_letter'] != null) {
    $manager_letter = unserialize(base64_decode($product['manager_letter']));
}?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Редактировать продукт : ID <?=$product['product_id'];?></h1>
        <div class="logout">
            <a href="<?=$script_url;?>" target="_blank">Перейти на сайт</a>
            <a href="<?=$script_url;?>/admin/logout" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/products/">Продукты</a></li>
        <li>Редактировать продукт</li>
    </ul>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Сохранено!</div>
    <?php endif;?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="traning-top">
            <div class="admin_top-inner">
                <div>
                    <img src="https://lk.school-master.ru/str/icon.php">
                </div>

                <div>
                    <h3 class="traning-title traning-title__with-icon mb-0"><?=$product['product_name'];?> <a target="_blank" title="Страница заказа" href="/buy/<?=$product['product_id'];?>"><i class="icon-exit-top-right" style="font-size: 16px;"></i></a></h3>
                    <p class="mt-0">ID: <?=$product['product_id'];?></p>
                </div>
            </div>

            <ul class="nav_button">
                <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?=$script_url;?>/admin/products/">Закрыть</a></li>
            </ul>
        </div>

        <div class="tabs">
            <ul>
                <li>Основное</li>
                <li>События</li>
                <?php if($partnership):?>
                    <li>Начисления</li>
                <?php endif;?>
                <li>Рассрочка</li>
                <li>Допродажи</li>
                <li>Прочее</li>
                <li>Корзина</li>
            </ul>

            <div class="admin_form">
                <!-- 1 вкладка -->
                <div>
                    <?php $prod_types = Product::getTypes();?>
                    <h4 class="h4-border">Основное <span style="font-weight: normal;">(<?=mb_strtolower($prod_types[$product['type_id']]);?>)</span></h4>
                    <div class="row-line">
                        <div class="col-1-2">
                            <p><label>Название:</label>
                                <input type="text" name="name" placeholder="Название продукта" value="<?=$product['product_name'];?>" required="required">
                            </p>

                            <p><label>Служебное название:</label>
                                <input type="text" name="service_name" placeholder="Служебное название продукта" value="<?=$product['service_name'];?>">
                            </p>

                            <div class="width-100"><label>Тип продукта:</label>
                                <div class="select-wrap">
                                    <select name="product_type">
                                        <?php foreach($prod_types as $prod_type => $prod_name):
                                            if ($prod_type == 3 && !System::CheckExtensension('membership', 1)) {
                                                continue;
                                            }?>
                                            <option value="<?=$prod_type;?>"<?= $prod_type == $product['type_id'] ? 'selected="selected"' : '';?>><?=$prod_name;?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100"><label>Категория:</label>
                                <div class="select-wrap">
                                    <select name="cat_id">
                                        <option value="">Выберите</option>
                                        <?php $cat_list = Product::getAllCatList();
                                        foreach($cat_list as $cat):?>
                                            <option value="<?=$cat['cat_id'];?>"<?php if($cat['cat_id'] == $product['cat_id']) echo 'selected="selected"';?>><?=$cat['cat_name'];?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <p><label>Статус:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($product['status'] == 1) echo 'checked'; ?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($product['status'] == 0) echo 'checked'; ?>><span>Откл</span></label>
                                    <label class="custom-radio"><input name="status" type="radio" value="9" <?php if($product['status'] == 9) echo 'checked'; ?>><span>Архив</span></label>
                                </span>
                            </p>

                            <p><label class="custom-chekbox-wrap">
                                    <input type="checkbox" value="1" name="to_resale"<?php if($product['to_resale']) echo ' checked="checked"';?>>
                                    <span class="custom-chekbox"></span>Товар продления (для статистики)
                                </label>
                            </p>

                            <p><label>Показывать в списке продуктов:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="in_catalog" type="radio" value="1" <?php if($product['in_catalog'] == 1) echo 'checked'; ?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="in_catalog" type="radio" value="0" <?php if($product['in_catalog'] == 0) echo 'checked'; ?>><span>Нет</span></label>
                                </span>
                            </p>

                            <p><label>Показать отзывы:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_reviews" type="radio" value="1" <?php if($product['show_reviews'] == 1) echo 'checked'; ?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="show_reviews" type="radio" value="0" <?php if($product['show_reviews'] == 0) echo 'checked'; ?>><span>Нет</span></label>
                                </span>
                            </p>

                            <p><label>Продавать не больше 1 раза:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="sell_once" type="radio" value="1"<?php if($product['sell_once'] == 1) echo ' checked'; ?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="sell_once" type="radio" value="0"<?php if($product['sell_once'] == 0) echo ' checked'; ?>><span>Нет</span></label>
                                </span>
                            </p>

                            <div class="width-100"><label class="custom-chekbox-wrap">
                                    <input type="checkbox" value="1" name="show_custom_price" data-show_on="custom_price" <?php if(!empty($product['price_minmax'])) echo 'checked';?>>
                                    <span class="custom-chekbox"></span>Свободная цена
                                </label>
                            </div>

                            <div id="custom_price" class="width-100 hidden">
                                <?php if (!empty($product['price_minmax'])) {
                                    $price_mas = explode(":", $product['price_minmax']);
                                }?>

                                <p class="width-100"><label>Минимальная цена:</label>
                                    <input type="text" name="min_price" placeholder="Минимальная цена" value="<?=isset($price_mas[0]) ? $price_mas[0] : '';?>">
                                </p>

                                <p class="width-100"><label>Максимальная цена:</label>
                                    <input type="text" name="max_price" placeholder="Максимальная цена" value="<?=isset($price_mas[1]) ? $price_mas[1] : '';?>">
                                </p>
                            </div>

                            <p><label>Стоимость:</label>
                                <input type="text" value="<?=$product['price'];?>" name="price">
                            </p>

                            <p><label>Стоимость со скидкой:</label>
                                <input type="text" value="<?=$product['red_price'];?>" name="red_price">
                            </p>

                            <p><label>Количество:</label>
                                <input type="text" name="amt" value="<?=$product['product_amt'];?>" placeholder="Количество (в наличии)">
                            </p>

                            <p><label class="custom-chekbox-wrap">
                                    <input type="checkbox" value="1" name="show_amt"<?php if($product['show_amt'] == 1) echo ' checked="checked"';?>>
                                    <span class="custom-chekbox"></span>Показывать кол-во
                                </label>
                            </p>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100">
                                <label>Обложка:</label>
                            </div>

                            <div class="width-100">
                                <input type="file" name="product_cover">
                            </div>

                            <?php if(!empty($product['product_cover'])):?>
                                <div class="del_img_wrap">
                                    <img width="150" src="/images/product/<?=$product['product_cover']?>">
                                    <span class="del_img_link">
                                        <button type="submit" form="del_img" title="Удалить изображение с сервера?" name="del_img">
                                            <span class="icon-remove"></span>
                                        </button>
                                    </span>
                                </div>
                            <?php endif;?>

                            <input type="hidden" name="current_img" value="<?=$product['product_cover'];?>">

                            <p><input type="text" size="35" name="img_alt" value="<?=$product['img_alt'];?>" placeholder="Alt изображения"></p>

                            <p title="Видно только администрации" ><label>Примечание:</label>
                                <textarea name="note"><?=$product['note'];?></textarea>
                            </p>
                        </div>

                        <div class="col-1-1">
                            <p><label>Краткое описание</label>
                                <textarea name="desc" rows="3" cols="40"><?=$product['product_desc'];?></textarea>
                            </p>

                            <p class="width-100"><label>Надпись на кнопке описания:</label>
                                <input type="text" name="button_text" value="<?=$product['button_text'];?>">
                            </p>

                            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                        </div>
                    </div>

                <div class="row-line mt-0">
                    <div class="col-1-1 mb-0">
                        <h4>Куда направлять с кнопки описание</h4>

                        <div class="width-100">
                            <span class="custom-radio-wrap">
                                <label class="custom-radio" id="external-descr"><input type="radio" name="external_landing" value="1"<?php if($product['external_landing'] == 1) echo ' checked';?>>
                                    <span>Внешний лендинг</span>
                                </label>

                                <label class="custom-radio" id="inner-descr"><input type="radio" name="external_landing" value="0"<?php if($product['external_landing'] == 0) echo ' checked';?>>
                                    <span class="not-red">Внутреннее описание</span>
                                </label>
                            </span>
                        </div>

                        <p class="short-desct width-100 mb-20"<?php if($product['external_landing'] == 0) echo ' style="display: none;"';?> <?php if($product['external_landing'] == 1) echo ' style="display: block;"';?>>
                            <label>Ссылка на внешний лендинг:</label>
                            <input type="text" value="<?=$product['external_url']?>" name="external_url">
                        </p>
                    </div>
                </div>

                <div class="big-descr"<?php if($product['external_landing'] == 0) echo ' style="display: block;"';if($product['external_landing'] == 1) echo ' style="display: none;"';?>>
                    <div class="row-line mt-30">
                        <div class="col-1-1">
                            <p>Внутреннее описание (landing) продукта</p>
                            <?php /*<div class="split_test">
                                <div class="split_test_a">
                                    <p>Вариант 1</p>
                                    <span title="Заказов: <?=$slit_test[1];?> | Хитов: <?=$product['hits_1'];?>">
                                    <?php if($slit_test[1]!= 0 && $product['hits_1'] != 0) echo round(($slit_test[1] / $product['hits_1']) * 100); else echo 0;?> %</span>
                                </div>

                                <div class="split_test_b">
                                    <p>Вариант 2</p>
                                    <span title="Заказов: <?=$slit_test[2];?> | Хитов: <?=$product['hits_2'];?>">
                                    <?php if($slit_test[2]!= 0 && $product['hits_2'] != 0) echo round(($slit_test[2] / $product['hits_2']) * 100); else echo 0;?> %</span>
                                </div>
                                <div class="split_reset"><a href="/admin/products/reset/<?=$id;?>?type=<?=$prod_type['value'];?>">Обнулить</a></div>
                            </div>*/?>

                            <div class="width-100">
                                <textarea class="editor" name="text1"><?=$product['product_text1'];?></textarea>
                            </div>

                            <p><label>Произвольный HTML (выводится после описания):</label>
                                <textarea name="custom_code"><?=$product['custom_code'];?></textarea>
                            </p>
                        </div>
                    </div>

                    <div class="row-line mt-20">
                        <div class="col-1-2 mb-15"><label>Шаблон</label>
                            <div class="select-wrap">
                                <select name="text1_tmpl">
                                    <option value="1"<?php if($product['text1_tmpl'] == 1) echo ' selected="selected"';?>>Стандарт</option>
                                    <option value="2"<?php if($product['text1_tmpl'] == 2) echo ' selected="selected"';?>>Карточка</option>
                                    <option value="0"<?php if($product['text1_tmpl'] == 0) echo ' selected="selected"';?>>Без шаблона</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-1-2 mb-15"><label>Заголовок</label>
                            <div class="select-wrap">
                                <select name="text1_heading">
                                    <option value="1"<?php if($product['text1_heading'] == 1) echo ' selected="selected"';?>>Показать</option>
                                    <option value="0"<?php if($product['text1_heading'] == 0) echo ' selected="selected"';?>>Не показывать</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-1-2 mb-15"><label>CSS и JS в head</label>
                            <label>
                                <textarea name="text1_head"><?=$product['text1_head'];?></textarea>
                            </label>
                        </div>

                        <div class="col-1-2 mb-15">
                            <p>CSS и JS перед /body</p>
                            <p><textarea name="text1_bottom"><?=$product['text1_bottom'];?></textarea></p>
                        </div>
                    </div>

                    <?php if($setting['split_test_enable'] == 1):?>
                        <div class="row-line mt-20">
                            <div class="col-1-1">
                                <h4>Внутреннее описание (landing) продукта (2 вариант)</h4>

                                <div class="split_test">
                                    <div class="split_test_a">
                                        <p>Вариант 1</p>
                                        <span title="Заказов: <?=$slit_test[1];?> | Хитов: <?=$product['hits_1'];?>">
                                            <?=$slit_test[1]!= 0 && $product['hits_1'] != 0 ? round(($slit_test[1] / $product['hits_1']) * 100) : 0;?> %
                                        </span>
                                    </div>

                                    <div class="split_test_b">
                                        <p>Вариант 2</p>
                                        <span title="Заказов: <?=$slit_test[2];?> | Хитов: <?=$product['hits_2'];?>">
                                            <?=$slit_test[2]!= 0 && $product['hits_2'] != 0 ? round(($slit_test[2] / $product['hits_2']) * 100) : 0;?> %
                                        </span>
                                    </div>

                                    <div class="split_reset">
                                        <a href="/admin/products/reset/<?=$id;?>?type=<?=$product['type_id'];?>">Обнулить</a>
                                    </div>
                                </div>

                                <textarea class="editor" name="text2"><?=$product['product_text2'];?></textarea>
                            </div>
                        </div>

                        <div class="row-line mt-20">
                            <div class="col-1-2 mb-15"><label>Шаблон</label>
                                <div class="select-wrap">
                                    <select name="text2_tmpl">
                                        <option value="1"<?php if($product['text2_tmpl'] == 1) echo ' selected="selected"';?>>Стандарт</option>
                                        <option value="2"<?php if($product['text2_tmpl'] == 2) echo ' selected="selected"';?>>Карточка</option>
                                        <option value="0"<?php if($product['text2_tmpl'] == 0) echo ' selected="selected"';?>>Без шаблона</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-1-2 mb-15"><label>Заголовок</label>
                                <div class="select-wrap">
                                    <select name="text2_heading">
                                        <option value="1"<?php if($product['text2_heading'] == 1) echo ' selected="selected"';?>>Показать</option>
                                        <option value="0"<?php if($product['text2_heading'] == 0) echo ' selected="selected"';?>>Не показывать</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-1-2 mb-15"><label>CSS и JS в head</label>
                                <p><textarea name="text2_head"><?=$product['text2_head'];?></textarea></p>
                            </div>

                            <div class="col-1-2 mb-15"><label>CSS и JS перед /body</label>
                                <p><textarea name="text2_bottom"><?=$product['text2_bottom'];?></textarea></p>
                            </div>
                        </div>
                    <?php endif;?>
                </div>
            </div>


            <!-- 2 вкладка    СОБЫТИЯ  -->
            <div>
                <div class="row-line">
                    <div class="col-1-2 mb-15">
                        <?php $responder = System::CheckExtensension('responder',1);
                        if($responder):?>
                            <h4 class="h4-border">Рассылка</h4>
                            <div class="width-100"><label>Подписать на рассылку</label>
                                <select size="5" class="multiple-select" multiple="multiple" name="delivery[]">
                                    <?php $delivery_list = Responder::getDeliveryList(2,1,100);
                                    $delivery_arr = unserialize($product['delivery_sub']);
                                    if($delivery_list):
                                        foreach($delivery_list as $delivery):?>
                                            <option value="<?=$delivery['delivery_id'];?>"<?php if($delivery_arr != null && in_array($delivery['delivery_id'], $delivery_arr)) echo ' selected="selected"';?>>
                                                <?=$delivery['name'];?>
                                            </option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>

                            <div class="width-100"><label>Отписать от рассылок:</label>
                                <select size="5" class="multiple-select" multiple="multiple" name="delivery_unsub[]">
                                    <?php $delivery_arr = unserialize($product['delivery_unsub']);
                                    if($delivery_list):
                                        foreach($delivery_list as $delivery):?>
                                            <option value="<?=$delivery['delivery_id'];?>"<?php if($delivery_arr != null && in_array($delivery['delivery_id'], $delivery_arr)) echo ' selected="selected"';?>>
                                                <?=$delivery['name'];?>
                                            </option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        <?php endif;?>


                        <div class="width-100"><label>Добавить группы пользователю:</label>
                            <select size="7" class="multiple-select" multiple="multiple" name="add_group[]">
                                <?php $group_list = User::getUserGroups();
                                if($group_list):
                                    $add_groups = explode(",", $product['group_id']);
                                    foreach($group_list as $user_group):?>
                                        <option value="<?=$user_group['group_id'];?>"<?=in_array($user_group['group_id'], $add_groups)?' selected="selected"':''?>>
                                            <?=$user_group['group_title'];?>
                                        </option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>

                        <div class="width-100"><label>Удалить группы пользователя:</label>
                            <select size="7" class="multiple-select" multiple="multiple" name="del_group[]">
                                <?php if($group_list):
                                    $del_groups = explode(",", $product['del_group_id']);
                                    foreach($group_list as $user_group):?>
                                        <option value="<?=$user_group['group_id'];?>"<?=in_array($user_group['group_id'], $del_groups)?' selected="selected";':''?>>
                                            <?=$user_group['group_title'];?>
                                        </option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>

                        <?php if($product['type_id'] == '3'):?>
                            <p><label>План подписки:</label>
                                <select name="subscription">
                                    <option value="0">-- Выберите  --</option>
                                    <?php foreach($planes as $plane):?>
                                        <option value="<?=$plane['id'];?>"<?php if($plane['id'] == $product['subscription_id']) echo ' selected="selected"'?>>
                                            <?=empty($plane['service_name']) ? $plane['name'] : $plane['service_name'];?>
                                        </option>
                                    <?php endforeach;?>
                                </select>
                            </p>
                        <?php endif;?>

                        <p><label title="Укажите, если ваш продукт можно скачать">Ссылка для скачивания:</label>
                            <input type="text" name="link" value="<?=$product['link'];?>" placeholder="Ссылка для скачивания">
                        </p>

                        <div class="width-100">
                            <label>Лицензионные ключи</label>
                            <textarea name="pincodes" rows="8" cols="55"><?=$product['pincodes'];?></textarea>
                        </div><br />
                    </div>

                    <div class="col-1-2 mb-15">
                        <h4 class="h4-border">Интеграция с AcyMailing</h4>
                        <?php if(!empty($product['acymailing'])) {
                            $acymailing = unserialize(base64_decode($product['acymailing']));
                        }?>

                        <p><label title="Указывайте правильный протокол http или https">Адрес сайта с AcyMailing:</label>
                            <input placeholder="https://site.ru" type="text" name="acymailing[site]" value="<?php if(!empty($acymailing['site'])) echo $acymailing['site'];?>">
                        </p>

                        <div class="width-100"><label>Версия AcyMailing</label>
                            <div class="select-wrap">
                                <select name="acymailing[version]">
                                    <option value="5"<?php if(isset($acymailing['version']) && $acymailing['version'] == 5) echo ' selected="selected"';?> data-show_off="acymailing_field_id_order_num,acymailing_field_id_product_name,acymailing_field_id_pin_code">5 версия</option>
                                    <option value="6"<?php if(isset($acymailing['version']) && $acymailing['version'] == 6) echo ' selected="selected"';?>>7 версия</option>
                                </select>
                            </div>
                        </div>

                        <div class="width-100"><label>Платформа сайта с AcyMailing</label>
                            <div class="select-wrap">
                                <select name="acymailing[cms_type]">
                                    <option value="1"<?php if(!isset($acymailing['cms_type']) || $acymailing['cms_type'] == 1) echo ' selected="selected"';?>>Joomla</option>
                                    <option value="2"<?php if(isset($acymailing['cms_type']) && $acymailing['cms_type'] == 2) echo ' selected="selected"';?>>WordPress</option>
                                </select>
                            </div>
                        </div>

                        <p><label>ID рассылки (выписка счёта):</label>
                            <input type="text" name="acymailing[acy_id]" value="<?php if(!empty($acymailing['acy_id'])) echo $acymailing['acy_id'];?>">
                        </p>

                        <p><label>ID рассылки (после оплаты):</label>
                            <input type="text" name="acymailing[acy_id2]" value="<?php if(!empty($acymailing['acy_id2'])) echo $acymailing['acy_id2'];?>">
                        </p>

                        <p><label>Секретный ключ AcyMailing:</label>
                            <input type="text" name="acymailing[acy_key]" value="<?php if(!empty($acymailing['acy_key'])) echo $acymailing['acy_key'];?>">
                        </p>

                        <p id="acymailing_field_id_pin_code"><label>ID поля для отправки pin-кода</label>
                            <input type="text" name="acymailing[field_id_pin_code]" value="<?=isset($acymailing['field_id_pin_code']) ? $acymailing['field_id_pin_code'] : '5';?>">
                        </p>

                        <p><label class="custom-chekbox-wrap only-margin-top" title="Будут передаваться дополнительные данные из заказа: номер заказа, название продукта и пин код">
                                <input id="send_order_data" type="checkbox" value="1" name="acymailing[send_order_data]" <?php if(!empty($acymailing['send_order_data'])) echo 'checked';?> data-show_on="send_order_data_fields_ids">
                                <span class="custom-chekbox"></span>Передавать данные из заказа в AcyMailing
                            </label>
                        </p>

                        <div id="send_order_data_fields_ids" class="hidden">
                            <p id="acymailing_field_id_order_num"><label>ID поля для отправки номера заказа</label>
                                <input type="text" name="acymailing[field_id_order_num]" value="<?=isset($acymailing['field_id_order_num']) ? $acymailing['field_id_order_num'] : '3';?>">
                            </p>

                            <p id="acymailing_field_id_product_name"><label>ID поля для отправки имени продукта</label>
                                <input type="text" name="acymailing[field_id_product_name]" value="<?=isset($acymailing['field_id_product_name']) ? $acymailing['field_id_product_name'] : '4';?>">
                            </p>
                        </div>

                        <!--  РАСШИРЕНИЕ ExpertSender-->
                        <?php if(isset($expsndr) && $expsndr['list']) {
                            require_once(ROOT.'/extensions/expertsender/views/product_edit.php');
                        }?>

                        <h4 class="h4-border">Другое</h4>
                        <div class="width-100" title="Отправлять ли логин и пароль после оформления заказа"><label>Письма после заказа</label>
                            <div class="select-wrap">
                                <select name="send_pass">
                                    <option value="2"<?php if($product['send_pass'] == 2) echo ' selected="selected"';?>>Отправлять всё</option>
                                    <option value="1"<?php if($product['send_pass'] == 1) echo ' selected="selected"';?>>Отправлять только заказ</option>
                                    <option value="0"<?php if($product['send_pass'] == 0) echo ' selected="selected"';?>>Не отправлять ничего</option>
                                </select>
                            </div>
                        </div>

                        <p class="width-100" title="Перенаправление для бесплатных продуктов"><label>Редирект после оформления</label>
                            <input type="text" name="redirect_after" value="<?=$product['redirect_after'];?>">
                        </p>
                    </div>


                    <div class="col-1-1 mb-15">
                        <h4 class="h4-border">Письмо клиенту</h4>
                        <p class="label"><label>Тема письма:</label>
                            <input type="text" name="subject_letter" value="<?=$product['letter_subject'] ? $product['letter_subject'] : 'Ваш заказ.';?>">
                        </p>

                        <p><label>Текст письма с заказом</label>
                            <textarea name="letter" class="editor" rows="6" style="width:100%"><?=$product['letter'];?></textarea>
                        </p>

                        <p class="small">[CLIENT_NAME] - имя клиента<br />
                            [ORDER] - номер заказа<br />
                            [PRODUCT_NAME] - название продукта<br />
                            [SUMM] - сумма<br />
                            [LINK] - ссылка на скачивание<br />
                            [SUPPORT] - емейл службы поддержки<br />
                            [PINCODE] - пин код (лицензионный ключ)<br />
                            [EMAIL] - Email клиента
                        </p>
                    </div>

                    <div class="col-1-1">
                        <h4 class="h4-border mt-20">Автодобавление продуктов к заказу</h4>
                        <p>При оплате к основному заказу добавятся выбранные продукты</p>
                        <?php $list_select = Product::getProductListOnlySelect();?>

                        <div class="width-100"><label>Выберите продукты для добавления</label>
                            <?php $auto_add = !empty($product['auto_add']) ? unserialize(base64_decode($product['auto_add'])) : false;?>
                            <select name="auto_add[]" multiple="multiple" class="multiple-select">
                                <?php foreach ($list_select as $item):
                                    if ($item['product_id'] == $product['product_id']) {
                                        continue;
                                    }?>
                                    <option value="<?=$item['product_id'];?>"<?php if($auto_add && in_array($item['product_id'], $auto_add)) echo ' selected="selected"';?>><?=$item['product_name'];?></option>
                                    <?php if($item['service_name']):?>
                                        <option disabled="disabled" class="service-name">(<?=$item['service_name'];?>)</option>
                                    <?php endif;
                                endforeach;?>
                            </select>
                        </div>
                    </div>

                    <div class="col-1-1 mb-0">
                        <h4>Письмо куратору</h4>
                    </div>

                    <div class="col-1-2">
                        <p class="width-100"><label>Тема письма:</label>
                            <input type="text" name="subj_manager" value="<?php if(isset($manager_letter['subj_manager'])) echo $manager_letter['subj_manager'];?>">
                        </p>
                    </div>

                    <div class="col-1-2">
                        <p class="width-100" title="Если оставить поле пустым, то письмо НЕ отправится"><label>Email:</label>
                            <input type="email" name="email_manager" pattern="^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,})+$" value="<?php if(isset($manager_letter['email_manager'])) echo $manager_letter['email_manager'];?>">
                        </p>
                    </div>

                    <div class="col-1-1">
                        <div class="width-100">
                            <div class="label">Содержание письма:</div>
                            <textarea name="letter_manager" class="editor" rows="6"><?php if(isset($manager_letter['letter_manager'])) echo $manager_letter['letter_manager']?></textarea>
                            <div>
                            <br />
                            <p>Переменные для подстановки: <br />
                            [ORDER] - номер заказа<br />[DATE] - дата заказа<br />[SUMM] - сумма заказа<br />[PINCODE] - ключ активации (пин код) для продукта<br />[NAME] - имя клиента<br />[SURNAME] - фамилия клиента<br />[EMAIL] - Email клиента<br />
                            [LINK] - ссылка для скачивания продукта <br />[NICK_TG] - ник в Telegram<br />[NICK_IG] - ник в Instagram</p>
                            </div>
                        </div>
                    </div>


                    <div style="width: calc(100% - 30px); margin-bottom:10px">
                        <h4 class="h4-border">Промо коды</h4>
                    </div>

                    <div class="col-1-2">
                        <div class="width-100"><label>Включить генерацию промо кодов:</label>
                            <span class="custom-radio-wrap">
                                <label class="custom-radio"><input name="promo_enable" type="radio" value="1" <?php if($promo_gen && $promo_gen['status'] == 1) echo 'checked';?>><span>Да</span></label>
                                <label class="custom-radio"><input name="promo_enable" type="radio" value="0" <?php if($promo_gen && $promo_gen['status'] == 0 || $promo_gen == null) echo 'checked';?>><span>Нет</span></label>
                            </span>

                            <input type="hidden" name="promo_gen" value="<?php if($promo_gen != false) echo 1; else echo 0;?>">
                        </div>

                        <p><label>Время действия промокода, дней:</label>
                            <input type="text" name="duration" value="<?php if($promo_gen) echo $promo_gen['duration'];?>">
                        </p>

                        <p><label>Слово для начала промо кода (promo_ и т.д.):</label>
                            <input type="text" name="promo_word" placeholder="promo_" value="<?php if($promo_gen) echo $promo_gen['promo_word']?>">
                        </p>

                        <p><label title="Поддерживаются HTML теги">Описание промо кода</label>
                            <textarea name="promo_desc"><?php if($promo_gen) echo $promo_gen['promo_desc'];?></textarea>
                        </p>

                        <div class="width-100"><label>Тип дисконта:</label>
                            <div class="select-wrap">
                                <select name="type_discount">
                                    <option value="summ"<?php if($promo_gen && $promo_gen['type_discount'] == 'summ') echo ' selected="selected"';?>>Сумма</option>
                                    <option value="percent"<?php if($promo_gen && $promo_gen['type_discount'] == 'percent') echo ' selected="selected"';?>>Проценты</option>
                                </select>
                            </div>
                        </div>

                        <label>Значение дисконта :</label>
                        <input type="text" name="discount" value="<?php if($promo_gen) echo $promo_gen['discount']?>">
                    </div>

                    <div class="col-1-2">
                        <?php $params_promo = unserialize($promo_gen['products']);?>
                        <label>Действует на:</label>
                        <select class="multiple-select" name="promo_products[]" multiple="multiple" style="height: 300px;">
                            <?php foreach ($list_select as $item):?>
                                <option value="<?=$item['product_id'];?>"<?php if($params_promo) {if(in_array($item['product_id'], $params_promo)) echo ' selected="selected"'; }?>><?=$item['product_name'];?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>


                    <div class="col-1-1">
                        <h4 class="h4-border">HTTP-уведомления</h4>
                        <?php $notices = ProductHttpNotice::getNoticesToProduct($product['product_id']);
                        if($notices):?>
                            <div class="overflow-container">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th width="180" class="text-left">Имя</th>
                                            <th width="180" class="text-left">Адрес сайта</th>
                                            <th class="text-left">Переменные</th>
                                            <th class="td-last"></th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach($notices as $notice):
                                            $vars = !empty($notice['vars']) ? json_decode($notice['vars'], true) : null;
                                            $var_names = $vars ? implode(', ', array_filter(array_values($vars))) : '';
                                            if ($vars) {
                                                foreach ($vars as $name => $new_name) {
                                                    if (!$new_name) {
                                                        unset($vars[$name]);
                                                    }
                                                }
                                            };?>

                                            <tr>
                                                <td><?=$notice['notice_id'];?></td>
                                                <td class="text-left">
                                                    <a href="#" data-uk-modal data-prod_httpnotice_id="<?=$notice['notice_id'];?>">
                                                        <?=$notice['notice_name'];?>
                                                    </a>
                                                </td>
                                                <td class="text-left">
                                                    <span style="display:inline-block;text-overflow:ellipsis;max-width:180px;display: inline-block;overflow: hidden;">
                                                        <?=$notice['notice_url'];?>
                                                    </span>
                                                </td>
                                                <td class="text-left"><?=$var_names;?></td>
                                                <td class="td-last">
                                                    <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/products/delhttpnotice/<?=$notice['notice_id'];?>?token=<?=$_SESSION['admin_token'];?>&prod_id=<?=$product['product_id'];?>&prod_type=<?=$product['type_id'];?>" title="Удалить">
                                                        <i class="fas fa-times" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif;?>

                        <p><a href="#prod_httpnotice_add" data-uk-modal>Добавить уведомление</a></p>
                    </div>

                    <?php $product_reminder = ProductReminder::getReminderToProduct($product['product_id']);?>
                    <div class="col-1-1">
                        <h4 class="h4-border">Письма-уведомления о неоплаченном счете</h4>
                        <p><a href="#prod_reminder" data-uk-modal><?=$product_reminder ? 'Редактировать' : 'Добавить';?> тексты уведомлений</a></p>
                    </div>
                </div>
            </div>


            <!-- НАЧИСЛЕНИЯ -->
            <?php if($partnership):?>
                <div>
                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Партнерская программа</h4>
                        </div>

                        <div class="col-1-2">
                            <p>
                                <label>Включить начисления партнёрских</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="run_aff" type="radio" value="1" <?php if($product['run_aff'] == 1) echo 'checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="run_aff" type="radio" value="0" <?php if($product['run_aff'] == 0) echo 'checked';?>><span>Откл</span></label>
                                </span>
                            </p>

                            <p><label>Виден для партнёров</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="in_partner" type="radio" value="1" <?php if($product['in_partner'] == 1) echo 'checked'; ?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="in_partner" type="radio" value="0" <?php if($product['in_partner'] == 0) echo 'checked'; ?>><span>Нет</span></label>
                                </span>
                            </p>

                            <p class="width-100">
                                <label>Индивидуальная комиссия для продукта, %</label><input type="text" value="<?=$product['product_comiss'];?>" name="product_comiss">
                            </p>

                            <div class="width-100">
                                <label>Рекламные материалы</label>
                                <input type="file" name="ads">
                                <input type="hidden" name="current_ads" value="<?=$product['ads'];?>">
                            </div>

                            <?php if($product['ads'] != null):?>
                                <p><img src="/template/admin/images/zip.png" alt="">
                                    <span class="small"><?=$product['ads'];?></span>
                                    <!--a href="#"><span class="small">Удалить</span></a-->
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="row-line">
                        <div class="col-1-1 mb-0">
                            <h4>Авторы и учителя</h4>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100">
                                <p>Автор 1</p>

                                <div class="author-line">
                                    <?php $authors = User::getAuthors();?>
                                    <div class="select-wrap">
                                        <select name="author1">
                                            <option value="0">Выберите</option>
                                            <?php if($authors):
                                            foreach($authors as $author):?>
                                            <option value="<?=$author['user_id'];?>"<?php if($author['user_id'] == $product['author1']) echo ' selected="selected"';?>><?=$author['user_name'];?></option>
                                            <?php endforeach;
                                            endif?>
                                        </select>
                                    </div>

                                    <div>
                                        <input type="text" name="val1" value="<?=$product['comiss1'];?>">
                                    </div>

                                    <div class="select-wrap">
                                        <select name="comiss1">
                                            <option value="percent"<?php if($product['type_comiss1'] == 'percent') echo ' selected="selected"';?>>%</option>
                                            <option value="summ"<?php if($product['type_comiss1'] == 'summ') echo ' selected="selected"';?>>р.</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="width-100">
                                <p>Автор 2</p>
                                <div class="author-line">
                                    <div class="select-wrap">
                                        <select name="author2">
                                            <option value="0">Выберите</option>
                                            <?php if($authors):
                                                foreach($authors as $author):?>
                                                    <option value="<?=$author['user_id'];?>"<?php if($author['user_id'] == $product['author2']) echo ' selected="selected"';?>><?=$author['user_name'];?></option>
                                                <?php endforeach;
                                            endif; ?>
                                        </select>
                                    </div>

                                    <div class="">
                                        <input type="text" name="val2" value="<?=$product['comiss2'];?>">
                                    </div>

                                    <div class="select-wrap width-100">
                                        <select name="comiss2">
                                            <option value="percent"<?php if($product['type_comiss2'] == 'percent') echo ' selected="selected"';?>>%</option>
                                            <option value="summ"<?php if($product['type_comiss2'] == 'summ') echo ' selected="selected"';?>>р.</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="width-100">
                                <p>Автор 3</p>
                                <div class="author-line">
                                    <div class="select-wrap">
                                        <select name="author3">
                                            <option value="0">Выберите</option>
                                            <?php if($authors):
                                            foreach($authors as $author):?>
                                                <option value="<?=$author['user_id'];?>"<?php if($author['user_id'] == $product['author3']) echo ' selected="selected"';?>><?=$author['user_name'];?></option>
                                            <?php endforeach;
                                            endif; ?>
                                        </select>
                                    </div>

                                    <div class="">
                                        <input type="text" name="val3" value="<?=$product['comiss3'];?>">
                                    </div>

                                    <div class="select-wrap">
                                        <select name="comiss3">
                                            <option value="percent"<?php if($product['type_comiss3'] == 'percent') echo ' selected="selected"';?>>%</option>
                                            <option value="summ"<?php if($product['type_comiss3'] == 'summ') echo ' selected="selected"';?>>р.</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <p></p>
                        </div>
                    </div>
                </div>
            <?php endif;?>



            <!--  4 вкладка РАССРОЧКА  -->
            <div>
                <div class="row-line">
                    <div class="col-1-1 mb-15">
                        <h4 class="h4-border">Рассрочка</h4>
                    </div>

                    <div class="col-1-2">
                        <div class="width-100"><label>Разрешить рассрочку</label>
                            <div class="select-wrap">
                                <select name="installment">
                                    <option value="0">Нет</option>
                                    <option value="1"<?php if($product['installment'] == 1) echo ' selected="selected"';?> data-show_on="installments">Да</option>
                                    <!--option value="2"<?php if($product['installment'] == 2) echo ' selected="selected"';?>>Только через платёжные системы</option>
                                    <option value="3"<?php if($product['installment'] == 3) echo ' selected="selected"';?>>Внутри системы</option-->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row-line hidden" id="installments">
                    <div class="col-1-2">
                        <div class="width-100">
                            <label>Рассрочка для продукта:</label>
                            <select size="7" class="multiple-select" multiple="multiple" name="installments[]">
                                <?php $instalments = Product::getInstalments();
                                if($instalments):
                                    $product_installments = Installment::getInstallmentsIds2Product($product['product_id']);
                                    foreach($instalments as $instalment):?>
                                        <option value="<?=$instalment['id'];?>"<?php if($product_installments && in_array($instalment['id'], $product_installments)) echo ' selected="selected"';?>><?=$instalment['title'];?></option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>
                    </div>

                    <div class="col-1-2"></div>

                    <div class="col-1-2">
                        <p><strong>Действия при успешном погашении</strong></p>
                        <div class="width-100">
                            <label>Добавить группы пользователю:</label>
                            <select size="7" class="multiple-select" multiple="multiple" name="installment_action[add_group][]">
                                <?php $group_list = User::getUserGroups();
                                if($group_list):
                                    foreach($group_list as $user_group):?>
                                        <option value="<?=$user_group['group_id'];?>"<?php if(isset($installment_action['add_group'])){ if(in_array($user_group['group_id'], $installment_action['add_group'])) echo ' selected="selected"';}?>><?=$user_group['group_title'];?></option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>

                        <div class="width-100"><label>Добавить план подписки:</label>
                            <select name="installment_action[add_plane]">
                                <option value="0">-- Выберите  --</option>
                                <?php if($planes):
                                    foreach($planes as $plane):?>
                                        <option value="<?=$plane['id'];?>"<?php if($installment_action && $plane['id'] == $installment_action['add_plane']) echo ' selected="selected"';?>><?=$plane['name']?></option>
                                        <?php if($plane['service_name']):?>
                                            <option disabled="disabled" class="service-name">(<?=$plane['service_name'];?>)</option>
                                        <?php endif;
                                    endforeach;
                                endif;?>
                            </select>
                        </div>
                    </div>

                    <div class="col-1-2">
                        <p><strong>Действия при просрочке</strong></p>

                        <div class="width-100"><label>Удалить группы пользователя:</label>
                            <select size="7" class="multiple-select" multiple="multiple" name="installment_action[del_group][]">
                                <?php $group_list = User::getUserGroups();
                                if($group_list):
                                    foreach($group_list as $user_group):?>
                                        <option value="<?=$user_group['group_id'];?>"<?php if(isset($installment_action['del_group'])){ if(in_array($user_group['group_id'], $installment_action['del_group'])) echo ' selected="selected"';}?>><?=$user_group['group_title'];?></option>
                                    <?php endforeach;
                                endif;?>
                            </select>
                        </div>

                        <div class="width-100"><label>Отменить планы подписок:</label>
                            <select size="7" class="multiple-select" multiple="multiple" name="installment_action[del_plane][]">
                                <?php if($planes):
                                    foreach($planes as $plane):?>
                                        <option value="<?=$plane['id'];?>"<?php if(isset($installment_action['del_plane']) && in_array($plane['id'], $installment_action['del_plane'])) echo ' selected="selected"';?>><?=$plane['name']?></option>
                                        <?php if($plane['service_name']):?>
                                            <option disabled="disabled" class="service-name">(<?=$plane['service_name'];?>)</option>
                                        <?php endif;
                                    endforeach;
                                endif;?>
                            </select>
                        </div>
                    </div>


                    <div class="col-1-1">
                        <h4>Действия при оплатах рассрочки</h4>
                        <?php $installment_list = Product::getInstalments();
                        if($installment_list):?>
                            <p>
                                <label>При оплате в рассрочку добавлять только эти группы:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="installment_addgroups" type="radio" value="1" <?php if($product['installment_addgroups'] == 1) echo 'checked';?>><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="installment_addgroups" type="radio" value="0" <?php if($product['installment_addgroups'] == 0) echo 'checked';?>><span>Откл</span></label>
                                </span>
                            </p>

                            <?php foreach($installment_list as $installment):
                                $max_periods = $installment['max_periods'];?>

                                <div class="installment-block">
                                    <p><strong>Рассрочка: <a target="_blank" href="/admin/installment/edit/<?=$installment['id'];?>"><?=$installment['title'];?></a></strong></p>

                                    <?php for($x = 1; $x <= $max_periods; $x++):?>

                                    <p><label>При оплате <?=$x;?> платежа присвоить группу:</label>
                                        <select name="group_after_install[<?=$installment['id'];?>][<?=$x;?>]">
                                            <option value="0">- Не выбрано -</option>
                                            <?php $group_list = User::getUserGroups();
                                            $group_arr = explode(",", $product['group_id']);
                                            if($group_list):
                                                foreach($group_list as $user_group):?>
                                                    <option value="<?=$user_group['group_id'];?>" <?php if($group_actions && $group_actions[$installment['id']][$x] == $user_group['group_id']) echo 'selected="selected"';?>><?=$user_group['group_title'];?></option>
                                                <?php endforeach;
                                            endif;?>
                                        </select>
                                    </p>
                                   <?php endfor;?>
                                </div>
                            <?php endforeach;
                        endif;?>
                    </div>
                </div>
            </div>

            <!-- 3 вкладка  АПСЕЛЛЫ -->
            <div>
                <div class="menu-apsell">
                    <ul>
                        <li>Предложение 1</li>
                        <li>Предложение 2</li>
                        <li>Предложение 3</li>
                    </ul>
                    <div>
                        <div class="row-line">
                            <div class="col-1-2">
                                <div class="width-100"><label>Выберите продукт</label>
                                    <div class="select-wrap">
                                        <select name="upsell_1">
                                            <option value="0">Нет</option>
                                            <?php //$list_select = Product::getProductListOnlySelect();
                                            foreach ($list_select as $item):?>
                                                <option value="<?=$item['product_id'];?>"<?php if($product['upsell_1'] == $item['product_id']) echo ' selected="delected"';?>><?=$item['product_name'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-1-2">
                                <p><label>Стоимость</label><input type="text" value="<?=$product['upsell_1_price'];?>" name="upsell_1_price"></p>
                            </div>

                            <div class="col-1-1">
                                <div class="width-100"><label>Описание до кнопки купить</label><textarea class="editor" rows="4" cols="50" name="upsell_1_desc"><?=$product['upsell_1_desc']?></textarea></div>
                            </div>

                            <div class="col-1-1">
                                <p><label>Описание после кнопки купить</label><textarea class="editor" rows="7" cols="50" name="upsell_1_text"><?=$product['upsell_1_text']?></textarea></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="row-line">
                            <div class="col-1-2">
                                <div class="width-100"><label>Выберите продукт</label>
                                    <div class="select-wrap">
                                        <select name="upsell_2">
                                            <option value="0">Нет</option>
                                            <?php //$list_select = Product::getProductListOnlySelect();
                                            foreach ($list_select as $item):?>
                                                <option value="<?=$item['product_id'];?>"<?php if($product['upsell_2'] == $item['product_id']) echo ' selected="delected"';?>><?=$item['product_name'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-1-2">
                                <p><label>Стоимость</label><input type="text" value="<?=$product['upsell_2_price'];?>" name="upsell_2_price"></p>
                            </div>

                            <div class="col-1-1">
                                <p><label>Описание до кнопки купить</label><textarea class="editor" rows="4" cols="50" name="upsell_2_desc"><?=$product['upsell_2_desc']?></textarea></p>
                            </div>

                            <div class="col-1-1">
                                <p><label>Описание после кнопки купить</label><textarea class="editor" rows="7" cols="50" name="upsell_2_text"><?=$product['upsell_2_text']?></textarea></p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="row-line">
                            <div class="col-1-2">
                                <div class="width-100"><label>Выберите продукт</label>
                                    <div class="select-wrap">
                                        <select name="upsell_3">
                                            <option value="0">Нет</option>
                                            <?php //$list_select = Product::getProductListOnlySelect();
                                            foreach ($list_select as $item):?>
                                                <option value="<?=$item['product_id'];?>"<?php if($product['upsell_3'] == $item['product_id']) echo ' selected="delected"';?>><?=$item['product_name'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-1-2">
                                <p><label>Стоимость</label><input type="text" value="<?=$product['upsell_3_price'];?>" name="upsell_3_price"></p>
                            </div>

                            <div class="col-1-1">
                                <p><label>Описание до кнопки купить</label><textarea class="editor" rows="4" cols="50" name="upsell_3_desc"><?=$product['upsell_3_desc']?></textarea></p>
                            </div>

                            <div class="col-1-1">
                                <p><label>Описание после кнопки купить</label><textarea class="editor" rows="7" cols="50" name="upsell_3_text"><?=$product['upsell_3_text']?></textarea></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!--   5 вкладка  ПРОЧЕЕ -->
            <div>
                <div class="row-line">
                    <div class="col-1-2">

                        <h4 class="h4-border">Комплектации</h4>
                        <div class="width-100"><label title="Указывается, если ваш продукт будет комплектацией базового">Базовый продукт</label>
                            <div class="select-wrap">
                                <select name="base_id">
                                    <option value="0">Нет</option>
                                    <?php foreach ($list_select as $item):?>
                                        <option value="<?=$item['product_id'];?>"<?php if($product['base_id'] == $item['product_id']) echo ' selected="selected"';?>><?=$item['product_name'];?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <?php $complect_params = unserialize(base64_decode($product['complect_params']));
                        $complect_params = explode("|", $complect_params);?>
                        <p class="width-100"><label>Название комплектации</label><input type="text" size="35" value="<?=$complect_params[0];?>" name="complect_name" placeholder="Название комплектации"></p>
                        <p class="width-100"><label>Очерёдность вывода</label><input type="text" size="35" value="<?=$product['complect_sort']; ?>" name="complect_sort" placeholder="Очерёдность"></p>
                        <p class="width-100"><label>Список выгод</label><textarea rows="4" cols="50" name="complect_list"><?=$complect_params[1];?></textarea></p>

                        <div class="width-100">
                            <label title="Выделяет блок комплектации">Выделить комплектацию</label>
                            <div class="select-wrap">
                                <select name="complect_highlight">
                                    <option value="default"<?php if($complect_params[2] == 'default') echo ' selected="selected"';?>>Нет</option>
                                    <option value="one"<?php if($complect_params[2] == 'one') echo ' selected="selected"';?>>Вариант 1</option>
                                    <option value="two"<?php if($complect_params[2] == 'two') echo ' selected="selected"';?>>Вариант 2</option>
                                    <option value="three"<?php if($complect_params[2] == 'three') echo ' selected="selected"';?>>Вариант 3</option>
                                    <option value="four"<?php if($complect_params[2] == 'four') echo ' selected="selected"';?>>Вариант 4</option>
                                </select>
                            </div>
                        </div>

                        <h4 class="h4-border mt-30">Цены продукта</h4>
                        <!--div class="width-100">
                            <label title="Определяет вид блока с ценой">Вид блока с ценой</label>
                            <div class="select-wrap">
                                <select name="price_layout">
                                    <option value="0"<?php //if($product['price_layout'] == 0) echo ' selected="selected"';?>>Обычный</option>
                                    <option value="1"<?php //if($product['price_layout'] == 1) echo ' selected="selected"';?>>Вертикальный</option>
                                    <option value="2"<?php //if($product['price_layout'] == 2) echo ' selected="selected"';?>>Выпадающий список</option>
                                </select>
                            </div>
                            </div-->
                            <input type="hidden" name="price_layout" value="0">

                            <div class="width-100">
								<label>Скрыть цену в каталоге</label>

								<span class="custom-radio-wrap">
									<label class="custom-radio"><input name="hidden_price" type="radio" value="1" <?php if($product['hidden_price'] == 1) echo 'checked';?>><span>Да</span></label>
									<label class="custom-radio"><input name="hidden_price" type="radio" value="0" <?php if($product['hidden_price'] == 0) echo 'checked';?>><span>Нет</span></label>
								</span>
							</div>

                            <div class="width-100">
                                <label>Показать цены и кнопку купить в комплектации</label>

                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_price_box" type="radio" value="1" <?php if($product['show_price_box'] == 1) echo 'checked';?>><span>Да</span></label>
                                    <label class="custom-radio"><input name="show_price_box" type="radio" value="0" <?php if($product['show_price_box'] == 0) echo 'checked';?>><span>Нет</span></label>
                                </span>
                            </div>



                            <p class="width-100"><label>HTML код под ценой и кнопками</label><textarea name="code_price_box"><?=$product['code_price_box'];?></textarea></p>
                        </div>


                        <div class="col-1-2">
                            <h4 class="h4-border">SEO</h4>
                            <p><label>Алиас:</label><input type="text" name="alias" value="<?=$product['product_alias'];?>" placeholder="Алиас продукта"></p>
                            <p><label>Title:</label><input type="text" name="title" value="<?=$product['product_title'];?>" placeholder="Title продукта"></p>
                            <p><label>Meta Description:</label><textarea name="meta_desc" rows="3" cols="40"><?=$product['meta_desc'];?></textarea></p>
                            <p><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"><?=$product['meta_keys'];?></textarea></p>
                            <label class="custom-chekbox-wrap">
                                <input type="checkbox" value="1" name="select_payments_on" <?php if($product['select_payments']) echo 'checked';?> data-show_on="select_payments">
                                <span class="custom-chekbox"></span>Свои способы оплаты
                            </label>
                            <div id="select_payments" class="width-100 hidden">
                                <select class="multiple-select" name="select_payments[]" multiple="multiple">
                                    <?php $select_payments = unserialize($product['select_payments']);
                                    $payments = Order::getPayments();
                                    if($payments && $select_payments):
                                        foreach($payments as $payment):?>
                                            <option value="<?=$payment['payment_id'];?>" <?php if($product['select_payments'] && in_array($payment['payment_id'], $select_payments)) echo ' selected="selected"';?>><?=$payment['title'];?></option>
                                        <?php endforeach;
                                    else:
                                        foreach($payments as $payment):?>
                                            <option value="<?=$payment['payment_id'];?>" selected="selected"><?=$payment['title'];?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>

						<h4 class="h4-border">Разделение финансов</h4>
						<div class="select-wrap">
							<select name="organization_id">
								<option value="0">Нет</option>
								<?php $prod_org = Organization::getOrgByProduct($id);
								$org_list = Organization::getOrgList();
								if($org_list):
								foreach($org_list as $org):?>
								<option value="<?=$org['id'];?>"<?php if($org['id'] == $prod_org) echo ' selected="selected"';?>><?=$org['org_name'];?></option>
								<?php endforeach;
								endif;?>
							</select>
						</div>


                    </div>
                </div>
            </form>

            <!-- 6 вкладка  тут другая форма, поэтому оставляем после вкладки №5  -->
            <div>
                <div class="row-line">
                    <?php if($related_products):?>
                    <div class="col-1-1">
                        <h4><strong>Добавленные продукты</strong></h4>
                        <?php foreach($related_products as $related_prod):?>
                        <div class="width-100">
                            <form class="form-row" action="" id="edit_related<?=$related_prod['id'];?>" method="POST">
                                <div class="form-row-name">
                                    <a href="/admin/related/edit/<?=$id;?>/<?=$related_prod['id'];?>"><?php $name = Product::getProductName($related_prod['product_id']); echo $name['product_name'];?></a>
                                </div>

                                <div class="form-row-price">
                                    <div class="price-input-wrap-2">
                                        <input class="price-input-2" title="Цена" type="text" name="price" value="<?=$related_prod['price'];?>">
                                        <div class="price-input-cur-2">р.</div>
                                    </div>
                                </div>

                                <div class="form-row-sort">
                                    <div class="relative">
                                        <input class="price-input-sort-input" type="text" title="Порядок" placeholder="Порядок" name="sort" value="<?=$related_prod['sort'];?>">
                                        <span class="icon-numbers-1-9 price-input-sort"></span>
                                    </div>
                                    <input type="hidden" name="show_complects" value="0">
                                </div>

                                <div class="form-row-status">
                                    <div class="select-wrap">
                                        <select name="status">
                                            <option value="1"<?php if($related_prod['status'] == 1) echo ' selected="selected"';?>>Вкл</option>
                                            <option value="0"<?php if($related_prod['status'] == 0) echo ' selected="selected"';?>>Откл</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-row-submit">
                                    <input type="hidden" name="related_id" value="<?=$related_prod['id'];?>">
                                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                                    <button type="submit" title="Сохранить" name="save_related" class="button save button-green-rounding button-lesson"><span class="icon-check"></span></button>
                                    <button type="submit" onclick="return confirm('<?=System::Lang('YOU_SHURE');?>?')" title="Удалить" name="del_related" class="button save button-red-rounding button-lesson"><span class="icon-remove"></span></button>
                                </div>
                            </form>
                        </div>
                    <?php endforeach;?>
                    </div>
                    <?php endif; ?>

                    <div class="col-1-1">
                        <h4>Добавить продукт для показа в корзине в процессе оформления заказа</h4>
                        <form class="row-line inner-flex-end" id="related" action="" method="POST">

                            <div class="col-1-4">
                                <p class="mb-5">Выберите продукт</p>
                                <div class="select-wrap">
                                    <select name="product_related">
                                        <option value="0">Нет</option>
                                        <?php foreach ($list_select as $item):
                                            if($item['product_id'] == $id) continue;?>
                                            <option value="<?=$item['product_id'];?>"><?=$item['product_name'];?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-1-4">
                                <p class="mb-5">Стоимость</p>
                                <div class="price-input-wrap-2">
                                    <input class="price-input-2" type="text" placeholder="Цена" name="price">
                                    <div class="price-input-cur-2">р.</div>
                                </div>
                            </div>

                            <div class="col-1-4">
                                <p class="mb-5">Задать порядок</p>
                                <input type="text" placeholder="Порядок" name="sort">
                            </div>

                            <input type="hidden" name="show_complects" value="0">

                            <div class="col-1-4">
                                <input type="hidden" name="status" value="1">
                                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                                <input type="submit" name="add_related" class="button save button-green-rounding add-prod-but" value="Добавить">
                            </div>

                            <div class="col-1-1">
                                <h4>Универсальное описание данного продукта для корзины</h4>
                                <textarea name="related_desc" class="editor"></textarea>
                                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="/admin/delimg/<?=$product['product_id'];?>" id="del_img" method="POST">
        <input type="hidden" name="path" value="images/product/<?=$product['product_cover'];?>">
        <input type="hidden" name="page" value="admin/products/edit/<?=$product['product_id'];?><?php if(isset($_GET['type'])) echo '?type='.$_GET['type'];?>">
        <input type="hidden" name="table" value="products">
        <input type="hidden" name="name" value="product_cover">
        <input type="hidden" name="where" value="product_id">
        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    </form>

    <div class="buttons-under-form">
        <p class="button-delete"><a onclick="return confirm('Вы уверены?')" href="<?=$setting['script_url'];?>/admin/products/del/<?=$product['product_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="Удалить"><i class="icon-remove"></i>Удалить продукт</a></p>

        <form class="copy-but" action="/admin/products/copy/<?=$product['product_id'];?>" method="POST">
            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
            <button class="button-copy-2" type="submit" name="copy"><i class="icon-copy"></i>Копировать продукт</button>
        </form>
    </div>

    <div id="prod_httpnotice_add" class="uk-modal"><?php require_once(__DIR__ . '/httpnotice/add.php');?></div>
    <div id="prod_httpnotice_edit" class="uk-modal"></div>

    <div id="prod_reminder" class="uk-modal">
        <?php ;
        if (!$product_reminder){
            require_once(__DIR__ . '/reminder/add.php');
        } else {
            require_once(__DIR__ . '/reminder/edit.php');
        }?>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>