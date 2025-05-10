<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Добавить нового участника</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a>
        <a href="/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><a href="/admin/membersubs/">Планы подписок</a></li>
        <li><a href="/admin/memberusers/">Участники</a></li>
        <li>Добавить нового участника</li>
    </ul>
    <form action="" method="POST" enctype="multipart/form-data">

        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Добавить нового участника</h3>
            <ul class="nav_button">
                <li><input type="submit" name="add" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/memberusers/">Закрыть</a></li>
            </ul>
        </div>
        
        <div class="admin_form">
            <!-- 1 вкладка -->
                <div class="row-line">
                <div class="col-1-2">
                    <p><label>ID пользователя: </label><input type="text" name="user_id" placeholder="Введите ID юзера" required="required">
                        <a class="search-user" target="_blank" href="/admin/users">Найти</a></p>
                    
                    <div><label>Добавить подписку:</label>
                        <div class="select-wrap">
                        <select name="plane">
                    <?php $planes = Member::getPlanes();
                    foreach($planes as $plane):?>
                    <option value="<?php echo $plane['id']?>"><?php echo $plane['name']?></option>
                    <?php endforeach;?>
                    </select>
                        </div>
                    </div>
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>