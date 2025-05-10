<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Отправить массовое письмо</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/responder/mass/">Массовые рассылки</a></li>
        <li>Отправить массовое письмо</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Отправить массовое письмо</h3>
            <ul class="nav_button">
                <li><input type="submit" name="add" value="Отправить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/responder/mass/">Закрыть</a></li>
            </ul>
        </div>
        <div class="admin_form">
            <!-- 1 вкладка -->
            <div>
                <div class="row-line">
                    <div class="col-1-2">
                        <h4>Основное</h4>
                        <p class="width-100"><label>Название (для админа): </label><input type="text" name="name" placeholder="Название" required="required"></p>
                        <p class="width-100"><label>Описание: </label><textarea name="desc" cols="45" rows="4"></textarea></p>
                        <input type="hidden" name="type" value="1">
                        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                    </div>

                    <div class="col-1-2">
                        <h4>Отправка</h4>
                        <p class="width-100"><label>Время отправки:</label><input type="text" class="datetimepicker" name="send" autocomplete="off"></p>
                        <p class="width-100"><label>Задача, цель письма:</label><br /><textarea name="target" cols="55" rows="4"></textarea></p>
                    </div>

                    <div class="col-1-1">
                        <h4>Письмо:</h4>
                        <p class="width-100"><label>Тема письма: </label><input type="text" name="subject" placeholder="Тема письма" required="required"></p>
                        <p class="width-100"><textarea class="editor" name="letter"></textarea></p>
                        <div class="width-100">
                            <div class="tags_letter">
                            <p><strong>Теги для подстановки:</strong></p>
                            <p>[NAME] - имя подписчика<br>[UNSUBSCRIBE] - ссылка на отписку</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="gray-block">
                    <div class="row-line">
                        <div class="col-1-1">

                            <h2>Выбор адресатов</h2>
                            <div class="row-line">
                                <div class="col-1-2">
                                    <h4>Включить типы:</h4>
                                    <div class="width-100"><label>Типы пользователей: </label>

                                        <select class="multiple-select" name="user_types[]" multiple="multiple">
                                            <option value="all">Все</option>
                                            <option value="is_client">Клиенты</option>
                                            <option value="is_partner">Партнёры</option>
                                            <option value="is_author">Авторы</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-1-2">
                                    <h4 class="red">Исключить типы:</h4>
                                    <div class="width-100"><label>Типы пользователей: </label>

                                        <select class="multiple-select" name="ex_user_types[]" multiple="multiple">
                                            <option value="all">Все</option>
                                            <option value="is_client">Клиенты</option>
                                            <option value="is_partner">Партнёры</option>
                                            <option value="is_author">Авторы</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-1-2">
                                    <h4>Включить группы</h4>
                                    <div class="width-100"><label>Типы пользователей: </label>

                                        <select class="multiple-select" name="user_groups[]" multiple="multiple">
                                            <?php $user_groups = User::getUserGroups();
                        if($user_groups):
                        foreach($user_groups as $user_group):?>
                                            <option value="<?php echo $user_group['group_id'];?>"><?php echo $user_group['group_title'];?></option>
                                            <?php endforeach;
                        endif; ?>
                                        </select>

                                    </div>
                                </div>


                                <div class="col-1-2">
                                    <h4 class="red">Исключить группы</h4>
                                    <div><label>Типы пользователей: </label>
                                        <select class="multiple-select" name="ex_user_groups[]" multiple="multiple">
                                            <?php $user_groups = User::getUserGroups();
                        if($user_groups):
                        foreach($user_groups as $user_group):?>
                                            <option value="<?php echo $user_group['group_id'];?>"><?php echo $user_group['group_title'];?></option>
                                            <?php endforeach;
                        endif; ?>
                                        </select></div>
                                </div>

                                <?php $responder = System::CheckExtensension('responder', 1);
                    if($responder): ?>

                                <div class="col-1-2">
                                    <h4>Включить по email рассылке</h4>
                                    <div class="width-100"><label>Рассылки: </label>
                                        <select class="multiple-select" name="user_delivery[]" multiple="multiple">
                                            <?php $user_subs = Responder::getDeliveryList(2, 1,100);
                        if($user_subs):
                        foreach($user_subs as $sub):?>
                                            <option value="<?php echo $sub['delivery_id'];?>"><?php echo $sub['name'];?></option>
                                            <?php endforeach;
                        endif; ?>
                                        </select></div>
                                </div>

                                <div class="col-1-2">
                                    <h4>Исключить по email рассылке</h4>
                                    <div class="width-100"><label>Рассылки: </label>
                                        <select class="multiple-select" name="ex_user_delivery[]" multiple="multiple">
                                            <?php $user_subs = Responder::getDeliveryList(2, 1, 100);
                        if($user_subs):
                        foreach($user_subs as $sub):?>
                                            <option value="<?php echo $sub['delivery_id'];?>"><?php echo $sub['name'];?></option>
                                            <?php endforeach;
                        endif; ?>
                                        </select></div>
                                </div>
                                <?php endif;?>


                                <?php $membership = System::CheckExtensension('membership', 1);
                    if($membership): ?>

                                <div class="col-1-2">
                                    <h4>Включить по плану подписки</h4>
                                    <div class="width-100"><label>Планы: </label>

                                        <select class="multiple-select" name="user_subs[]" multiple="multiple">
                                            <?php $user_subs = Member::getPlanes();
                                            if($user_subs):
                                                foreach($user_subs as $sub):?>
                                                    <option value="<?php echo $sub['id'];?>"><?php echo $sub['name'];?></option>
                                            <?php endforeach;
                                            endif; ?>
                                        </select></div>
                                </div>

                                <div class="col-1-2">
                                    <h4 class="red">Исключить по плану подписки</h4>
                                    <div class="width-100"><label>Планы: </label>
                                        <select class="multiple-select" name="ex_user_subs[]" multiple="multiple">
                                            <?php $user_subs = Member::getPlanes();
                                                if($user_subs):
                                                foreach($user_subs as $sub):?>
                                                    <option value="<?php echo $sub['id'];?>"><?php echo $sub['name'];?></option>
                                                <?php endforeach;
                                                endif; ?>
                                        </select></div>
                                </div>
                                <?php endif; ?>


                                <?php $blog = System::CheckExtensension('sllllllllllllog', 1);
                                if($blog): ?>
                                <div class="col-1-2">
                                    <h4>Включить по сегменту</h4>
                                    <div class="width-100"><label>Сегменты: </label>
                                        <select class="multiple-select" name="user_segments[]" multiple="multiple">
                                            <?php $user_segmets = Blog::getSegmentsList();
                                            if($user_segmets):
                                                foreach($user_segmets as $segment):?>
                                                    <option value="<?php echo $segment['sid'];?>"><?php echo $segment['name'];?></option>
                                            <?php endforeach;
                                            endif; ?>
                                        </select></div>
                                    <div class="width-100"><label>Вероятность:</label>
                                        <div class="select-wrap">
                                            <select name="chance">
                                                <option value="0">Любая</option>
                                                <option value="1">выше 10%</option>
                                                <option value="2">выше 20%</option>
                                                <option value="3">выше 30%</option>
                                                <option value="4">выше 40%</option>
                                                <option value="5">выше 50%</option>
                                                <option value="6">выше 60%</option>
                                                <option value="7">выше 70%</option>
                                                <option value="8">выше 80%</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-1-2">
                                    <h4 class="red">Исключить по сегменту</h4>
                                    <div class="width-100"><label>Сегменты: </label>
                                        <select class="multiple-select" name="ex_user_segments[]" multiple="multiple">
                                            <?php $user_segmets = Blog::getSegmentsList();
                        if($user_segmets):
                        foreach($user_segmets as $segment):?>
                                            <option value="<?php echo $segment['sid'];?>"><?php echo $segment['name'];?></option>
                                            <?php endforeach;
                        endif; ?>
                                        </select></div>
                                </div>

                            <?php endif;?>
                        </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
jQuery('.datetimepicker').datetimepicker({
format:'d.m.Y H:i',
lang:'ru'
});
</script>
</body>
</html>