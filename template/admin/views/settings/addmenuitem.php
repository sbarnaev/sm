<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
<div class="top-wrap">
    <h1>Создать пункт меню</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
</div>

  <ul class="breadcrumb">
    <li>
      <a href="/admin">Дашбоард</a>
    </li>
    <li>
      <a href="/admin/menuitems/">Пункты меню</a>
    </li>
    <li>Создать пункт меню</li>
  </ul>

    <form action="" method="POST">

      <div class="admin_top admin_top-flex">
        <div class="admin_top-inner">
          <div>
            <img src="/template/admin/images/icons/item-menu.svg" alt="">
          </div>
          <div>
            <h3 class="traning-title mb-0">Изменить пункт меню</h3>
          </div>
        </div>
        <ul class="nav_button">
          <li><input type="submit" name="addmenuitem" value="Создать" class="button save button-white font-bold"></li>
          <li class="nav_button__last"><a class="button red-link" href="<?php echo $setting['script_url'];?>/admin/menuitems/">Закрыть</a></li>
        </ul>
      </div>

        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Имя: </label><input type="text" name="name" placeholder="Имя пункта" required="required"></p>

                    <?php if($type == 'custom'){?>
                    <p class="width-100"><label>URL: </label><input type="text" name="url" placeholder="URL адрес" required="required"></p>
                    <?php } elseif($type == 'static') {?>
                    <p class="width-100"><label>URL: </label><input type="text" name="url" placeholder="URL адрес" disabled="disabled" value="<?php if(isset($_GET['alias'])) echo 'page/'.$_GET['alias'];?>" required="required">
                    <input type="hidden" name="url" value="<?php if(isset($_GET['alias'])) echo 'page/'.$_GET['alias'];?>"></p>
                    <?php } else {?>
                    <p class="width-100"><label>URL: </label><input type="text" name="url" disabled="disabled" placeholder="URL адрес" value="<?php echo $type;?>" required="required">
                    <input type="hidden" name="url"  value="<?php echo $type;?>"></p>
                    <?php }?>

                    <div class="width-100"><label>Родительский пункт: </label>
                        <div class="select-wrap">
                        <select name="parent_id">
                        <option value="0">Нет</option>
                        <?php if($menu_items):
                        foreach($menu_items as $item):?>
                        <option value="<?php echo $item['item_id'];?>"><?php echo $item['name'];?></option>
                        <?php endforeach;
                        endif;?>
                    </select>
                    </div>
                    </div>

                    <p class="width-100"><label>Порядок: </label><input type="text" name="sort" value="1" size="3"></p>

                    <div class="width-100"><label>Статус: </label>
                        <div class="select-wrap">
                        <select name="status">
                        <option value="1">Включен</option>
                        <option value="0">Отключен</option>
                    </select>
                    </div>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                    <input type="hidden" name="type" value="<?php echo $type;?>">
                    <input type="hidden" name="menu_id" value="1">
                </div>

                <div class="col-1-2">
                    <h4>Дополнительно</h4>
                    <p class="width-100"><label>Title ссылки:</label><input type="text" name="title"></p>
                    <div class="width-100"><label>Открывать ссылку: </label>
                       <div class="select-wrap">
                        <select name="new_window">
                        <option value="0">В этой же вкладке</option>
                        <option value="1">В новой вкладке</option>
                    </select>
                    </div>
                    </div>
                    
                    <div class="width-100"><label>Показывать в меню: </label>
                       <div class="select-wrap">
                        <select name="visible">
                        <option value="1">Показывать</option>
                        <option value="0">Скрыть</option>
                    </select>
                    </div>
                    </div>
                    
                    <div class="width-100"><label>Включить в карту сайта: </label>
                       <div class="select-wrap">
                        <select name="sitemap">
                        <option value="1">Да</option>
                        <option value="0">Нет</option>
                    </select>
                    </div>
                    </div>
                    
                    <div class="width-100"><label>Частота обновления: </label>
                       <div class="select-wrap">
                        <select name="changefreq">
                        <option value="always">Постоянно</option>
                        <option value="hourly">Каждый час</option>
                        <option value="daily">Каждый день</option>
                        <option value="weekly">Каждую неделю</option>
                        <option value="monthly">Каждый месяц</option>
                        <option value="yearly">Каждый год</option>
                        <option value="never">Никогда</option>
                    </select>
                    </div>
                    </div>
                    
                    <div class="width-100"><label>Приоритет сканирования: </label>
                       <div class="select-wrap">
                        <select name="priority">
                        <option value="1.0">1.0</option>
                        <option value="0.9">0.9</option>
                        <option value="0.8">0.8</option>
                        <option value="0.7">0.7</option>
                        <option value="0.6">0.6</option>
                        <option value="0.5">0.5</option>
                        <option value="0.4">0.4</option>
                        <option value="0.3">0.3</option>
                        <option value="0.2">0.2</option>
                        <option value="0.1">0.1</option>
                    </select>
                    </div>
                    </div>
                </div>
</div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>