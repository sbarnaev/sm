<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Список отзывов</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/products/">Продукты</a>
        </li>
        <li>Отзывы</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><div class="nav_gorizontal__parent nav_gorizontal__parent-yellow"><a class="button-yellow-rounding" href="<?php echo $setting['script_url'];?>/admin/reviewscat/">Категории отзывов</a><span class="nav-click icon-arrow-down"></span></div>

                <ul class="drop_down">
                    <li><a href="/admin/reviewscat/add/">Добавить категорию</a></li>
                    <li><a href="/admin/reviewscat/">Список категорий</a></li>
                </ul>

            </li>

            <li class="nav_gorizontal__parent-wrap"><div class="nav_gorizontal__parent nav_gorizontal__parent-yellow"><a class="button-yellow-rounding" href="<?php echo $setting['script_url'];?>/admin/reviews/labels/">Метки отзывов</a><span class="nav-click icon-arrow-down"></span></div>

                <ul class="drop_down">
                    <li><a href="/admin/reviews/addlabel/">Создать метку</a></li>
                    <li><a href="/admin/reviews/labels/">Список меток</a></li>
                </ul>

            </li>

        </ul>
    </div>

    <div class="filter admin_form">
        <form action="" method="POST">
            <div class="filter-row">
                <div>
                    <div class="select-wrap">
                        <select name="cat_id">
                        <option value="">Категория</option>
                        <?php $cat_list = Product::getReviewsCats();
                        if($cat_list):
                        foreach($cat_list as $cat):?>
                            <option value="<?php echo $cat['cat_id']?>"><?php echo $cat['cat_name']?></option>
                        <?php endforeach;
                        endif;
                        ?>
                        </select>
                    </div>
                </div>
                
                <div class="max-width-120 mr-auto">
                    <div class="select-wrap">
                        <select name="status">
                            <option value="">Статус</option>
                            <option value="0">Отключен</option>
                            <option value="1">Включен</option>
                        </select>
                    </div>
                </div>

                <div>
                    <div class="button-group">
                        <input class="button-blue-rounding" type="submit" name="filter" value="Найти">
                        <a class="red-link" href="<?php echo $setting['script_url'];?>/admin/reviews/">Сбросить</a>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th class="text-left">Имя</th>
                <th class="text-left">Дата</th>
                <th class="text-left">Категория</th>
                <th class="text-left">Статус</th>
                <th class="td-last"></th>
            </tr>
            </thead>
            <tbody>
            <?php if($list_reviews){
                foreach($list_reviews as $review):?>
            <tr<?php if($review['status'] == 0) echo ' class="off"';?>>
                <td><?php echo $review['id'];?></td>
                <td class="text-left"><a href="/admin/reviews/edit/<?php echo $review['id'];?>"><?php echo $review['name'];?></a></td>
                <td class="text-left"><?php echo $review['create_date']?></td>
                <td class="text-left"><?php if($review['cat_id'] != null) {$cat_name = Product::getReviewCatByID($review['cat_id']); echo $cat_name['cat_name'];}?></td>
                <td class="td-last">
                    <?php if($review['status'] == 1){?>
                    <span class="stat-yes"><i class="icon-stat-yes"></i></span>
                    <?php } else {?>
                    <span class="stat-no"></span>
                    <?php } ?>
                </td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/admin/reviews/delrev/<?php echo $review['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
            </tr>
            <?php endforeach; 
            } else echo '<p>Отзывов пока нет, поработайте над ними.</p>';?>
            </tbody>
        </table>
    </div>
</div>
    <?php if(isset($is_pagination) && $is_pagination == true) echo $pagination->get(); ?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>