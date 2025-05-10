<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('AUTO_LETTERS_LIST'); echo ' : '. $delivery['name'];?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Cписок писем автосерий</li>
    </ul>
    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="/admin/responder/autoletters/<?php echo $id; ?>/add"><?php echo System::Lang('ADD_AUTO_LETTER');?></a></li>
      
        </ul>
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Не возможно удалить!</div>'?>

<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th class="text-left">Тема</th>
            <th>Отправка, часы</th>
            <th>Задача/Цель</th>
            <th class="td-last">Act</th>
        </tr>
    </thead>
    <tbody>
        <?php if($letter_list){
            foreach($letter_list as $letter):?>
        <tr<?php if($letter['status'] == 0) echo ' class="off"';?>>
            <td><?php echo $letter['letter_id'];?></td>
            <td class="text-left"><a href="/admin/responder/autoletters/<?php echo $id;?>/edit/<?php echo $letter['letter_id'];?>"><?php echo $letter['subject'];?></a></td>
            <td><?php echo $letter['send_time'];?></td>
            <td><img src="/template/admin/images/list.png" alt="" title="<?php echo $letter['target'];?>" style="cursor: help;"></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/responder/autoletters/<?php echo $delivery['delivery_id'];?>/del/<?php echo $letter['letter_id'];?>?token=<?php echo $_SESSION['admin_token'];?>&type=<?php echo $delivery['type'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;} else 'Пока нет писем для автосерии';?>
    </tbody>
    </table>
</div>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>