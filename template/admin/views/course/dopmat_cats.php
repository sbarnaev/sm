<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1><?php echo System::Lang('CAT_LIST');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    <div class="nav_gorizontal">
        <ul>
            <li>
                <form class="nav-row" action="" method="POST"><input class="dop_cat" type="text" name="cat_name" >
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                    <input class="button-green-rounding" type="submit" name="addcat" value="Создать категорию">
                </form>
            </li>
            <li>
                <a class="button-red-rounding" href="/admin/dopmat">назад к Доп.материалам</a>
            </li>
        </ul>
    </div>
    
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Категория содержит материалы, удалить не возможно!</div>'?>
    <?php if($cat_list){?>
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
    <?php foreach($cat_list as $cat):?>
        <tr>
            <td><?php echo $cat['cat_id'];?></td>
            <td><?php echo $cat['name'];?></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/dopmat/delcat/<?php echo $cat['cat_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;?>
    </tbody>
    </table>
    </div>
    <?php } else echo 'Пока нет категорий'; ?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>