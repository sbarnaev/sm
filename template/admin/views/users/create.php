<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('CREATE_USER');?></h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/users/">Пользователи</a>
        </li>
        <li>Добавить пользователя</li>
    </ul>
    <?php if(isset($_GET['success'])):?>
    <div class="admin_message">Сохранено!</div>
    <?php endif;?>
    
    <?php if(isset($error_msg)):?>
        <div class="admin_warning"><?php echo $error_msg;?></div>
    <?php endif;?>
    
    <form action="" method="POST" enctype="multipart/form-data">

        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/user-add.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0"><?php echo System::Lang('CREATE_USER');?></h3>
                </div>
            </div>
            <ul class="nav_button">
                <li><input type="submit" name="create" value="<?php echo System::Lang('SAVE');?>" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/users/">Закрыть</a></li>
            </ul>
        </div>


    <div class="admin_form">


            <h4 class="h4-border"><?php echo System::Lang('BASIC');?></h4>

            <div class="row-line">
                <div class="col-1-2">
                    <p class="width-100"><label>Имя: </label><input type="text" name="name" required="required"></p>
                    <?php if($setting['show_surname'] > 0):?>
                        <p class="width-100"><label>Фамилия:</label><input type="text" name="surname"></p>
                    <?php endif;?>
                    <p class="width-100"><label>Логин (для админов): </label><input type="text" name="login"></p>
                    <p class="width-100"><label>E-mail: </label><input type="text" name="email" required="required"></p>
                    <p class="width-100"><label>Пароль: </label><input type="text" name="pass"></p>
                    <div class="width-100"><label>Статус: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" checked=""><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0"><span>Откл</span></label>
                        </span>
                    </div>
                </div>
            <div class="col-1-2">

                <p class="width-100"><label>Телефон: </label><input type="text" name="phone"></p>
                <p class="width-100"><label>Город: </label><input type="text" name="city"></p>
                <p class="width-100"><label>Индекс: </label><input type="text" name="zipcode" ></p>
                <p class="width-100"><label>Адрес: </label><textarea name="address" cols="45" rows="3"></textarea></p>

                <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                <input type="hidden" name="method" value="handmade">
            </div>
            </div>
            <h4 class="h4-border"><?php echo System::Lang('USER_DOP_INFO');?></h4>
            <div class="row-line">
            <div class="col-1-2">
                <div class="width-100"><label>Уровень: </label>
                    <div class="select-wrap">
                        <select name="role">
                        <option value="user">Пользователь</option>
                        <option value="manager">Менеджер</option>
                        <option value="admin">Админ</option>
                    </select>
                    </div>
                </div>
                <div class="width-100"><label>Клиент?</label>
                    <div class="select-wrap">
                    <select name="is_client">
                    <option value="0">Нет</option>
                    <option value="1">Да</option>
                </select>
                    </div>
                </div>
                <div class="width-100"><label>Отправить письмо с паролем?</label>
                    <div class="select-wrap">
                        <select name="send_login">
                            <option value="0">Нет</option>
                            <option value="1">Да</option>
                        </select>
                    </div>
                </div>
            </div>
                <div class="col-1-2">
                        <label>Группы</label>
                        <?php $user_group_list = User::getUserGroups();
                        if($user_group_list):
                    foreach($user_group_list as $user_group):?>
                        <div>
                            <label class="custom-chekbox-wrap" for="<?php echo $user_group['group_name'];?>">
                                <input type="checkbox" id="<?php echo $user_group['group_name'];?>" name="groups[]"
                                       value="<?php echo $user_group['group_id'];?>"><span class="custom-chekbox"></span> <?php echo $user_group['group_title'];?>
                            </label>
                        </div>
                        <?php endforeach;
                        endif?>

                </div>
            </div>
    </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>