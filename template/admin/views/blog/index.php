<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('POST_LIST');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Список записей блога</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="/admin/blog/add/"><?php echo System::Lang('ADD_POST');?></a></li>

            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent nav_gorizontal__parent-yellow">
                    <a href="javascript:void(0);" class="nav-click button-yellow-rounding">Категории</a>
                    <span class="nav-click icon-arrow-down"></span>
                </div>
                <ul class="drop_down">
                    <li><a href="/admin/rubrics/add/">Создать категорию</a></li>
                    <li><a href="/admin/rubrics/">Список категорий</a></li>
                </ul>
            </li>
            <li><a title="Общие настройки блога" class="settings-link" target="_blank" href="/admin/blogsetting/"><i class="icon-settings"></i></a></li>

        </ul>
    </div>

    <div class="filter admin_form">
        <form action="" method="POST">
            <div class="filter-row filter-flex-end">
                <div class="max-width-147">
                    <input type="text" name="title" placeholder="Поиск">
                </div>
                <div class="max-width-147 max-width-none">
                    <div class="select-wrap">
                        <select name="cat_id">
                            <option value="0">Категория</option>
                            <?php $cat_list = Blog::getRubricList();
                            if($cat_list):
                            foreach($cat_list as $cat):?>
                                <option value="<?php echo $cat['id']?>"><?php echo $cat['name']?></option>
                            <?php endforeach; 
                            endif;?>
                        </select>
                    </div>
                </div>
                <div class="max-width-147 mr-auto">
                    <div class="select-wrap">
                        <select name="status">
                            <option value="2">Статус</option>
                            <option value="1">Опубликовано</option>
                            <option value="0">Скрыто</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="button-group">
                        <input class="button-blue-rounding" type="submit" name="filter" value="Найти">
                        <a class="red-link" href="/admin/blog/">Сбросить</a>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Не возможно удалить!</div>'?>
<div class="admin_form admin_form--margin-top">
    <!--
    <div class="d-flex flex-right mb-30">
    <button class="button-blue-rounding">Сохранить сортировку</button>
    </div>
    -->
<div class="overflow-container">
<table class="table">
    <thead>
        <tr>
            <!-- <th>SORT</th> -->
            <? /* <th>Обложка</th> */ ?>
            <th class="text-left">Название</th>
            <th class="text-left">Категория</th>
            <th>Хиты</th>
            <th class="td-last">Act</th>
        </tr>
        </thead>
    <tbody>
        <?php if($post_list){
            foreach($post_list as $post):?>
        <tr<?php if($post['status'] == 0) echo ' class="off"';?>>
            <!-- <td>
                <input class="input-sort" type="text" value="">
            </td> -->
        <? /* <td class="tbl_img"><img src="/images/post/cover/<?php echo $post['post_img'];?>" alt=""></td> */ ?>
            <td class="text-left"><a href="/admin/blog/edit/<?php echo $post['post_id'];?>"><?php echo $post['name'];?></a></td>
            <td class="text-left"><?php echo Blog::getRubricName($post['rubric_id']);?></td>
            <td><?php echo $post['hits'];?></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/blog/del/<?php echo $post['post_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;} else {echo 'Вы ешё не добавили записей';}?>
      </tbody>
    </table>
</div>
</div>
    <?php if($is_pagination == true) echo $pagination->get(); ?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>