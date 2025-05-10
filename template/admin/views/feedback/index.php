<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
  <div class="top-wrap">
    <h1>Список сообщений</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
  </div>

  <ul class="breadcrumb">
    <li>
      <a href="/admin">Дашбоард</a>
    </li>
    <li>Список сообщений</li>
  </ul>

  <div class="nav_gorizontal">
    <ul class="nav_gorizontal__ul flex-right">
      <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="/admin/feedback/addform/">Добавить форму</a></li>
      <li><a class="button-yellow-rounding" href="/admin/feedback/forms/">Список форм</a></li>
    </ul>
  </div>
    
    <!--div class="filter">
    </div-->
    
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
    <table class="table">
       <thead>
        <tr>
          <th>ID</th>
            <th class="text-left">Имя</th>
            <th class="text-left">E-mail</th>
            <th class="td-last"></th>
        </tr>
        </thead>
        <tbody>
        <?php if($messages):
        foreach($messages as $message):
        $text = strip_tags($message['text']);
        $text = mb_substr($text, 0, 100)?>
        <tr<?php if($message['status'] == 0) echo ' class="off"'; if($message['status'] == 2) echo ' class="conf"'; if($message['status'] == 9) echo ' style="background: #ffc3ad"';?>>
            <td><a href="<?php echo $setting['script_url'];?>/admin/feedback/view/<?php echo $message['id'];?>"><?php echo $message['id'];?></a></td>
            <td class="text-left"><?php echo $message['name'];?><br><?php echo date("d.m.Y", $message['create_date']);?> <a href="<?php echo $setting['script_url'];?>/admin/feedback/view/<?php echo $message['id'];?>">Посмотреть</a></td>
            <td class="text-left"><?php echo $message['email'];?></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('<?php echo System::Lang('YOU_SHURE');?>?')" href="<?php echo $setting['script_url'];?>/admin/feedback/del/<?php echo $message['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="<?php echo System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;
        endif;?>
        </tbody>
    </table>
</div>
</div>
    <?php if(isset($is_pagination) && $is_pagination == true) echo $pagination->get(); ?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>