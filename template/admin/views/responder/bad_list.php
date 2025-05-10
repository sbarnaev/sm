<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('DELIVERY_LIST');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Список некорректных Email</li>
    </ul>

<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
<table class="table">
    <thead>
        <tr>
            <th>ID пользователя</th>
            <th class="text-left">Email</th>
        </tr>
    </thead>
    <tbody>
        <?php if($bad_list){
            $bad_list = explode(",", $bad_list);
            foreach($bad_list as $bad_user):?>
        <tr>
            <td><a href="/admin/users/edit/<?=$bad_user?>"><?=$bad_user?></td>
            <td class="text-left"><?=User::getUserNameByID($bad_user)['email'];?></a></td>
        </tr>
       <?php endforeach;};?>
    </tbody>
    </table>
</div>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>