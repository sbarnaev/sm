<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
  <div class="top-wrap">
    <h1>Статистика выплат партнёрам</h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
  </div>
  <ul class="breadcrumb">
    <li>
      <a href="/admin">Дашбоард</a>
    </li>
    <li>
      <a href="/admin/aff/">Партнерка</a>
    </li>
    <li>Статистика выплат партнёрам</li>
  </ul>

  <div class="nav_gorizontal">
    <ul class="nav_gorizontal__ul flex-right">
      <li><a class="button-yellow-rounding" href="/admin/authors/">Назад</a></li>
    </ul>
  </div>

    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>

<div class="admin_form admin_form--margin-top">
    <div class="overflow-container">
    <table class="table">
       <thead>
        <tr>
            <th>ID</th>
            <th class="text-left">Партнёр</th>
            <th>Заработано</th>
            <th>Выплачено</th>
            <th>К выплате</th>
            
        </tr>
        </thead>
        <tbody>
        <?php if($partners){
            foreach($partners as $partner):
            $user = User::getUserNameByID($partner['user_id']);?>
        <tr<?php if($user['status'] == 0) echo ' class="off"';?>>
            <td><?php echo $partner['user_id'];?></td>
            <td class="text-left"><p><a target="_blank" href="/admin/users/edit/<?php echo $partner['user_id'];?>"><?php echo $user['user_name'];?></a>
            <br /><span class="small"><?php echo $user['email']?></span></p></td>

        <? /*
            <td><a class="button_req">Реквизиты</a> 
            <div class="div_req"><?php $req = Aff::getPartnerReq($partner['user_id']);
                        $req = unserialize($req['requsits']);
                        $req_arr = explode("\r\n", $params['params']['req']);

                        foreach($req_arr as $req_item):
                        $req_item = explode("=", $req_item);?>

                       <?php if(!empty($req["$req_item[0]"])):
                        
                        if($req_item[0] != 'rs'){?>
                        <p><?php echo $req_item[1];?> <input type="text" value="<?php
                        if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"];?>">
                        </p>
                        
                        
                        <?php } else {?>
                        <p><?php echo $req_item[1];?> <br />Счёт: <input type="text" value="<?php
                        if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['rs'];?>">
                        <br />
                        Банк: <input type="text" value="<?php
                        if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['name'];?>">
                        <br />
                        БИК : <input type="text" value="<?php
                        if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['bik'];?>">
                        </p>
                        <?php } endif; ?>

                <?php endforeach; ?>
                </div>
            </td>
        */ ?>

            <td><a href="/admin/aff/userstat/<?php echo $partner['user_id'];?>" target="_blank"><?php echo $partner['summ'];?></a></td>
            <td class="fz-16"><?php echo $partner['pay'];?></td>
            <td class="fz-16"><?php echo $partner['summ'] - $partner['pay'];?></td>
            
        </tr>
        <?php endforeach;
        } else {echo 'Нет выплат';}?>
        </tbody>
    </table>
    </div>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>