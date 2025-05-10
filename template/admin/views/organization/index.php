<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Список организаций</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Организации</li>
    </ul>
    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
        <li><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/organizations/add/">Добавить организацию</a></li>
        </ul>
    </div>
    <?php if(isset($_GET['delete_success'])) echo '<div class="admin_message">Организация успешно удалена</div>';?>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно</div>';?>
    <div class="admin_form">
    <div class="overflow-container">
            <?php if($org_list){?>
            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th class="text-left">Название</th>
                    <th class="td-last"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($org_list as $org):?>
                <tr>
                    <td><?php echo $org['id'];?></td>
                    <td class="text-left"><a href="<?php echo $setting['script_url'];?>/admin/organizations/edit/<?php echo $org['id'];?>"><?php echo $org['org_name'];?></a></td>
                    <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="<?php echo $setting['script_url'];?>/admin/organizations/del/<?php echo $org['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
                </tr>
                <?php endforeach;?>
                </tbody>
            </table>
            <?php } else echo 'Организаций ещё не добавлено'; ?>
        </div>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>