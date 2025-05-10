<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
  <div class="top-wrap">
    <h1><?php echo System::Lang('RUBRICS_LIST');?></h1>
    <div class="logout">
        <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
  </div>

  <ul class="breadcrumb">
    <li>
      <a href="/admin">Дашбоард</a>
    </li>
    <li>Список категорий</li>
  </ul>

  <div class="nav_gorizontal">
    <ul class="nav_gorizontal__ul flex-right">
      <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="/admin/rubrics/add/"><?php echo System::Lang('ADD_RUBRIC');?></a></li>
      <li><a class="button-yellow-rounding" href="/admin/blog/"><?php echo System::Lang('POST_LIST');?></a></li>
    </ul>
  </div>

    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <?php if(isset($_GET['fail'])) echo '<div class="admin_warning">Не возможно удалить!</div>'?>
<div class="admin_form admin_form--margin-top">
<div class="overflow-container">
    <table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th class="text-left">Название</th>
            <th class="td-last">Act</th>
        </tr>
       </thead>
        <tbody>
        <?php if($rubric_list){
            foreach($rubric_list as $rubric):?>
        <tr<?php if($rubric['status'] == 0) echo ' class="off"';?>>
            <td><?php echo $rubric['id'];?></td>
            <td class="text-left"><a href="/admin/rubrics/edit/<?php echo $rubric['id'];?>"><?php echo $rubric['name'];?></a></td>
            <td class="td-last"><a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/rubrics/del/<?php echo $rubric['id'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
        </tr>
        <?php endforeach;} else echo 'Ещё нет ни одной категории';?>

        </tbody>
    </table>
</div>
</div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>