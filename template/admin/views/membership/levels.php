<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1><?php echo System::Lang('MEMBER_LEVELS');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a>   | <a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    <div class="nav_gorizontal">
        <ul>
            <li><a href="/admin/memberlevels/add">+ <?php echo System::Lang('CREATE_MEMBER_LEVEL');?></a></li>
            <li><a href="/admin/membersubs/add">+ <?php echo System::Lang('CREATE_PLANE');?></a></li>
        </ul>
    </div>
    <div class="filter">
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Не возможно удалить!</div>'?>
    <table class="table">
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Дата создания</th>
            <th>Act</th>
        </tr>
        <?php if($levels){
            foreach($levels as $level):?>
        <tr>
            <td><?php echo $level['id']; ?></td>
            <td><a href="/admin/membersubs/edit/<?php echo $level['id']; ?>"><?php echo $level['name']; ?></a></td>
            <td><?php echo $level['create_date']; ?></td>
            <td><a onclick="return confirm('Вы уверены?')" href="/admin/membersubs/delete/<?php echo $level['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><img src="/template/admin/images/del.png" alt="Delete"></a></td>
        </tr>
        <?php endforeach;
        } else {
            echo 'Вы пока не добавили уровни доступа';
        }?>
        
    </table>

    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>