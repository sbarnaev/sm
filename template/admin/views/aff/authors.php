<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>
<script>
    $(document).ready(function(){
        $('.button_req').click(function () {
            $(this).next('.div_req').toggleClass('open');
        });
    });
</script>
<style>
    .button_req {text-decoration:none; border-bottom: 1px dashed #555; overflow: hidden;}
    .div_req {height:0; visibility:hidden; transition: 0.5s;}
    .div_req.open {height:250px; visibility:visible; padding:0.5em 0 0 0; transition: 0.5s}
</style>
<div class="main">
  <div class="top-wrap">
    <h1>Выплаты авторских</h1>
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
    <li>Выплаты авторских</li>
  </ul>
  <div class="nav_gorizontal">
    <ul class="nav_gorizontal__ul flex-right">
      <li class="nav_gorizontal__parent-wrap"><a class="button-yellow-rounding" href="/admin/users?role=is_author">Все авторы</a></li>
      <li><a class="button-yellow-rounding" href="/admin/authors/paystat/">Все выплаты</a></li>
    </ul>
  </div>
  <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
<div class="admin_form admin_form--margin-top">
  <div class="overflow-container">
    <table class="table">
      <thead>
      <tr>
        <th class="text-left">Автор</th>
        <th>Заработано</th>
        <th>Выплачено</th>
        <th>К выплате</th>
        <th>Выплатить</th>
      </tr>
      </thead>
      <tbody>
      <?php if($authors){
            foreach($authors as $author):
            $user = User::getUserNameByID($author['user_id']);?>
      <tr<?php if($user['status'] == 0) echo ' class="off"';?>>
      <td class="text-left"><a target="_blank" href="/admin/users/edit/<?php echo $author['user_id'];?>"><?php echo $user['user_name'];?></a>
        <br /><span class="small"><?php echo $user['email']?></span>
        <br />
        <?php $req = Aff::getPartnerReq($author['user_id']);
            $req = unserialize($req['requsits']);
        if($req):?>
        <a class="button_req" href="javascript:void(0);">Реквизиты</a>
        <div class="div_req">
            <?php $req_arr = explode("\r\n", $params['params']['req']);
            foreach($req_arr as $req_item):
                $req_item = explode("=", $req_item);
                if(!empty($req["$req_item[0]"])):?>
                    <div class="mb-10"><?=$req_item[1];?>
                        <?php if($req_item[0] != 'rs'):?>
                            <input type="text" value="<?php if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"];?>">
                        <?php else:?>
                            <div class="mb-5">Счёт: <input type="text" value="<?php if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['rs'];?>"></div>
                            <div class="mb-5">Банк: <input type="text" value="<?php if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['name'];?>"></div>
                            <div class="mb-5">БИК : <input type="text" value="<?php if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['bik'];?>"></div>
                            <div class="mb-5">ИНН : <input type="text" value="<?php if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"]['itn'];?>"></div>
                        <?php endif;?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php else:?>
            <span class="small">Реквизиты не указаны</span>
        <?php endif; ?>
        </td>
      <td><a href="/admin/authors/userstat/<?php echo $author['user_id'];?>" target="_blank"><?php echo $author['summ'];?></a></td>
      <td><?php echo $author['pay'];?></td>
      <td class="fz-16"><?php echo $author['summ'] - $author['pay'];?></td>
      <td><form id="pay<?php echo $author['user_id'];?>" action="" method="POST">
        <input type="text" name="summ" required="required">
        <input type="hidden" name="partner" value="<?php echo $author['user_id'];?>">
        <input class="button-green-rounding mt-5 d-block" type="submit" name="pay" value="Выплатить">
      </form>
      </td>
      </tr>
      <?php endforeach;
        } else {echo '<tr><td class="text-left" colspan="5">Нет выплат</td></tr>';}?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>

</html>