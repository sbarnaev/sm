<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1><?php echo System::Lang('FORUM_TOPICS');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    <div class="nav_gorizontal">
        <ul>
            <li><a class="button-green-rounding" href="/admin/forum/topics/add">+ <?php echo System::Lang('ADD_TOPIC');?></a></li>
            <li><a class="button-yellow-rounding" href="/admin/forum/section"><?php echo System::Lang('FORUM_SECTION');?></a></li>
            <li><a class="button-yellow-rounding" href="/admin/forum/cats"><?php echo System::Lang('FORUM_CATS');?></a></li>
        </ul>
    </div>
    <div class="filter">
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Не возможно удалить!</div>'?>
<div class="overflow-container">
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Категория</th>
            <th>Автор</th>
            <th>Дата создания</th>
            <th class="td-last">Act</th>
        </tr>
    </thead>
    <tbody>
        <?php if($topic_list){
            foreach($topic_list as $topic):?>
        <tr<?php if($topic['status'] == 0) echo ' class="off"';?>>
            <td><?php echo $topic['topic_id'];?></td>
            <td class="item_name"><a href="/admin/forum/viewtopic/<?php echo $topic['topic_id'];?>"><?php echo $topic['topic_title'];?></a><br />
            <a class="small" href="/admin/forum/topics/edit/<?php echo $topic['topic_id'];?>" >Изменить</a></td>
            <td><?php $cat = Forum::getCatDataByID($topic['cat_id']); echo $cat['name'];?></td>
            <td><?php $user = User::getUserDataForAdmin($topic['user_id']); echo $user['user_name'];?></td>
            <td><?php echo date("d-m-Y H:i:s", $topic['create_date']);?></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/forum/topics/del/<?php echo $topic['topic_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach; } else echo 'Пока нет ни одной темы';?>
     </tbody>
    </table>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>