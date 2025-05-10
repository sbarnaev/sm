<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Разделы галереи</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    <div class="nav_gorizontal">
        <ul>
            <li><a class="button-green-rounding" href="<?php echo $setting['script_url'];?>/admin/gallery/addsection">+ Создать раздел</a></li>
            <li><a class="button button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/gallery/cats">Категории</a></li>
        </ul>
    </div>
    
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Раздел не может буть удален, в нём содержаться категории!</div>'?>
<div class="overflow-container">
<table class="table table-striped">
    <thead>
        <tr>
            <th>Обложка</th>
            <th>Раздел</th>
            <th>Кол-во категорий</th>
            <th class="td-last">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if($section_list){
        foreach($section_list as $section):?>
        <tr<?php if($section['status'] == 0) echo ' class="off"'; ?>>
            <td style="width: 25%;"><?php if(!empty($section['cover'])) echo '<img width="200" src="/images/gallery/cats/'.$section['cover'].'" alt="">';?></td>
            <td style="width: 30%;"><a href="/admin/gallery/editsection/<?php echo $section['id'];?>"><?php echo $section['name'];?></a></td>
            <td><?php $count = Gallery::countCat($section['id']); echo $count;?></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('<?php echo System::Lang('YOU_SHURE');?>?')" href="<?php echo $setting['script_url'];?>/admin/gallery/delsection/<?php echo $section['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="<?php echo System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;
        } else echo 'No sections'; ?>
    </tbody>
    </table>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>