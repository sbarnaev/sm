<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Добавить продукт</h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?=$setting['script_url'];?>/admin/logout" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/products/">Продукты</a></li>
        <li>Добавить продукт</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="traning-top">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/prod-icon.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Добавить продукт</h3>
                </div>
            </div>

            <ul class="nav_button">
                <li><input type="submit" name="addproduct" value="Сохранить" class="button save button-green-rounding"></li>
                <li class="nav_button__last"><a class="button button-red-rounding" href="<?=$setting['script_url'];?>/admin/products/">Закрыть</a></li>
            </ul>
        </div>

        <div class="tabs">
            <ul>
                <li>Основное</li>
                <li>События</li>
                <?php if($partnership):?>
                    <li>Начисления</li>
                <?php endif;?>
                <li>Допродажи</li>
                <li>Прочее</li>
            </ul>

            <div class="admin_form">
                <!-- 1 вкладка -->
                <div>
                    <div class="row-line">
                        <div class="col-1-2">
                            <h4>Основное</h4>
                            <p class="width-100"><label>Название:</label>
                                <input type="text" name="name" placeholder="Название продукта" required="required">
                            </p>

                            <p class="width-100"><label>Служебное название:</label>
                                <input type="text" name="service_name" placeholder="Служебное название продукта">
                            </p>

                            <div class="width-100"><label>Категория:</label>
                                <div class="select-wrap">
                                    <select name="cat_id">
                                        <option value="">Выберите</option>
                                        <?php $cat_list = Product::getAllCatList();
                                        if($cat_list):
                                            foreach($cat_list as $cat):?>
                                                <option value="<?=$cat['cat_id'];?>"><?=$cat['cat_name'];?></option>
                                            <?php endforeach;
                                        endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="width-100"><label>Статус:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="status" type="radio" value="1" checked=""><span>Вкл</span></label>
                                    <label class="custom-radio"><input name="status" type="radio" value="0"><span>Откл</span></label>
                                    <label class="custom-radio"><input name="status" type="radio" value="9"><span>Архив</span></label>
                                </span>
                            </div>

                            <p><label class="custom-chekbox-wrap">
                                    <input type="checkbox" value="1" name="to_resale">
                                    <span class="custom-chekbox"></span>Товар продления
                                </label>
                            </p>

                            <div class="width-100"><label>Показывать в каталоге</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="in_catalog" type="radio" value="1" checked=""><span>Да</span></label>
                                    <label class="custom-radio"><input name="in_catalog" type="radio" value="0"><span>Нет</span></label>
                                </span>
                            </div>

                            <div class="width-100"><label>Показать отзывы к продукту</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="show_reviews" type="radio" value="1" checked=""><span>Да</span></label>
                                    <label class="custom-radio"><input name="show_reviews" type="radio" value="0"><span>Нет</span></label>
                                </span>
                            </div>

                            <p><label>Продавать не больше 1 раза:</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="sell_once" type="radio" value="1"><span>Да</span></label>
                                    <label class="custom-radio"><input name="sell_once" type="radio" value="0" checked="checked"><span>Нет</span></label>
                                </span>
                            </p>

                            <div class="width-100"><label class="custom-chekbox-wrap">
                                    <input type="checkbox" value="1" name="show_custom_price" data-show_on="custom_price">
                                    <span class="custom-chekbox"></span>Свободная цена
                                </label>
                            </div>

                            <div id="custom_price" class="width-100 hidden">
                                <p class="width-100"><label>Минимальная цена:</label>
                                    <input type="text" name="min_price" placeholder="Минимальная цена">
                                </p>

                                <p class="width-100"><label>Максимальная цена:</label>
                                    <input type="text" name="max_price" placeholder="Максимальная цена">
                                </p>
                            </div>

                            <p class="width-100"><label>Стоимость:</label>
                                <input type="text" name="price" placeholder="Цена">
                            </p>

                            <p class="width-100"><label>Стоимость со скидкой:</label>
                                <input type="text" name="red_price" placeholder="Красная цена">
                                <?php $prod_type = getProductType();?>
                                <input type="hidden" name="product_type" value="<?=$prod_type['value'];?>">
                            </p>

                            <p class="width-100"><label>Количество:</label>
                                <input type="text" value="-1" name="amt" placeholder="Количество (в наличии)">

                                <label class="custom-chekbox-wrap only-margin-top">
                                    <input type="checkbox" value="1" name="show_amt">
                                    <span class="custom-chekbox"></span>Показывать кол-во
                                </label>
                            </p>

                            <p class="width-100"><label>Краткое описание</label>
                                <textarea name="desc"></textarea>
                            </p>

                            <p class="width-100"><label>Надпись на кнопке описания</label>
                                <input type="text" name="button_text" value="Заказать">
                            </p>

                            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Обложка:</label>
                                <input type="file" name="product_cover">
                            </div>

                            <p class="width-100"><input type="text" size="35" name="img_alt" placeholder="Альтернативный текст"></p>
                        </div>
                    </div>

                    <div class="row-line mt-0">
                        <div class="col-1-2">
                        <h4>Куда направлять с кнопки описание</h4>
                            <div class="width-100">
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio" id="inner-descr"><input type="radio" name="external_landing" value="0"><span>Внутреннее описание</span></label>
                                    <label class="custom-radio" id="external-descr"><input type="radio" name="external_landing" value="1" checked><span>Внешний лендинг</span></label>
                                </span>
                            </div>

                            <p class="short-desct width-100 mb-20"><label>Ссылка на внешний лендинг:</label>
                                <input type="text" name="external_url">
                            </p>
                        </div>
                    </div>

                    <div class="big-descr" style="display: none;">
                        <div class="row-line">
                            <div class="col-1-1">
                                <h4>Внутреннее описание (landing) продукта</h4>

                                <p class="width-100">
                                    <textarea class="editor" name="text1"></textarea>
                                </p>

                                <div class="width-100">
                                    <div class="label">Шаблон:</div>
                                    <div class="select-wrap">
                                        <select name="text1_tmpl">
                                            <option value="1">Стандарт</option>
                                            <option value="2">Карточка</option>
                                            <option value="0">Без шаблона</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="width-100">
                                    <div class="label">Заголовок:</div>
                                    <div class="select-wrap">
                                        <select name="text1_heading">
                                            <option value="1">Показать</option>
                                            <option value="0">Не показывать</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-1-2">
                                <h4>CSS и JS в head</h4>
                                <p class="width-100"><textarea name="text1_head" rows="6"></textarea></p>
                            </div>

                            <div class="col-1-2">
                                <h4>CSS и JS перед /body</h4>
                                <p class="width-100"><textarea name="text1_bottom" rows="6"></textarea></p>
                            </div>
                        </div>
                        <? /*
                        <?php if($setting['split_test_enable'] == 1):?>
                        <div class="row-line">
                            <div class="col-1-1">
                                <h4>Внутреннее описание (landing) продукта (2 вариант)</h4>
                                <p class="width-100"><textarea class="editor" name="text2"></textarea></p>
                                <div class="width-100">
                                    <div class="label">Шаблон: </div>
                                    <div class="select-wrap">
                                        <select name="text2_tmpl">
                                            <option value="1">Стандарт</option>
                                            <option value="2">Карточка</option>
                                            <option value="0">Без шаблона</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="width-100">
                                    <div class="label">Заголовок:</div>
                                    <div class="select-wrap">
                                        <select name="text2_heading">
                                            <option value="1">Показать</option>
                                            <option value="0">Не показывать</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-1-2">
                                <h4>CSS и JS в head</h4>
                                <p class="width-100"><textarea name="text2_head" rows="6"></textarea></p>
                            </div>

                            <div class="col-1-2">
                                <h4>CSS и JS перед /body</h4>
                                <p class="width-100"><textarea name="text2_bottom" rows="6"></textarea></p>
                            </div>
                        </div>
                        <?php endif;?>
                        */ ?>
                    </div>
                </div>


                <!-- 2 вкладка ----------------------- -->
                <div>
                    <div class="row-line">
                        <div class="col-1-2">
                            <?php $responder = System::CheckExtensension('responder',1);
                            if($responder):?>
                                <div class="width-100"><label>Подписать на рассылку</label>
                                    <select class="multiple-select" size="5" multiple="multiple" name="delivery[]">
                                        <?php $delivery_list = Responder::getDeliveryList(2,1,100);
                                        if($delivery_list):
                                            foreach($delivery_list as $delivery):?>
                                                <option value="<?=$delivery['delivery_id'];?>"><?=$delivery['name'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>

                                <div class="width-100"><label>Отписать от рассылок</label>
                                    <select class="multiple-select" size="5" multiple="multiple" name="delivery_unsub[]">
                                        <?php if($delivery_list):
                                            foreach($delivery_list as $delivery):?>
                                                <option value="<?=$delivery['delivery_id'];?>"><?=$delivery['name'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <div class="width-100"><label>Добавить группы пользователю</label>
                                <select class="multiple-select" size="7" multiple="multiple" name="add_group[]">
                                    <?php $group_list = User::getUserGroups();
                                    if($group_list):
                                        foreach($group_list as $user_group):?>
                                            <option value="<?=$user_group['group_id'];?>"><?=$user_group['group_title'];?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>

                            <div class="width-100"><label>Удалить группы пользователя</label>
                                <select class="multiple-select" size="7" multiple="multiple" name="del_group[]">
                                    <?php if($group_list):
                                        foreach($group_list as $user_group):?>
                                            <option value="<?=$user_group['group_id'];?>"><?=$user_group['group_title'];?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>

                            <?php if(isset($_GET['type'])&& $_GET['type'] == '3'):?>
                                <div class="width-100"><label>План подписки:</label>
                                    <div class="select-wrap">
                                        <select required="required" name="subscription">
                                            <option value="">Выберите</option>
                                            <?php $planes = Member::getPlanes();
                                            if($planes):
                                                foreach($planes as $plane):?>
                                                    <option value="<?=$plane['id'];?>"><?=$plane['name'];?></option>
                                                <?php endforeach;
                                            endif;?>
                                        </select>
                                    </div>
                                </div>
                            <?php endif;?>

                            <p class="width-100"><label>Ссылка:</label>
                                <input type="text" name="link" placeholder="Ссылка для скачивания">
                            </p>

                            <p class="width-100"><label>HTTP-уведомление</label>
                                <input type="text" size="35" name="notif_url" placeholder="Уведомление на скрипт">
                            </p>

                            <div class="width-100">
                                <h4>Пин коды</h4>
                                <textarea name="pincodes" rows="8" cols="55"></textarea>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <h4>Письма заказа</h4>
                            <div class="width-100" title="Отправлять ли логин и пароль после оформления заказа"><label>Письма после заказа</label>
                                <div class="select-wrap">
                                    <select name="send_pass">
                                        <option value="2">Отправлять всё</option>
                                        <option value="1">Отправлять только заказ</option>
                                        <option value="0">Не отправлять ничего</option>
                                    </select>
                                </div>
                            </div>

                            <p class="width-100" title="Перенаправление для бесплатных продуктов"><label>Редирект после оформления</label>
                                <input type="text" name="redirect_after" placeholder="">
                            </p>
                        </div>


                        <div class="col-1-1">
                            <div class="width-100">
                                <p class="label"><label>Тема письма:</label>
                                    <input type="text" name="subject_letter" value="Ваш заказ.">
                                </p>

                                <div class="label">Письмо клиенту:</div>
                                <textarea name="letter" class="editor" rows="6"><?=$setting['client_letter'];?></textarea>
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
                        </div>

                        <div class="col-1-1 mb-0">
                            <h4>Письмо куратору</h4>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100"><label>Тема письма:</label><input type="text" name="subj_manager"></p>
                        </div>

                        <div class="col-1-2">
                            <p class="width-100" title="Если оставить поле пустым, то письмо НЕ отправится"><label>Email:</label><input type="email" name="email_manager" pattern="^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,})+$"></p>
                        </div>

                        <div class="col-1-1">
                            <div class="width-100">
                                <div class="label">Содержание письма:</div>
                                <textarea name="letter_manager" class="editor" rows="6"></textarea>

                                <p>Переменные для подстановки:<br />
                                    [ORDER] - номер заказа<br />
                                    [DATE] - дата заказа<br />
                                    [SUMM] - сумма заказа<br />
                                    [NAME] - имя клиента<br />
                                    [SURNAME] - фамилия клиента<br />
                                    [EMAIL] - Email клиента<br />
                                    [NICK_TG] - ник в Telegram<br />
                                    [NICK_IG] - ник в Instagram
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3 вкладка  Начисления   ------------------- -->
                <?php if($partnership):?>
                    <div>
                        <div class="row-line">
                            <div class="col-1-1 mb-0">
                                <h4>Партнерская программа</h4>
                            </div>

                            <div class="col-1-2">
                                <div class="width-100"><label>Включить начисления партнёрских</label>
                                    <span class="custom-radio-wrap">
                                        <label class="custom-radio"><input name="run_aff" type="radio" value="1"><span>Вкл</span></label>
                                        <label class="custom-radio"><input name="run_aff" type="radio" value="0" checked=""><span>Откл</span></label>
                                    </span>
                                </div>

                                <div class="width-100">
                                    <label>Виден для партнёров</label>
                                    <span class="custom-radio-wrap">
                                        <label class="custom-radio"><input name="in_partner" type="radio" value="1"><span>Да</span></label>
                                        <label class="custom-radio"><input name="in_partner" type="radio" value="0" checked=""><span>Нет</span></label>
                                    </span>
                                </div>

                                <div class="width-100">
                                    <label>Индивидуальная комиссия для продукта, %</label><input type="text" name="product_comiss">
                                </div>
                            </div>
                        </div>

                        <div class="row-line">
                            <div class="col-1-1 mb-0">
                                <h4>Авторы и учителя</h4>
                            </div>

                            <div class="col-1-2">
                                <div class="width-100">
                                    <div class="label">Автор 1</div>

                                    <?php $authors = User::getAuthors();?>
                                    <div class="author-line">
                                        <div class="select-wrap">
                                            <select name="author1">
                                                <option value="0">Выберите</option>
                                                <?php if($authors):
                                                    foreach($authors as $author):?>
                                                        <option value="<?=$author['user_id'];?>"><?=$author['user_name'];?></option>
                                                    <?php endforeach;
                                                endif;?>
                                            </select>
                                        </div>

                                        <div><input type="text" name="val1"></div>

                                        <div class="select-wrap">
                                            <select name="comiss1">
                                                <option value="percent">%</option>
                                                <option value="summ">р.</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="width-100">
                                    <div class="label">Автор 2</div>

                                    <div class="author-line">
                                        <div class="select-wrap">
                                            <select name="author2">
                                                <option value="0">Выберите</option>
                                                <?php if($authors):
                                                    foreach($authors as $author):?>
                                                        <option value="<?=$author['user_id'];?>"><?=$author['user_name'];?></option>
                                                    <?php endforeach;
                                                endif;?>
                                            </select>
                                        </div>

                                        <div><input type="text" name="val2"></div>

                                        <div class="select-wrap">
                                            <select name="comiss2">
                                                <option value="percent">%</option>
                                                <option value="summ">р.</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="width-100">
                                    <div class="label">Автор 3</div>

                                    <div class="author-line">
                                        <div class="select-wrap">
                                            <select name="author3">
                                                <option value="0">Выберите</option>
                                                <?php if($authors):
                                                foreach($authors as $author):?>
                                                    <option value="<?=$author['user_id'];?>"><?=$author['user_name'];?></option>
                                                <?php endforeach;
                                                endif; ?>
                                            </select>
                                        </div>

                                        <div><input type="text" name="val3"></div>

                                        <div class="select-wrap">
                                            <select name="comiss3">
                                                <option value="percent">%</option>
                                                <option value="summ">р.</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <p></p>
                            </div>
                        </div>
                    </div>
                <?php endif;?>
                <!-- НАЧИСЛЕНИЯ -->


                <!-- 4 вкладка  АПСЕЛЛЫ -->
                <div>

                    <div class="menu-apsell">
                        <ul>
                            <li>Предложение 1</li>
                            <li>Предложение 2</li>
                            <li>Предложение 3</li>
                        </ul>
                        <div>
                            <div>
                                <div class="row-line">
                                    <div class="col-1-2">
                                        <div class="width-100">
                                            <label>Выберите продукт</label>
                                            <div class="select-wrap">
                                                <select name="upsell_1">
                                                    <option value="0">Нет</option>
                                                    <?php $list_select = Product::getProductListOnlySelect();
                                                    if($list_select):
                                                    foreach ($list_select as $item):?>
                                                    <option value="<?=$item['product_id'];?>"><?=$item['product_name'];?></option>
                                                    <?php endforeach;
                                                    endif;?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-1-2">
                                        <p class="width-100"><label>Стоимость</label><input type="text" name="upsell_1_price"></p>
                                    </div>
                                    <div class="col-1-1">
                                        <p class="width-100"><label>Описание до кнопки купить</label><textarea class="editor" rows="4" cols="50" name="upsell_1_desc"></textarea></p>
                                    </div>
                                    <div class="col-1-1">
                                        <p class="width-100"><label>Описание после кнопки купить</label><textarea class="editor" rows="7" cols="50" name="upsell_1_text"></textarea></p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row-line">
                                    <div class="col-1-2">
                                        <div class="width-100">
                                            <label>Выберите продукт</label>
                                            <div class="select-wrap">
                                                <select name="upsell_2">
                                                    <option value="0">Нет</option>
                                                    <?php if($list_select):
                                                    foreach ($list_select as $item):?>
                                                    <option value="<?=$item['product_id'];?>"><?=$item['product_name'];?></option>
                                                    <?php endforeach;
                                                    endif;?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-1-2">
                                        <p class="width-100"><label>Стоимость</label><input type="text" name="upsell_2_price"></p>
                                    </div>
                                    <div class="col-1-1">
                                        <div class="width-100"><label>Описание до кнопки купить</label><textarea class="editor" rows="4" cols="50" name="upsell_2_desc"></textarea></div>
                                    </div>
                                    <div class="col-1-1">
                                        <p class="width-100"><label>Описание после кнопки купить</label><textarea class="editor" rows="7" cols="50" name="upsell_2_text"></textarea></p>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="row-line">
                                    <div class="col-1-2">
                                        <div class="width-100">
                                            <label>Выберите продукт</label>
                                            <div class="select-wrap">
                                                <select name="upsell_3">
                                                    <option value="0">Нет </option>
                                                    <?php if($list_select):
                                                    foreach ($list_select as $item):?>
                                                    <option value="<?=$item['product_id'];?>"><?=$item['product_name'];?></option>
                                                    <?php endforeach;
                                                    endif;?>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-1-2">
                                        <p class="width-100"><label>Стоимость</label><input type="text" name="upsell_3_price"></p>
                                    </div>
                                    <div class="col-1-1">
                                        <p class="width-100"><label>Описание до кнопки купить</label><textarea class="editor" rows="4" cols="50" name="upsell_3_desc"></textarea></p>
                                    </div>

                                    <div class="col-1-1">
                                        <div class="width-100"><label>Описание после кнопки купить</label><textarea class="editor" rows="7" cols="50" name="upsell_3_text"></textarea></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div>
                    <div class="row-line">
                        <div class="col-1-2">

                            <h4>Комплектации</h4>

                            <div class="width-100">
                                <label title="Указывается, если ваш продукт будет комплектацией базового">Базовый продукт</label>
                                <div class="select-wrap">
                                    <select name="base_id">
                                        <option value="0">- Нет -</option>
                                        <?php if($list_select):
                                            foreach ($list_select as $item):?>
                                                <option value="<?=$item['product_id'];?>"><?=$item['product_name'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            </div>

                            <p class="width-100"><label>Название комплектации</label>
                                <input type="text" size="35" name="complect_name" placeholder="Название комплектации">
                            </p>

                            <p class="width-100"><label>Очерёдность вывода</label>
                                <input type="text" size="35" name="complect_sort" placeholder="Очерёдность">
                            </p>

                            <p class="width-100"><label>Список выгод</label>
                                <textarea rows="4" cols="50" name="complect_list"></textarea>
                            </p>

                            <div class="width-100"><label title="Выделяет блок комплектации">Выделить комплектацию</label>
                                <div class="select-wrap">
                                    <select name="complect_highlight">
                                        <option value="default">Нет</option>
                                        <option value="one">Вариант 1</option>
                                        <option value="two">Вариант 2</option>
                                        <option value="three">Вариант 3</option>
                                        <option value="four">Вариант 4</option>
                                    </select>
                                </div>
                            </div>


                            <h4>Цены продукта</h4>
                            <!--div class="width-100">
                            <label title="Определяет вид блока с ценой">Вид блока с ценой</label>
                            <div class="select-wrap">
                                <select name="price_layout">
                                    <option value="0">Обычный</option>
                                    <option value="1">Вертикальный</option>
                                    <option value="2">Выпадающий список</option>
                                </select>
                            </div>
                            </div-->
                            <input type="hidden" name="price_layout" value="0">

                            <div class="width-100"><label>Скрыть цену в каталоге</label>
                                    <span class="custom-radio-wrap">
                                        <label class="custom-radio"><input name="hidden_price" type="radio" value="1"><span>Да</span></label>
                                        <label class="custom-radio"><input name="hidden_price" type="radio" value="0" checked="checked"><span>Нет</span></label>
                                    </span>
                                </div>

                                <div class="width-100"><label>Показать цены и кнопку купить в комплектации</label>
                                    <span class="custom-radio-wrap">
                                        <label class="custom-radio"><input name="show_price_box" type="radio" value="1"><span>Да</span></label>
                                        <label class="custom-radio"><input name="show_price_box" type="radio" value="0" checked="checked"><span>Нет</span></label>
                                    </span>
                                </div>

                            <p class="width-100"><label>HTML код под ценой и кнопками</label>
                                <textarea style="font-size:14px" rows="8" cols="50" name="code_price_box"></textarea>
                            </p>
                        </div>

                        <div class="col-1-2">
                            <h4>SEO</h4>
                            <p class="width-100"><label>Алиас:</label><input type="text" name="alias" placeholder="Алиас продукта"></p>
                            <p class="width-100"><label>Title:</label><input type="text" name="title" placeholder="Title продукта"></p>
                            <p class="width-100"><label>Meta Description:</label><textarea name="meta_desc" rows="3" cols="40"></textarea></p>
                            <p class="width-100"><label>Meta Keys:</label><textarea name="meta_keys" rows="3" cols="40"></textarea></p>
                            <label class="custom-chekbox-wrap">
                                <input type="checkbox" value="1" name="select_payments_on" data-show_on="select_payments">
                                <span class="custom-chekbox"></span>Свои способы оплаты
                            </label>
                            <div id="select_payments" class="width-100 hidden">
                                <select class="multiple-select" name="select_payments[]" multiple="multiple">
                                    <?php $payments = Order::getPayments();
                                    if($payments):
                                        foreach($payments as $payment):?>
                                            <option value="<?=$payment['payment_id'];?>" selected="selected"><?=$payment['title'];?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
    
</body>
</html>
<?php function getProductType()
{
    if(isset($_GET['type']) && $_GET['type'] == 2){
        $prod_type['value'] = 2; $prod_type['type'] = 'Физический продукт';
    } elseif(isset($_GET['type']) && $_GET['type'] == 3) {
        $prod_type['value'] = 3; $prod_type['type'] = 'Подписка';
    } else {
        $prod_type['value'] = 1; $prod_type['type'] = 'Цифровой продукт';
    }
    return $prod_type;
}?>