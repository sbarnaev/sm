<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
  <div class="top-wrap">
    <h1>Формы обратной связи</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
  </div>
  <ul class="breadcrumb">
    <li>
      <a href="/admin">Дашбоард</a>
    </li>
    <li>
      <a href="/admin/feedback/">Обратная связь</a>
    </li>
    <li>Список форм</li>
  </ul>
  <div class="nav_gorizontal">
    <ul class="nav_gorizontal__ul flex-right">
      <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="/admin/feedback/addform/">Создать форму</a></li>
    </ul>
  </div>
    
    <!--div class="filter">
    </div-->
    
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Это форма по-умолчанию, её удалять нельзя!</div>'?>
<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
    <table class="table">
       <thead>
        <tr>
            <th>ID</th>
            <th class="text-left">Имя</th>
            <th class="text-left">Хиты</th>
            <th class="td-last"></th>
        </tr>
        </thead>
        <tbody>
        <?php if($forms){
        foreach($forms as $form):?>
        <tr<?php if($form['status'] == 0) echo ' class="off"';?>>
            <td><?php echo $form['form_id'];?></td>
            <td class="text-left"><a href="/admin/feedback/editform/<?php echo $form['form_id'];?>"><?php echo $form['name'];?></a> <?php if($form['default_form'] == 1) echo '<span style="font-size:18px; color:orange" title="форма по-умолчанию"> ★</span>'?></td>
            <td class="text-left"><?php echo $form['hits'];?></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('<?php echo System::Lang('YOU_SHURE');?>?')" href="<?php echo $setting['script_url'];?>/admin/feedback/delform/<?php echo $form['form_id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="<?php echo System::Lang('DELETE');?>"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;
        } else echo 'Здесь пока нет форм';?>
        </tbody>
    </table>
</div>
</div>
    <?php if(isset($is_pagination) && $is_pagination == true) echo $pagination->get();?>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>