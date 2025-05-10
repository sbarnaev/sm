<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1><?php echo System::Lang('PROF_LIST');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    <div class="nav_gorizontal">
        <ul>
            <li><a class="button-green-rounding" href="/admin/courses/addprof">+ <?php echo System::Lang('ADD_PROFFESSION');?></a></li>
            <li><a class="button-green-rounding" href="/admin/courses/add">+ <?php echo System::Lang('CREATE_COURSE');?></a></li>
            <li><a class="button-yellow-rounding" href="/admin/courses/cats"><?php echo System::Lang('CAT_LIST');?></a></li>
            <li><a class="button-yellow-rounding" href="/admin/courses"><?php echo System::Lang('COURSES_LIST');?></a></li>
        </ul>
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">К данной професии принадлежат курсы, удалить нельзя!</div>'?>
    <table class="table">
        <?php if($prof_list){?>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Act</th>
        </tr>

        <?php foreach($prof_list as $prof):?>
        <tr>
            <td><?php echo $prof['prof_id'];?></td>
            <td><a href="/admin/courses/profs/edit/<?php echo $prof['prof_id'];?>"><?php echo $prof['name'];?></a></td>
            <td><a onclick="return confirm('Вы уверены?')" href="/admin/courses/delprof/<?php echo $prof['prof_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><img src="/template/admin/images/del.png" alt="Delete"></a></td>
        </tr>
        <?php endforeach;
        } else echo 'У вас ещё не создано профессий'; ?>
        
    </table>

    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>