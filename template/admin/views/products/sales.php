<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Список акций</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a>
            <a href="/admin/logout" class="red">Выход</a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/products/">Продукты</a></li>
        <li>Акции</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap">
                <a class="button-red-rounding" href="/admin/sales/add/">Создать акцию</a>
            </li>
            <li><a class="settings-link" href="/admin/sales/page/"><i class="icon-settings"></i></a></li>
        </ul>
    </div>

    <div class="filter"></div>
    
    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>
    
    <div class="admin_form admin_form--margin-top">
        <div class="overflow-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th class="text-left">Название</th>
                        <th class="text-left">Тип</th>
                        <th class="td-last"></th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php if($sales):
                        foreach($sales as $sale):
                            if ($sale['type'] == 9) {
                                continue;
                            }?>
                            <tr<?php if($sale['status'] == 0) echo ' class="off"';?>>
                                <td><?=$sale['id'];?></td>
                                <td class="text-left">
                                    <a href="<?=$setting['script_url'];?>/admin/sale/edit/<?=$sale['id'];?>">
                                        <?=$sale['name'];?>
                                    </a>
                                </td>
                                <td class="text-left">
                                    <?php if($sale['type'] == 1) {
                                        echo 'Красная цена';
                                    } elseif($sale['type'] == 2) {
                                        echo 'Промо код';
                                    } else {
                                        echo 'Динамическая';
                                    }?>
                                </td>
                                <td class="td-last">
                                    <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/sales/del/<?=$sale['id'];?>?token=<?=$_SESSION['admin_token'];?>" title="Удалить">
                                        <i class="fas fa-times" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach;
                    endif;?>
                </tbody>
            </table>
        </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>