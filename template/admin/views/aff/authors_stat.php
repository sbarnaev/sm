<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
  <div class="top-wrap">
    <h1>Статистика выплат авторских</h1>
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
      <li><a class="button-yellow-rounding" href="/admin/authors"><< Необходио выплатить</a></li></ul>
  </div>
  <div class="filter">
  </div>
  <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
<div class="overflow-container">
  <p><strong>Список выплат авторам</strong></p>
  <table class="table table-striped">
    <thead>
    <tr>
      <th>ID</th>
      <th>Автор</th>
      <th>Реквизиты</th>
      <th>Заработано</th>
      <th>Выплачено</th>
      <th>К выплате</th>
    </tr>
    </thead>
    <tbody>
    <?php if($authors){
            foreach($authors as $author):
            $user = User::getUserNameByID($author['user_id']);?>
    <tr<?php if($user['status'] == 0) echo ' class="off"';?>>
    <td><?php echo $author['user_id'];?></td>
    <td><p><a target="_blank" href="/admin/users/edit/<?php echo $author['user_id'];?>"><?php echo $user['user_name'];?></a>
      <br /><span class="small"><?php echo $user['email']?></span></p></td>

    <td><?php $req = Aff::getPartnerReq($author['user_id']);
                        $req = unserialize($req['requsits']);
                        $req_arr = explode("\r\n", $params['params']['req']);

                        foreach($req_arr as $req_item):
                        $req_item = explode("=", $req_item);?>

      <?php if(!empty($req["$req_item[0]"])):?>
      <p><?php echo $req_item[1];?> <input type="text" value="<?php
                        if(array_key_exists("$req_item[0]", $req)) echo $req["$req_item[0]"];?>">
      </p>
      <?php endif; ?>

      <?php endforeach; ?>
    </td>

    <td><a href="/admin/authors/userstat/<?php echo $author['user_id'];?>" target="_blank"><?php echo $author['summ'];?></a></td>
    <td><?php echo $author['pay'];?></td>
    <td class="focus"><?php echo $author['summ'] - $author['pay'];?></td>

    </tr>
    <?php endforeach;
        } else {echo '<tr><td class="text-left" colspan="5">Нет выплат</td></tr>';}?>
    </tbody>
  </table>
</div>
<?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>