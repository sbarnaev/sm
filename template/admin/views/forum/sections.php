<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1><?php echo System::Lang('FORUM_SECTION');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    <div class="nav_gorizontal">
        <ul>
            <li><a class="button-green-rounding" href="/admin/forum/addsection">+ <?php echo System::Lang('ADD_SECTION');?></a></li>
            <li><a class="button-yellow-rounding" href="/admin/forum/cats"><?php echo System::Lang('FORUM_CATS');?></a></li>
        </ul>
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Не возможно удалить!</div>'?>
 <div class="overflow-container">
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Дата создания</th>
            <th class="td-last">Act</th>
        </tr>
        </thead>
   <tbody>
        <?php if($sections){
            foreach($sections as $section):?>
        <tr<?php if($section['status'] == 0) echo ' class="off"';?>>
            <td><?php echo $section['section_id'];?></td>
            <td class="item_name"><a href="/admin/forum/editsection/<?php echo $section['section_id'];?>"><?php echo $section['name'];?></a></td>
            <td><?php echo $section['create_date'];?></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/forum/delsection/<?php echo $section['section_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;} else echo 'Пока нет разделов';?>
    </tbody>
    </table>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>