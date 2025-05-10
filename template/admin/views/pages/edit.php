<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
  <div class="top-wrap">
    <h1>Изменить страницу</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
  </div>
  <ul class="breadcrumb">
    <li>
      <a href="/admin">Дашбоард</a>
    </li>
    <li>
      <a href="/admin/statpages/">Статичные страницы</a>
    </li>
    <li>Изменить страницу</li>
  </ul>
  <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    <form action="" method="POST">

      <div class="admin_top admin_top-flex">
        <h3 class="traning-title">Изменить страницу</h3>
        <ul class="nav_button">
          <li><input type="submit" name="editpage" value="Сохранить" class="button save button-white font-bold"></li>
          <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/statpages/">Закрыть</a></li>
        </ul>
      </div>

        <div class="admin_form">
                <div class="row-line">
                  <div class="col-1-2">
                    <h4>Основное</h4>
                    <p><label>Название: </label><input type="text" name="name" value="<?php echo $page['name']?>" placeholder="Название страницы" required="required"></p>

                    <p><label>Статус: </label>
                      <span class="custom-radio-wrap">
                    <label class="custom-radio"><input name="status" type="radio" value="1" <?php if($page['status'] == 1) echo 'checked';?>><span>Вкл</span></label>
                    <label class="custom-radio"><input name="status" type="radio" value="0" <?php if($page['status'] == 0) echo 'checked';?>><span>Откл</span></label>
                </span>
                    </p>

                    <p class="width-100"><label>Загрузить с URL: </label><input type="text" name="curl" value="<?php echo $page['curl']?>" placeholder="URL страницы"></p>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                  </div>
                  <div class="col-1-2">
                    <h4>SEO</h4>
                    <p><label>Алиас: </label><input type="text" value="<?php echo $page['alias'];?>" name="alias" placeholder="Алиас страницы "></p>
                    <p><a target="_blank" href="/page/<?php echo $page['alias'];?>">Открыть страницу >></a></p>
                    <p><label>Title: </label><input type="text" name="title" value="<?php echo $page['title'];?>" placeholder="Title страницы"></p>
                    <p>Meta Description:<br /><textarea name="meta_desc" rows="3" cols="40"><?php echo $page['meta_desc'];?></textarea></p>
                    <p>Meta Keys:<br /><textarea name="meta_keys" rows="3" cols="40"><?php echo $page['meta_keys'];?></textarea></p>
                  </div>

                  <div class="col-1-1">
                    <div class="width-100"><textarea class="editor" name="content"><?php echo $page['content'];?></textarea></div>
                    <p>Произвольный HTML код</p>
                    <div><textarea name="custom_code" rows="4" style="font-size: 0.95rem;"><?php echo $page['custom_code'];?></textarea></div>
                    <div class="width-100"><label>Шаблон: </label>
                      <div class="select-wrap">
                        <select name="tmpl">
                          <option value="1"<?php if($page['tmpl'] == 1) echo ' selected="selected"';?>>Стандарт</option>
                          <option value="0"<?php if($page['tmpl'] == 0) echo ' selected="selected"';?>>Без шаблона</option>
                        </select>
                      </div>
                    </div>
                  </div>

                  <div class="col-1-2">
                    <h4>CSS и JS в head</h4>
                    <textarea name="in_head" rows="6"><?php echo $page['in_head']?></textarea>
                  </div>

                  <div class="col-1-2">
                    <h4>CSS и JS перед /body</h4>
                    <textarea name="in_body" rows="6"><?php echo $page['in_body']?></textarea>
                  </div>
                  <div class="col-1-1">
                    <p>* CSS: <code>&lt;link rel="stylesheet" href="/template/css/style.css" type="text/css" /&gt;</code><br />
                      * JS: <code>&lt;script src="jquery-1.12.4.min.js"&gt;&lt;/script&gt;</code></p>
                  </div>

                </div>



        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>