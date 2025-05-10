<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1><?=System::Lang('CREATE_USER_GROUP');?></h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?>
            </a><a href="/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/users/">Пользователи</a></li>
        <li><a href="/admin/usergroups/">Группы пользователей</a></li>
        <li>Добавить группу</li>
    </ul>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div><img src="/template/admin/images/icons/group-user.svg" alt=""></div>

                <div>
                    <h3 class="traning-title mb-0">Новая группа</h3>
                    <p class="mt-0">для пользователей</p>
                </div>
            </div>

            <ul class="nav_button">
                <li><input type="submit" name="save" value="Добавить" class="button save button-white font-bold"></li>
                <li class="nav_button__last">
                    <a class="button red-link" href="/admin/usergroups/">Закрыть</a>
                </li>
            </ul>
        </div>

        <div class="tabs">
            <ul>
                <li>Основное</li>
                <!--  РАСШИРЕНИЕ TELEGRAM-->
                <?php if (System::CheckExtensension('telegram', 1)):?>
                    <li>События</li>
                <?php endif;?>
            </ul>

            <div class="admin_form">
                <div>
                    <div class="row-line">
                        <div class="col-1-2">
                            <p class="width-100"><label><?=System::Lang('USER_GROUP_TITLE');?>:</label>
                                <input type="text" value="" name="title" required="required">
                            </p>

                            <p class="width-100"><label>Системное имя (англ.): </label>
                                <input type="text" value="" name="name" required="required">
                            </p>

                            <p class="width-100"><label><?=System::Lang('USER_GROUP_DESC');?>:</label>
                                <textarea cols="40" rows="2" name="desc"></textarea>
                            </p>

                            <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                        </div>
                    </div>
                </div>

                <!--  РАСШИРЕНИЕ TELEGRAM-->
                <?php if (System::CheckExtensension('telegram', 1)):?>
                    <div>
                        <?php require_once(ROOT . '/extensions/telegram/views/users/addgroup.php');?>
                    </div>
                <?php endif;?>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>