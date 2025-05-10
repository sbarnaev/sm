<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('CAT_LIST');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/courses/">Тренинги</a>
        </li>
        <li>Список категорий</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="/admin/courses/addcat/"><?php echo System::Lang('ADD_CATEGORY');?></a></li>
           <? /*
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="/admin/courses/add/"><?php echo System::Lang('CREATE_COURSE');?></a></li>
            <li><a class="button-yellow-rounding" href="/admin/courses/"><?php echo System::Lang('COURSES_LIST');?></a></li>
            */ ?>
        </ul>
    </div>

    <div class="filter">
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Категория содержит курсы, удалить не возможно!</div>'?>
    <div class="admin_form admin_form--margin-top">
        <div class="overflow-container">
            <table class="table">
                <?php if($cat_list){?>
                <thead>
                    <tr>
                        <th>ID</th>
                        <? /* <th>Обложка</th> */ ?>
                        <th class="text-left">Название</th>
                        <th class="td-last"></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach($cat_list as $cat):?>
                <tr<?php if($cat['status'] == 0) echo ' class="off"';?>>
                    <td><?php echo $cat['cat_id'];?></td>
                <? /* <td style="width: 20%;"><img src="/images/course/category/<?php echo $cat['cover'];?>" alt=""></td> */ ?>
                    <td class="text-left"><a href="/admin/courses/cats/edit/<?php echo $cat['cat_id'];?>"><?php echo $cat['name'];?></a></td>
                    <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/courses/delcat/<?php echo $cat['cat_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
                </tr>
                <?php endforeach;
                } else echo 'У вас ещё не категорий'; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>