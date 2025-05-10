<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Список меток отзывов</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>

    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/reviews/">Отзывы</a>
        </li>
        <li>Список меток отзывов</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/reviews/addlabel/">Создать метку</a></li>
        </ul>
    </div>

    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <div class="admin_form admin_form--margin-top">
        <div class="overflow-container">
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th class="text-left">Название</th>
                        <th class="text-left">Дата</th>
                        <th class="td-last"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if($list_labels){
                        foreach($list_labels as $label):?>
                    <tr>
                        <td><?php echo $label['label_id'];?></td>
                        <td class="text-left"><a href="/admin/reviews/editlabel/<?php echo $label['label_id'];?>"><?php echo $label['label_name'];?></a></td>
                        <td class="text-left"><?php echo $label['create_date'];?></td>
                        <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/admin/reviews/dellabel/<?php echo $label['label_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
                    </tr>
                    <?php endforeach;
                    } else echo 'Меток пока нет';?>
                    </tbody>
                </table>
            </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>