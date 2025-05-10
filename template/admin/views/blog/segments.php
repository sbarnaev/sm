<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1><?php echo System::Lang('SEGMENT_LIST');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    <div class="nav_gorizontal">
        <ul>
            <li><a class="button-green-rounding" href="/admin/segments/add">+ <?php echo System::Lang('ADD_SEGMENT');?></a></li>
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
            <th class="td-last">Act</th>
        </tr>
    </thead>
    <tbody>
        <?php if($list){
            foreach($list as $item):?>
        <tr>
            <td><?php echo $item['sid'];?></td>
            <td><a href="/admin/segments/edit/<?php echo $item['sid'];?>"><?php echo $item['name'];?></a></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/segments/delete/<?php echo $item['sid'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>

        <?php endforeach; } else echo 'Вы ещё не добавили ни одного сегмента';?>
        </tbody>
    </table>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>