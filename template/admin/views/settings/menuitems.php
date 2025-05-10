<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1><?=System::Lang('MENU_ITEMS_LIST');?></h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank"><?=System::Lang('GO_SITE');?></a><a href="<?=$setting['script_url'];?>/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><?=System::Lang('MENU_ITEMS_LIST');?></li>
    </ul>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>
    
    <div class="admin_top admin_top-flex">
        <div class="admin_top-inner">
            <div><img src="/template/admin/images/icons/nastr-icon.svg" alt=""></div>
            <div><h3 class="traning-title mb-0">Настройка меню</h3></div>
        </div>
    </div>
    
    <div class="tabs">
        <ul>
            <li>Главное меню</li>
            <li>Меню пользователя</li>
        </ul>

        <div class="admin_form">
            
                <div>
                
                
                <div class="col-1-2"><a class="button save button-green-rounding" href="#ModalItem" data-uk-modal="{center:true}">Создать новый пункт</a><p></p></div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="text-left">Название</th>
                            <th class="text-left">Тип</th>
                            <th class="text-left">URL</th>
                            <th class="td-last"></th>
                        </tr>
                    </thead>
    
                    <tbody>
                        <?php if($menu_items):
                            foreach($menu_items as $item):?>
                                <tr<?php if($item['status'] == 0) echo ' class="off"'?>>
                                    <td><?=$item['item_id'];?></td>
                                    <td class="text-left"><?php if($item['parent_id'] != 0) echo '|- '; ?><a href="<?="{$setting['script_url']}/admin/menuitems/edit/{$item['item_id']}?type={$item['type']}"?>"><?=$item['name'];?></a></td>
                                    <td class="text-left"><?=$item['type'];?></td>
                                    <td class="text-left"><?=$item['link'];?></td>
                                    <td class="td-last"><a class="link-delete" onclick="return confirm('<?=System::Lang('YOU_SHURE');?>?')" href="<?=$setting['script_url'];?>/admin/menuitems/del/<?=$item['item_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="<?=System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
                                </tr>
                            <?php endforeach;
                        else:
                            echo 'Нет пунктов меню';
                        endif;?>
                    </tbody>
                </table>
                </div>
                
                <div>
                <form action="" method="POST">
                <table class="table">
                <div class="col-1-2"><input type="submit" name="user_menu_save" class="button save button-green-rounding" style="font-size:100%" value="Сохранить">
                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>"><p></p></div>
                        <thead>
                            <tr>
                                <th class="text-left">Пункт</th>
                                <th class="text-left">Статус</th>
                                <th class="text-left">Имя пунтка</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-left">Кураторская 2.0</td>
                                <td class="text-left">
                                    <div class="width-100"><label>Включено:</label>
                                        <span class="custom-radio-wrap">
                                            <label class="custom-radio"><input name="user_menu[curators2]" type="radio" value="1"<?php if(isset($user_menu['curators2']) && $user_menu['curators2'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                            <label class="custom-radio"><input name="user_menu[curators2]" type="radio" value="0"<?php if(isset($user_menu['curators2']) && $user_menu['curators2'] == 0) echo ' checked';?>><span>Откл</span></label>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <input type="text" name="user_menu[curators2_title]" value="<?=$user_menu['curators2_title'];?>">
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="text-left">Кураторская 1.0</td>
                                <td class="text-left">
                                    <div class="width-100"><label>Включено:</label>
                                        <span class="custom-radio-wrap">
                                            <label class="custom-radio"><input name="user_menu[curators]" type="radio" value="1"<?php if(isset($user_menu['curators']) && $user_menu['curators'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                            <label class="custom-radio"><input name="user_menu[curators]" type="radio" value="0"<?php if(isset($user_menu['curators']) && $user_menu['curators'] == 0) echo ' checked';?>><span>Откл</span></label>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <input type="text" name="user_menu[curators_title]" value="<?=$user_menu['curators_title'];?>">
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="text-left">Мои тренинги 2.0</td>
                                <td class="text-left">
                                    <div class="width-100"><label>Включено:</label>
                                        <span class="custom-radio-wrap">
                                            <label class="custom-radio"><input name="user_menu[mytraining2]" type="radio" value="1"<?php if(isset($user_menu['mytraining2']) && $user_menu['mytraining2'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                            <label class="custom-radio"><input name="user_menu[mytraining2]" type="radio" value="0"<?php if(isset($user_menu['mytraining2']) && $user_menu['mytraining2'] == 0) echo ' checked';?>><span>Откл</span></label>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <input type="text" name="user_menu[mytraining2_title]" value="<?=$user_menu['mytraining2_title'];?>">
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="text-left">Мои тренинги 1.0</td>
                                <td class="text-left">
                                    <div class="width-100"><label>Включено:</label>
                                        <span class="custom-radio-wrap">
                                            <label class="custom-radio"><input name="user_menu[mytraining]" type="radio" value="1"<?php if(isset($user_menu['mytraining']) && $user_menu['mytraining'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                            <label class="custom-radio"><input name="user_menu[mytraining]" type="radio" value="0"<?php if(isset($user_menu['mytraining']) && $user_menu['mytraining'] == 0) echo ' checked';?>><span>Откл</span></label>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <input type="text" name="user_menu[mytraining_title]" value="<?=$user_menu['mytraining_title'];?>">
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="text-left">Партнёрка</td>
                                <td class="text-left">
                                    <div class="width-100"><label>Включено:</label>
                                        <span class="custom-radio-wrap">
                                            <label class="custom-radio"><input name="user_menu[partners]" type="radio" value="1"<?php if(isset($user_menu['partners']) && $user_menu['partners'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                            <label class="custom-radio"><input name="user_menu[partners]" type="radio" value="0"<?php if(isset($user_menu['partners']) && $user_menu['partners'] == 0) echo ' checked';?>><span>Откл</span></label>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <input type="text" name="user_menu[partners_title]" value="<?=$user_menu['partners_title'];?>">
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="text-left">Авторская</td>
                                <td class="text-left">
                                    <div class="width-100"><label>Включено:</label>
                                        <span class="custom-radio-wrap">
                                            <label class="custom-radio"><input name="user_menu[authors]" type="radio" value="1"<?php if(isset($user_menu['authors']) && $user_menu['authors'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                            <label class="custom-radio"><input name="user_menu[authors]" type="radio" value="0"<?php if(isset($user_menu['authors']) && $user_menu['authors'] == 0) echo ' checked';?>><span>Откл</span></label>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <input type="text" name="user_menu[authors_title]" value="<?=$user_menu['authors_title'];?>">
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="text-left">Заказы</td>
                                <td class="text-left">
                                    <div class="width-100"><label>Включено:</label>
                                        <span class="custom-radio-wrap">
                                            <label class="custom-radio"><input name="user_menu[myorders]" type="radio" value="1"<?php if(isset($user_menu['myorders']) && $user_menu['myorders'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                            <label class="custom-radio"><input name="user_menu[myorders]" type="radio" value="0"<?php if(isset($user_menu['myorders']) && $user_menu['myorders'] == 0) echo ' checked';?>><span>Откл</span></label>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <input type="text" name="user_menu[myorders_title]" value="<?=$user_menu['myorders_title'];?>">
                                </td>
                            </tr>
                            
                            <tr>
                                <td class="text-left">Профиль</td>
                                <td class="text-left">
                                    <div class="width-100"><label>Включено:</label>
                                        <span class="custom-radio-wrap">
                                            <label class="custom-radio"><input name="user_menu[myprofile]" type="radio" value="1"<?php if(isset($user_menu['myprofile']) && $user_menu['myprofile'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                            <label class="custom-radio"><input name="user_menu[myprofile]" type="radio" value="0"<?php if(isset($user_menu['myprofile']) && $user_menu['myprofile'] == 0) echo ' checked';?>><span>Откл</span></label>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <input type="text" name="user_menu[myprofile_title]" value="<?=$user_menu['myprofile_title'];?>">
                                </td>
                            </tr>
                            
                            <?php $forum = System::CheckExtensension('forum2');
                            if($forum):?>
                            <tr>
                                <td class="text-left">Форум</td>
                                <td class="text-left">
                                    <div class="width-100"><label>Включено:</label>
                                        <span class="custom-radio-wrap">
                                            <label class="custom-radio"><input name="user_menu[forum]" type="radio" value="1"<?php if(isset($user_menu['forum']) && $user_menu['forum'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                            <label class="custom-radio"><input name="user_menu[forum]" type="radio" value="0"<?php if(isset($user_menu['forum']) && $user_menu['forum'] == 0) echo ' checked';?>><span>Откл</span></label>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <input type="text" name="user_menu[forum_title]" value="<?php if(isset($user_menu['forum_title'])) echo $user_menu['forum_title'];?>">
                                </td>
                            </tr>
                            <?php endif;?>
                            
                            
                            <tr>
                                <td class="text-left">Свой URL</td>
                                <td class="text-left">
                                    <div class="width-100"><label>Включено:</label>
                                        <span class="custom-radio-wrap">
                                            <label class="custom-radio"><input name="user_menu[custom1]" type="radio" value="1"<?php if(isset($user_menu['custom1']) && $user_menu['custom1'] == 1) echo ' checked';?>><span>Вкл</span></label>
                                            <label class="custom-radio"><input name="user_menu[custom1]" type="radio" value="0"<?php if(isset($user_menu['custom1']) && $user_menu['custom1'] == 0) echo ' checked';?>><span>Откл</span></label>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-left">
                                    <input type="text" name="user_menu[custom1_title]" value="<?php if(isset($user_menu['custom1_title'])) echo $user_menu['custom1_title'];?>" placeholder="Имя пункта">
                                    <input type="text" name="user_menu[custom1_url]" value="<?php if(isset($user_menu['custom1_url'])) echo $user_menu['custom1_url'];?>" placeholder="URL ссылки">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </form>
                </div>
            
        </div>
    </div>

    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

<div id="ModalItem" class="uk-modal">
    <div class="uk-modal-dialog">
        <div class="userbox modal-userbox">
            <a href="#close" title="Закрыть" class="uk-modal-close uk-close modal-close"><span class="icon-close"></span></a>

            <div>
                <h3 class="modal-head">Выберите тип пункта меню</h3>
                <div class="row-line">
                    <div class="col-1-2">
                        <p><a href="/admin/menuitems/add?type=main">Главная страница</a></p>
                        <p><a href="/admin/menuitems/add?type=catalog">Каталог продуктов</a></p>
                        <p><a href="/admin/menuitems/add?type=feedback">Обратная связь</a></p>
                        <p><a href="/admin/menuitems/add?type=custom">Внешний URL</a></p>

                        <?php if($setting['enable_reviews'] == 1):?>
                            <p><a href="/admin/menuitems/add?type=reviews">Страница отзывов</a></p>
                        <?php endif;?>

                        <?php if(System::CheckExtensension('training', 1)):?>
                            <p><a href="/admin/menuitems/add?type=training">Главный список тренингов (new)</a></p>
                            <!--<p><a href="/admin/menuitems/add?type=cat_training">Категории тренингов (new)</a></p>-->
                        <?php endif;?>
                    </div>

                    <div class="col-1-2">
                        <?php $blog = System::CheckExtensension('blog', 1);
                        if($blog):?>
                            <p><a href="/admin/menuitems/add?type=blog">Блог</a></p>
                        <?php endif;?>


                        <?php $corses = System::CheckExtensension('courses', 1);
                        if($corses):?>
                            <p><a href="/admin/menuitems/add?type=courses">Онлайн курсы</a></p>
                        <?php endif;?>

                        <?php $aff = System::CheckExtensension('partnership', 1);
                        if($aff):?>
                            <p><a href="/admin/menuitems/add?type=aff">Партнёрская программа</a></p>
                        <?php endif;?>


                        <?php $forum = System::CheckExtensension('forum2', 1);
                        if($forum):?>
                            <p><a href="/admin/menuitems/add?type=forum">Форум</a></p>
                        <?php endif; ?>

                        <?php $gallery = System::CheckExtensension('gallery', 1);
                        if($gallery):?>
                            <p><a href="/admin/menuitems/add?type=gallery">Галерея</a></p>
                        <?php endif; ?>


                        <?php $pages = System::getStaticPages();
                        if($pages):
                            foreach($pages as $page):?>
                                <p><a href="/admin/menuitems/add?type=static&alias=<?=$page['alias']?>"><?=$page['name']?></a></p>
                            <?php endforeach;
                        endif;?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>