<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Список категорий</h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training/">Тренинги</a></li>
        <li>Список категорий</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="/admin/training/addcat">Добавить категорию</a></li>
           
        </ul>
    </div>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>
    <?php if(isset($_GET['fail'])):?>
        <div class="admin_warning">Не удалось удалить, возможно категория содержит подкатегории!</div>
    <?php endif;?>

    <div class="admin_form admin_form--margin-top">
        <div class="overflow-container">
            <table class="table">
                <?php if($cat_list):?>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="text-left">Название</th>
                            <th class="td-last"></th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        <?php foreach($cat_list as $category):?>
                            <tr<?php if($category['status'] == 0) echo ' class="off"';?>>
                                <td><?=$category['cat_id'];?></td>
                                <td class="text-left"><a href="/admin/training/editcat/<?=$category['cat_id'];?>"><?=$category['name'];?></a></td>
                                <td class="td-last">
                                    <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/training/delcat/<?=$category['cat_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="Удалить">
                                        <i class="fas fa-times" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>

                            <?php $subcategories = TrainingCategory::getSubCategories($category['cat_id'], null);
                            if($subcategories):
                                foreach ($subcategories as $subcategory):?>
                                    <tr class="subcat <?php if($subcategory['status'] == 0) echo ' off';?>">
                                        <td style="padding: 0 0 0 30px;"><?=$subcategory['cat_id'];?></td>
                                        <td style="padding: 0 0 0 30px;" class="text-left">|_ <a href="/admin/training/editcat/<?=$subcategory['cat_id'];?>"><?=$subcategory['name'];?></a></td>
                                        <td class="td-last">
                                            <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/training/delcat/<?=$subcategory['cat_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="Удалить">
                                                <i class="fas fa-times" aria-hidden="true"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach;
                            endif;
                        endforeach;
                    else:
                        echo 'У вас ещё не создано категорий';
                    endif;?>
                </tbody>
            </table>
        </div>
    </div>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>