<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('EMAIL_SUBSCRIBERS');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Подписчики рассылок</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="/admin/subscribers/add/"><?php echo System::Lang('ADD_SUBS');?></a></li>
            <li><a class="button-yellow-rounding" href="/admin/subscribers/import/"><?php echo System::Lang('IMPORT_SUBS');?></a></li>
        </ul>
    </div>

    <div class="filter admin_form">
        <form action="" method="post">
            <div class="search-row">
                <div>
                    <div class="select-wrap">
                        <select name="delivery">
                        <option value="0">По подписке</option>
                        <?php $delivery_list = Responder::getDeliveryList(2);
                        foreach($delivery_list as $delivery):?>
                        <option value="<?php echo $delivery['delivery_id'];?>"><?php echo $delivery['name'];?></option>
                        <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="mr-auto">
                    <input type="text" name="email" placeholder="По e-mail">
                </div>
                <div>
                    <div class="button-group">
                        <input class="button-blue-rounding" type="submit" name="filter" value="Фильтр">
                        <a class="red-link" href="/admin/subscribers">Сбросить</a>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Не возможно удалить!</div>'?>
    <div class="admin_form admin_form--margin-top">
        <p>Всего записей: <?php echo $total;?></p>
        <div class="overflow-container">
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th class="text-left">Имя</th>
                    <th class="text-left">Email</th>
                    <th class="text-left">Рассылка</th>
                    <th class="td-last"></th>
                </tr>
                </thead>
                <tbody>
                <?php if($subs_list){
                foreach($subs_list as $subs):?>
                <tr<?php if($subs['cancelled'] != 0 || $subs['confirmed'] == 0) echo ' class="off"';
            if($subs['spam'] != 0) echo ' class="refund"';?>>
                <td><?php echo $subs['id'];?></td>
                <td class="text-left"><?php $user = User::getUserDataByEmail($subs['email'], null);
                if($user) echo $user['user_name'];
                else echo $subs['subs_name'];?></td>
                <td class="text-left"><?php echo $subs['email'];?></td>
                <td class="text-left"><?php $deliver = Responder::getDeliveryData($subs['delivery_id']); echo $deliver['name'];?></td>
                <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/subscribers/del/<?php echo $subs['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
                </tr>
                <?php endforeach; } else echo 'Нет подписчиков';?>
                </tbody>
            </table>

        </div>
    </div>


    <?php if(isset($is_pagination) && $is_pagination == true) echo $pagination->get(); ?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>