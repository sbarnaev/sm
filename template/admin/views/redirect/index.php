<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('REDIRECTS_LIST');?></h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Редиректы</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/redirect/add/"><?php echo System::Lang('ADD_REDIRECT');?></a></li>

            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent nav_gorizontal__parent-yellow">
                    <a href="javascript:void(0);" class="nav-click button-yellow-rounding">Категории</a>
                    <span class="nav-click icon-arrow-down"></span>
                </div>
                <ul class="drop_down">
                    <li><a href="<?php echo $setting['script_url'];?>/admin/redirect/addcat/"><?php echo System::Lang('ADD_REDIRECT_CAT');?></a></li>
                    <li><a href="<?php echo $setting['script_url'];?>/admin/redirect/cats/"><?php echo System::Lang('REDIRECT_CATS');?></a></li>
                </ul>
            </li>

        </ul>
    </div>
    
    <div class="filter admin_form">
        <form action="" method="POST">
            <div class="filter-row">
                <div class="max-width-147">
                    <div class="select-wrap">
                        <select name="cat_id">
                            <option value="0">Категория</option>
                            <?php $cat_list = Redirect::getRdrCatList();
                foreach($cat_list as $cat):?>
                            <option value="<?php echo $cat['cat_id'];?>"><?php echo $cat['name'];?></option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="max-width-147 mr-auto">
                    <input type="text" name="url" placeholder="по url">
                </div>
                <div>
                    <div class="button-group">
                        <input class="button-blue-rounding" type="submit" name="filter" value="Фильтр">
                        <a class="red-link" href="<?php echo $setting['script_url'];?>/admin/redirect/">Сброс</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
    
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
    <table class="table">
        <thead>
        <tr>
            <th class="text-left">Название</th>
            <th class="text-left">Откуда</th>
            <th class="text-left">Куда</th>
            <th class="text-left">Категория</th>
            <th>Хиты</th>
            <th class="td-last"></th>
        </tr>
        </thead>
        <tbody>
        <?php if($redirect_list){
        foreach($redirect_list as $redirect):?>
        <tr<?php if($redirect['status'] == 0) echo ' class="off"'; ?>>
        <td class="text-left rdr_2"><a href="<?php echo $setting['script_url'];?>/admin/redirect/edit/<?php echo $redirect['id'];?>" title="<?php echo $redirect['rdr_desc'];?>"><?php echo $redirect['title'];?></a></td>
        <td class="text-left rdr_1"><input class="dop_cat-2" type="text" disabled value="<?php echo $setting['script_url'].'/rdr/'.$redirect['id'];?>"></td>

        <td class="rdr_2 text-left"><input class="dop_cat-2" type="text" disabled value="<?php echo $redirect['url'];?>"></td>
        <td class="text-left"><?php $cat = Redirect::getCat($redirect['cat_id']); echo $cat['name'];?></td>
        <td><?php echo $redirect['hits'];?></td>
        <td class="td-last"><a class="link-delete" onclick="return confirm('<?php echo System::Lang('YOU_SHURE');?>?')" href="<?php echo $setting['script_url'];?>/admin/redirect/del/<?php echo $redirect['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="<?php echo System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;
        } else echo '<p>No redirects</p>'; ?>
        </tbody>
    </table>
</div>
</div>
    <?php if(isset($is_pagination) && $is_pagination == true) echo $pagination->get(); ?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>