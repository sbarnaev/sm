<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('CONDITIONS');?></h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li><?php echo System::Lang('CONDITIONS');?></li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/conditions/add/"><?php echo System::Lang('ADD_CONDITION');?></a></li>

        </ul>
    </div>
    
    <!--div class="filter admin_form">
    </div-->
    
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
    <table class="table">
        <thead>
        <tr>
            <th class="text-left">Название</th>
            <th class="text-left">Тип</th>
            <th class="text-left">Действие</th>
            <th class="text-left">Интервал</th>
            <th class="td-last"></th>
        </tr>
        </thead>
        <tbody>
        <?php if($conditions_list){
        foreach($conditions_list as $condition):?>
        <tr<?php if($condition['status'] == 0) echo ' class="off"'; ?>>
        <td class="text-left col-width-50"><a href="<?php echo $setting['script_url'];?>/admin/conditions/edit/<?php echo $condition['id'];?>"><?php echo $condition['name'];?></a></td>
        <td class="text-left rdr_1"><?php echo getConditionType($condition['type']);?></td>

        <td class="rdr_2 text-left"><?php echo getAction($condition);?></td>
        <td class="text-left"><?php echo $condition['period'];?></td>
        <td class="td-last"><a class="link-delete" onclick="return confirm('<?php echo System::Lang('YOU_SHURE');?>?')" href="<?php echo $setting['script_url'];?>/admin/conditions/del/<?php echo $condition['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="<?php echo System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;
        } else echo '<p>Нет условий</p>'; ?>
        </tbody>
    </table>
</div>
</div>
    <?php if(isset($is_pagination) && $is_pagination == true) echo $pagination->get(); ?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>
<?php function getConditionType($type)
{
    switch($type){
            case 1:
            return 'Последний вход';
            break;
            
            case 2:
            return 'Выполненное задание';
            break;
            
            case 3:
            return 'До дня рождения';
            break;
            
            case 4:
            return 'Принадлежат группе';
            break;

            case 5:
            return 'Подписка мембершипа заканчивается ...';
            break;

            case 6:
            return 'Не принадлежат никакой группе ...';
            break;
        }
    
}


function getAction($condition)
{
    $action = null;
    if($condition['send_letter'] == 1) $action .= 'Письмо<br />';
    if($condition['add_groups'] != null) $action .= 'Добавление в группу<br />';
    if($condition['delivery_id'] != 0) $action .= 'Подписка<br />';
    
    return $action;
}
?>