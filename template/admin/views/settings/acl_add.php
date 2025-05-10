<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Создать права менеджера</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/permissions/">Права доступа менеджеров</a></li>
        <li>Создать права менеджера</li>
    </ul>
    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Права доступа менеджеров</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addperm" value="Создать" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/permissions/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <div class="width-100"><label>Выберите менеджера: </label>
                    <div class="select-wrap">
                        <select name="user_id" required="required">
                        <option value="">- Выбрать -</option>
                    <?php $manager_list = User::getManagerList();
                    if($manager_list):
                    foreach($manager_list as $manager):?>
                        <option value="<?php echo $manager['user_id'];?>"><?php echo $manager['user_name'];?></option>
                        <?php endforeach;
                        endif;?>
                        </select>
                    </div>
                    </div>
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
            </div>
            
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Разрешить доступ:</h4>
                    <div class="width-100">
                    
                    
                    <label class="custom-chekbox-wrap" for="show_orders">
                        <input type="checkbox" id="show_orders" name="perm[show_orders]"
                               value="1"><span class="custom-chekbox"></span> Заказы 
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="show_products">
                        <input type="checkbox" id="show_products" name="perm[show_products]"
                               value="1"><span class="custom-chekbox"></span> Продукты 
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="show_courses">
                        <input type="checkbox" id="show_courses" name="perm[show_courses]"
                               value="1"><span class="custom-chekbox"></span> Онлайн курсы 
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="show_users">
                        <input type="checkbox" id="show_users" name="perm[show_users]"
                               value="1"><span class="custom-chekbox"></span> Пользователи
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="show_conditions">
                        <input type="checkbox" id="show_conditions" name="perm[show_conditions]"
                               value="1"><span class="custom-chekbox"></span> Условия
                    </label>
                    
                    <?php $membership = System::CheckExtensension('membership', 1);
                    if($membership):?>
                    <label class="custom-chekbox-wrap" for="show_member">
                        <input type="checkbox" id="show_member" name="perm[show_member]"
                               value="1"><span class="custom-chekbox"></span> Мембершип
                    </label>
                    <?php endif;?>
                    
                    <?php $blog = System::CheckExtensension('blog', 1);
                    if($blog):?>
                    <label class="custom-chekbox-wrap" for="show_blog">
                        <input type="checkbox" id="show_blog" name="perm[show_blog]"
                               value="1"><span class="custom-chekbox"></span> Блог
                    </label>
                    <?php endif;?>
                    
                    <label class="custom-chekbox-wrap" for="show_feedback">
                        <input type="checkbox" id="show_feedback" name="perm[show_feedback]"
                               value="1"><span class="custom-chekbox"></span> Обратная связь
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="show_pages">
                        <input type="checkbox" id="show_pages" name="perm[show_pages]"
                               value="1"><span class="custom-chekbox"></span> Статичные страницы
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="show_rdr">
                        <input type="checkbox" id="show_rdr" name="perm[show_rdr]"
                               value="1"><span class="custom-chekbox"></span> Редиректы
                    </label>
                    
                    <?php $responder = System::CheckExtensension('responder', 1);
                    if($responder):?>
                    <label class="custom-chekbox-wrap" for="show_responder">
                        <input type="checkbox" id="show_responder" name="perm[show_responder]"
                               value="1"><span class="custom-chekbox"></span> Email рассылка
                    </label>
                    <?php endif;?>
                    
                    <?php $forum = System::CheckExtensension('forum2', 1);
                    if($forum):?>
                    <label class="custom-chekbox-wrap" for="show_forum">
                        <input type="checkbox" id="show_forum" name="perm[show_forum]"
                               value="1"><span class="custom-chekbox"></span> Форум
                    </label>
                    <?php endif;?>
                    
                    <label class="custom-chekbox-wrap" for="show_channel">
                        <input type="checkbox" id="show_channel" name="perm[show_channel]"
                               value="1"><span class="custom-chekbox"></span> Каналы трафика
                    </label>
                    
                    
                    
                    
                    <?php $partnership = System::CheckExtensension('partnership', 1);
                    if($partnership):?>
                    <label class="custom-chekbox-wrap" for="show_aff">
                        <input type="checkbox" id="show_aff" name="perm[show_aff]"
                               value="1"><span class="custom-chekbox"></span> Партнёрка
                    </label>
                    <?php endif;?>
                    
                    <label class="custom-chekbox-wrap" for="show_widgets">
                        <input type="checkbox" id="show_widgets" name="perm[show_widgets]"
                               value="1"><span class="custom-chekbox"></span> Виджеты 
                    </label>

                    <label class="custom-chekbox-wrap" for="show_main_tunes">
                        <input type="checkbox" id="show_main_tunes" name="perm[show_main_tunes]"
                               value="1"><span class="custom-chekbox"></span> Главные настройки 
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="show_payment_tunes">
                        <input type="checkbox" id="show_payment_tunes" name="perm[show_payment_tunes]"
                               value="1"><span class="custom-chekbox"></span> Настройки платёжных систем
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="show_ext_tunes">
                        <input type="checkbox" id="show_ext_tunes" name="perm[show_ext_tunes]"
                               value="1"><span class="custom-chekbox"></span> Расширения
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="show_backups">
                        <input type="checkbox" id="show_backups" name="perm[show_backups]"
                               value="1"><span class="custom-chekbox"></span> Бэкапы
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="show_stat">
                        <input type="checkbox" id="show_stat" name="perm[show_stat]"
                               value="1"><span class="custom-chekbox"></span> Статистика
                    </label>

                    <label class="custom-chekbox-wrap" for="export_users">
                      <input type="checkbox" id="export_users" name="perm[export_users]"
                             value="1"><span class="custom-chekbox"></span> Экспорт пользователей
                    </label>



                    </div>
                </div>
                
                
                <div class="col-1-2">
                    <h4>Разрешить изменение:</h4>
                    <div class="width-100">
                    
                        <label class="custom-chekbox-wrap" for="change_orders">
                            <input type="checkbox" id="change_orders" name="perm[change_orders]"
                                   value="1"><span class="custom-chekbox"></span> Заказы 
                        </label>
                        
                        <label class="custom-chekbox-wrap" for="change_products">
                        <input type="checkbox" id="change_products" name="perm[change_products]"
                               value="1"><span class="custom-chekbox"></span> Продукты 
                        </label>
                        
                        <label class="custom-chekbox-wrap" for="change_courses">
                            <input type="checkbox" id="change_courses" name="perm[change_courses]"
                                   value="1"><span class="custom-chekbox"></span> Онлайн курсы 
                        </label>
                        
                        <label class="custom-chekbox-wrap" for="change_users">
                            <input type="checkbox" id="change_users" name="perm[change_users]"
                                   value="1"><span class="custom-chekbox"></span> Пользователи
                        </label>
                        
                        <label class="custom-chekbox-wrap" for="change_conditions">
                            <input type="checkbox" id="change_conditions" name="perm[change_conditions]"
                                   value="1"><span class="custom-chekbox"></span> Условия
                        </label>
                        
                        <?php if($membership):?>
                        <label class="custom-chekbox-wrap" for="change_member">
                            <input type="checkbox" id="change_member" name="perm[change_member]"
                                   value="1"><span class="custom-chekbox"></span> Мембершип
                        </label>
                        <?php endif;?>
                        
                        <?php if($blog):?>
                        <label class="custom-chekbox-wrap" for="change_blog">
                            <input type="checkbox" id="change_blog" name="perm[change_blog]"
                                   value="1"><span class="custom-chekbox"></span> Блог
                        </label>
                        <?php endif;?>
                        
                        <label class="custom-chekbox-wrap" for="change_feedback">
                            <input type="checkbox" id="change_feedback" name="perm[change_feedback]"
                                   value="1"><span class="custom-chekbox"></span> Обратная связь
                        </label>
                        
                        <label class="custom-chekbox-wrap" for="change_pages">
                            <input type="checkbox" id="change_pages" name="perm[change_pages]"
                                   value="1"><span class="custom-chekbox"></span> Статичные страницы
                        </label>
                        
                        <label class="custom-chekbox-wrap" for="change_rdr">
                            <input type="checkbox" id="change_rdr" name="perm[change_rdr]"
                                   value="1"><span class="custom-chekbox"></span> Редиректы
                        </label>
                        
                        <?php if($responder):?>
                        <label class="custom-chekbox-wrap" for="change_responder">
                            <input type="checkbox" id="change_responder" name="perm[change_responder]"
                                   value="1"><span class="custom-chekbox"></span> Email рассылка
                        </label>
                        <?php endif;?>
                        
                        <?php if($forum):?>
                        <label class="custom-chekbox-wrap" for="change_forum">
                            <input type="checkbox" id="change_forum" name="perm[change_forum]"
                                   value="1"><span class="custom-chekbox"></span> Форум
                        </label>
                        <?php endif;?>
                        
                        <label class="custom-chekbox-wrap" for="change_channel">
                            <input type="checkbox" id="change_channel" name="perm[change_channel]"
                                   value="1"><span class="custom-chekbox"></span> Каналы трафика
                        </label>
                        
                        
                        <?php if($partnership):?>
                        <label class="custom-chekbox-wrap" for="change_aff">
                            <input type="checkbox" id="change_aff" name="perm[change_aff]"
                                   value="1"><span class="custom-chekbox"></span> Партнёрка
                        </label>
                        <?php endif;?>
                        
                        
                        <label class="custom-chekbox-wrap" for="change_widgets">
                            <input type="checkbox" id="change_widgets" name="perm[change_widgets]"
                                   value="1"><span class="custom-chekbox"></span> Виджеты 
                        </label>
                        
                        <label class="custom-chekbox-wrap" for="change_main_tunes">
                            <input type="checkbox" id="change_main_tunes" name="perm[change_main_tunes]"
                                   value="1"><span class="custom-chekbox"></span> Главные настройки 
                        </label>
                    

                    
                    </div>
                </div>
            
                <div class="col-1-2">
                    <h4>Разрешить удаление:</h4>
                    <div class="width-100">
                    
                    
                    <label class="custom-chekbox-wrap" for="del_orders">
                        <input type="checkbox" id="del_orders" name="perm[del_orders]"
                               value="1"><span class="custom-chekbox"></span> Заказы 
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="del_products">
                        <input type="checkbox" id="del_products" name="perm[del_products]"
                               value="1"><span class="custom-chekbox"></span> Продукты 
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="del_courses">
                        <input type="checkbox" id="del_courses" name="perm[del_courses]"
                               value="1"><span class="custom-chekbox"></span> Онлайн курсы 
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="del_users">
                        <input type="checkbox" id="del_users" name="perm[del_users]"
                               value="1"><span class="custom-chekbox"></span> Пользователи
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="del_conditions">
                        <input type="checkbox" id="del_conditions" name="perm[del_conditions]"
                               value="1"><span class="custom-chekbox"></span> Условия
                    </label>
                    
                    <?php if($membership):?>
                    <label class="custom-chekbox-wrap" for="del_member">
                        <input type="checkbox" id="del_member" name="perm[del_member]"
                               value="1"><span class="custom-chekbox"></span> Мембершип
                    </label>
                    <?php endif;?>
                    
                    <?php if($blog):?>
                    <label class="custom-chekbox-wrap" for="del_blog">
                        <input type="checkbox" id="del_blog" name="perm[del_blog]"
                               value="1"><span class="custom-chekbox"></span> Блог
                    </label>
                    <?php endif;?>
                    
                    <label class="custom-chekbox-wrap" for="del_feedback">
                        <input type="checkbox" id="del_feedback" name="perm[del_feedback]"
                               value="1"><span class="custom-chekbox"></span> Обратная связь
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="del_pages">
                        <input type="checkbox" id="del_pages" name="perm[del_pages]"
                               value="1"><span class="custom-chekbox"></span> Статичные страницы
                    </label>
                    
                    <label class="custom-chekbox-wrap" for="del_rdr">
                        <input type="checkbox" id="del_rdr" name="perm[del_rdr]"
                               value="1"><span class="custom-chekbox"></span> Редиректы
                    </label>
                    
                    <?php if($responder):?>
                    <label class="custom-chekbox-wrap" for="del_responder">
                        <input type="checkbox" id="del_responder" name="perm[del_responder]"
                               value="1"><span class="custom-chekbox"></span> Email рассылка
                    </label>
                    <?php endif;?>
                    
                    <?php if($forum):?>
                    <label class="custom-chekbox-wrap" for="del_forum">
                        <input type="checkbox" id="del_forum" name="perm[del_forum]"
                               value="1"><span class="custom-chekbox"></span> Форум
                    </label>
                    <?php endif;?>
                    
                    <label class="custom-chekbox-wrap" for="del_channel">
                        <input type="checkbox" id="del_channel" name="perm[del_channel]"
                               value="1"><span class="custom-chekbox"></span> Каналы трафика
                    </label>
                    
                    </div>
                    
                    
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>