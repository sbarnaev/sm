<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1><?php echo System::Lang('DOPMAT');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    <div class="nav_gorizontal">
        <ul>
            <li><a class="button-green-rounding" href="/admin/dopmat/add">+ <?php echo System::Lang('ADD_DOPMAT');?></a></li>
            <li><a class="button-yellow-rounding" href="/admin/dopmat/cat"><?php echo System::Lang('DOPMAT_CAT');?></a></li>
        </ul>
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
	<?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Ошибка загрузки!</div>'?>
    <div class="overflow-container">
        <?php if($dopmat_list){?>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Название</th>
            <th>Категория</th>
            <th>Файл</th>
            <th class="td-last">Act</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach($dopmat_list as $dopmat):?>
        <tr>
            <td><?php echo $dopmat['mat_id'];?></td>
            <td><a href="/admin/dopmat/edit/<?php echo $dopmat['mat_id'];?>"><?php echo $dopmat['name'];?></a></td>
            <td><?php echo Course::getDopmatCatName($dopmat['cat_id']);?></td>
            <td><?php if($dopmat['file'] != null):?><a target="_blank" href="/load/dopmat/<?php echo $dopmat['file'];?>"><span title="<?php echo $dopmat['file'];?>"><?php $data = pathinfo('load/dopmat/'.$dopmat['file']); echo strtoupper($data['extension']);?></span></a><?php endif;?></td>
            <td class="td-last"><a onclick="return confirm('Вы уверены?')" href="/admin/dopmat/del/<?php echo $dopmat['mat_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><img src="/template/admin/images/del.png" alt="Delete"></a></td>
        </tr>
        <?php endforeach;?>
    </tbody>
    </table>
        <?php } else echo 'Вы ещё не добавили дополнительных материалов';?>
    </div>

    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>