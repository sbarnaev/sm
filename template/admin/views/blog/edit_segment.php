<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Редактировать сегмент (ID : <?php echo $segment['sid'];?>)</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    <form action="" method="POST">
    <div class="nav_gorizontal">
        <ul>
            <li><input type="submit" name="edit" value="Сохранить" class="button save button-green-rounding"></li>
            <li><a class="button button-red-rounding" href="/admin/segments">Закрыть</a></li>
        </ul>


    </div>
    <?php if(isset($_GET['success'])) echo '<div class="admin_message">Успешно!</div>'?>
    
        <div class="admin_form">
                <div class="box2">
                    <h4>Основное</h4>
                    <p><label>Название: </label><input type="text" name="name" value="<?php echo $segment['name'];?>" placeholder="Название сегмента" required="required"></p>
                    
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                <div class="box2">
                    <h4>Описание</h4>
                    <p>Описание:<br /><textarea name="desc" rows="3" cols="40"><?php echo $segment['seg_desc'];?></textarea></p>
                </div>
                
                <div class="box1">
                    <h4>URL адреса:</h4>
                    <div class="url_segment">
                        
                        <table class="table">
                            <tr>
                                <td> </td>
                                <td><input type="url" form="add_url" name="url" placeholder="url"></td>
                                <td><input type="text" form="add_url" name="url_desc" placeholder="описание"></td>
                                <td><input type="submit" form="add_url" name="add_url" value="Добавить"></td>
                            </tr>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>URL</th>
                                <th>Описание</th>
                                <th>Act</th>
                            </tr>
                            <?php if($url_list){
                                foreach($url_list as $url):?>
                            <tr>
                                <td><?php echo $url['url_id'];?></td>
                                <td><a target="_blank" href="<?php echo $setting['script_url'].$url['url'];?>"><?php echo $url['url'];?></a></td>
                                <td><?php echo $url['url_desc'];?></td>
                                <td><a onclick="return confirm('Вы уверены?')" href="/admin/segments/delurl/<?php echo $url['url_id'];?>/<?php echo $segment['sid'];?>?token=<?php echo $_SESSION['admin_token'];?>" title="Удалить"><img src="/template/admin/images/del.png" alt="Delete"></a></td>
                            </tr>
                            
                            <?php endforeach; } else echo 'URL не добавлены';?>
                        </table>
                    </div>
                </div>

        </div>
    </form>
    
    <form id="add_url" action="" method="POST">
        <input type="hidden" name="sid" value="<?php echo $segment['sid'];?>">
        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>