<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <h1>Просмотр темы</h1>
    <div class="logout">
        <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
    </div>
    <div class="nav_gorizontal">
        <ul>
            <li><a class="button button-red-rounding" href="/admin/forum/topics">Закрыть</a></li>
        </ul>
    </div>
    
        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-1">
                    
                    <div class="topic_message">
                        <div class="message_info">
                            <p><a href="/admin/users/edit/<?php echo $topic['user_id'];?>" target="_blank"><?php $topicstarter = User::getUserNameByID($topic['user_id']); echo $topicstarter['user_name'];?></a></p>
                            </div>
                            <div class="message_text">
                            <p class="mess_header"><?php echo $topic['topic_title'];?></p>
                            <?php echo $topic['topic_message'];?>
                        </div>
                    </div>
                    
                    <div class="message_list">
                    
                    <?php if($mess_list):
                    foreach($mess_list as $message):?>
                        <div class="message_item<?php if($message['status'] == 0) echo ' off';?>" id="mess<?php echo $message['mess_id'];?>">
                            <div class="message_info">
                                <p><a href="#mess<?php echo $message['mess_id'];?>"># <?php echo $message['mess_id'];?></a>
                                <br /><a href="/admin/users/edit/<?php echo $message['user_id'];?>" target="_blank"><?php $user_data = User::getUserNameByID($message['user_id']); echo $user_data['user_name'];?></a></p>
                            </div>
                            <div class="message_text">
                                <p class="mess_header"><?php echo $topic['topic_title'];?></p>
                                <?php echo $message['text'];?>
                                
                                <form action="" method="POST" id="edit_mess-<?php echo $message['mess_id']; ?>">
                                <input type="hidden" name="mess_id" value="<?php echo $message['mess_id']; ?>">
                                <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                                <input type="submit" class="save_mess" value="Изменить" name="update">
                                </form>
                                
                                <form class="del_mess_topic" id="del_mess-<?php echo $message['mess_id'];?>" action="" method="POST">
                                    <input type="hidden" name="mess_id" value="<?php echo $message['mess_id']; ?>">
                                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                                    <input type="submit" value="Удалить" name="delete">
                                </form>
                            </div>
                        </div>
                    
                    <?php endforeach; 
                    endif;?>
                     
                    </div>
                    
                    <div class="topic_answer" id="answer">
                    <h3>Ответить в теме:</h3>
                        <form action="" method="POST">
                        <p><textarea class="editor" name="message"></textarea></p>
                        <p><input type="submit" class="button" value="Ответить" name="answer">
                        <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>"></p>
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>