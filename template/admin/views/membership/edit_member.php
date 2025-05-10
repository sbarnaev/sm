<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Изменить подписку клиента</h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?=$setting['script_url'];?>/admin/logout" class="red">Выход</a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/memberusers/">Подписки клиентов</a></li>
        <li>Изменить подписку</li>
    </ul>
    
    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>

    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Изменить подписку клиента</h3>
            <ul class="nav_button">
                <li><input type="submit" name="edit" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="<?=$setting['script_url'];?>/admin/memberusers/">Закрыть</a></li>
            </ul>
        </div>

        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p><strong>ID подписки: </strong> <?=$member['id'];?></p>
                    <p><strong>Клиент: </strong> <a target="_blank" href="/admin/users/edit/<?=$member['user_id']; ?>"><?php $user = User::getUserNameByID($member['user_id']); echo $user['user_name'];?></a></p>
                    <p><strong>Email: </strong> <?=$user['email'];?></p>

                    <p><a target="_blank" href="/admin/membersubs/edit/<?=$member['subs_id'];?>">
                            <strong>План подписки:</strong>
                        </a>
                        <?php if($planes):?>
                            <div class="select-wrap">
                                <select name="plane_id">
                                    <option value="">- Выбрать -</option>
                                    <?php foreach($planes as $plane):?>
                                        <option value="<?=$plane['id'];?>"<?php if($plane['id'] == $member['subs_id']) echo ' selected="selected"';?>><?=$plane['name']?></option>
                                        <?php if($plane['service_name']):?>
                                            <option disabled="disabled" class="service-name">(<?=$plane['service_name'];?>)</option>
                                        <?php endif;
                                    endforeach;?>
                                </select>
                            </div>
                        <?php endif;?>
                    </p>

                    <p><strong>Кол-во продлений: </strong>
                        <?=$member['update_count'];?>
                    </p>
                    
                    <p><strong>Последнее продление: </strong>
                        <?php if($member['last_update'] != null) echo date("d-m-Y H:i:s", $member['last_update']);?>
                    </p>
                    
                    <p><label>Статус подписки: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio">
                                <input name="status" type="radio" value="1" <?php if($member['status']== 1) echo 'checked';?>>
                                <span>Вкл</span>
                            </label>
                            
                            <label class="custom-radio">
                                <input name="status" type="radio" value="0" <?php if($member['status']== 0) echo 'checked';?>>
                                <span>Откл</span>
                            </label>
                        </span>
                    </p>
                    
					<?php if($member['subscription_id'] != null):?>
                    <div class="width-100"><label>Рекуррентые платежи: <?php if($member['recurrent_cancelled'] == 1) echo 'Отменены клиентом'; else echo 'Активны';?></label>
                        <!--div class="select-wrap">
                            <select name="recurrent_cancelled">
                                <option value="1"<?php if($member['recurrent_cancelled'] == 1) echo ' selected="selected"';?>>Отменены клиентом</option>
                                <option value="0"<?php if($member['recurrent_cancelled'] == 0) echo ' selected="selected"';?>>Идут платежи</option>
                            </select>
                        </div-->
                    </div>
					<?php endif;?>
                    
                    <p><label>Действия:</label></p>
                    <div class="one-line">
                        <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/memberusers/delete/<?=$member['id'];?>?token=<?=$_SESSION['admin_token'];?>&action=delete" title="Удалить">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </a>
                    </div>

                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                  </div>
                  
                <div class="col-1-2">
                    <h4>Дополнительно</h4>
                    <p title="ID подписки во внешней системе: Cloudpayments и т.д.">Subscription ID:<br />
                        <textarea name="subscription_id" rows="3" cols="40"><?=$member['subscription_id'];?></textarea>
                    </p>
                  
                    <p><strong>Дата создания:</strong> <?=date("d.m.Y H:i:s", $member['create_date']); ?></p>
                    <p><strong>Дата окончания:</strong> <?php if($time > $member['end']) echo ''?> <?=date("d.m.Y H:i:s", $member['end']); ?></p>
                    <p><label>Изменить дату окончания:</label>
                        <input type="text" class="datetimepicker" name="end" value="<?=date("d.m.Y H:i:s", $member['end']);?>" autocomplete="off">
                    </p>
					
					<?php if($_SERVER['HTTP_HOST'] == 'lk.school-master.ru'):?>
                    <p><label>ID лицензии:</label>
                        <input type="text" name="lc_id" value="<?=$member['lc_id'];?>" autocomplete="off">
                    </p>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
<link rel="stylesheet" type="text/css" href="/template/admin/css/jquery.datetimepicker.min.css">
<script src="/template/admin/js/jquery.datetimepicker.full.min.js"></script>
<script>
  jQuery('.datetimepicker').datetimepicker({
    format:'d.m.Y H:i',
    lang:'ru'
  });
</script>
</body>
</html>