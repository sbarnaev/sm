<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Диалог с пользователем</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a><a href="/admin/logout" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/courses/">Тренинги</a></li>
        <li><a href="/admin/answers/">Список ответов</a></li>
        <li>Диалог с пользователем</li>
    </ul>

    <div class="admin_top admin_top-flex">
        <div>
        <h3 class="traning-title">Задание:</h3>
            <?=$lesson['task'];?>
            <!--div class="edit-lesson-wrap"><a href="#" class="edit-lesson"><i class="icon-pencil"></i> <span>Настройки курса</span></a></div-->
        </div>
        <a title="Закрыть" class="close-link" href="/admin/answers/"><i class="icon-close"></i></a>
    </div>

    <div class="admin_form relative admin_form-2">
        <?php if($dialog_list):
            foreach($dialog_list as $item):
                $user = User::getUserNameByID($item['user_id']);?>
                <div class="dialog_item-one">
                    <div class="dialog_item">
                        <div class="dialog_item__left">
                            <img src="<?=User::getAvatarUrl($user, $setting);?>" alt="" />
                        </div>

                        <div class="dialog_item__right">
                            <ul class="dialog_item__crumbs">
                                <li><?=Course::getCourseNameByID($lesson['course_id']);?></li>
                                <li><?=$lesson['name'];?></li>
                            </ul>

                            <div class="dialog_item__user_name">
                                <p class="font-bold">
                                    <?php if($user):?>
                                        <a target="_blank" href="/admin/users/edit/<?=$user['user_id'];?>"><?=$user['user_name'];?></a>
                                    <?php endif;?>
                                </p>
                                <span class="small"><?=date("d.m.Y H:i:s", $item['date']);?></span>
                            </div>

                            <div class="user_message">
                                <?=$item['body'];?>
                            </div>

                            <?php if (!empty($item['attach'])):?>
                                <div class="attach-wrap mt-5">
                                    <div>
                                        <span class="small">Прикрепленные вложения:</span>
                                    </div>

                                    <div class="attach">
                                        <?php foreach(json_decode($item['attach'], true) as $attach):?>
                                            <a style="margin-right:10px;" target="_blank" href="<?=urldecode($attach['path']);?>">
                                                <nobr>
                                                    <img src="/template/admin/images/attachment.png" alt="" style="width:16px;margin-right:5px;">
                                                    <span><?=$attach['name'];?></span>
                                                </nobr>
                                            </a>
                                        <?php endforeach?>
                                    </div>
                                </div>
                            <?php endif?>
                        </div>

                        <div class="del_mess">
                            <a title="Удалить" class="link-delete" href="/admin/answers/delmess/<?=$item['id'];?>?token=<?=$_SESSION['admin_token'];?>"><span class="icon-remove"></span></a>
                        </div>
                    </div>
                </div>

                <div class="dialog_item_answer__wrap">
                    <?php $answers = Course::getAnswerFromMess($item['id']);
                    if($answers):
                        foreach($answers as $answer):
                            $user_data = User::getUserNameByID($answer['user_id']);?>
                            <div class="dialog_item_answer">
                                <div class="dialog_item answer">
                                    <div class="dialog_item__left">
                                        <img src="<?=User::getAvatarUrl($user_data, $setting);?>" alt="" />
                                    </div>

                                    <div class="dialog_item__right">
                                        <div class="dialog_item__user_name">
                                        <?php $curator = User::getUserNameByID($answer['user_id']);?>
                                            <h4><?php if($curator){?>
                                            <a target="_blank" href="/admin/users/edit/<?=$curator['user_id'];?>"><?php if($curator) echo $curator['user_name'];?></a>
                                            <?php } else {?>
                                            <?php if($curator) echo $curator['user_name'];?>
                                            <?php } ?>
                                            </h4>
                                            <span class="small"># <?=$answer['id'];?></span> <span class="small"><?=date("d.m.Y H:i:s", $answer['date']);?></span>
                                        </div>
                                        <div class="user_message">
                                            <?=$answer['body'];?>
                                        </div>
                                        <?php if (!empty($answer['attach'])):?>
                                            <div class="attach-wrap mt-5">
                                                <div>
                                                    <span class="small">Прикрепленные вложения:</span>
                                                </div>

                                                <div class="attach">
                                                    <?php foreach(json_decode($answer['attach'], true) as $attach):?>
                                                        <a style="margin-right:10px;" target="_blank" href="<?=urldecode($attach['path']);?>">
                                                            <nobr><img src="/template/admin/images/attachment.png" alt="" style="width:16px;margin-right:5px;">
                                                                <span><?=$attach['name'];?></span></nobr>
                                                        </a>
                                                    <?php endforeach?>
                                                </div>
                                            </div>
                                        <?php endif?>
                                    </div>

                                    <div class="del_mess">
                                        <a title="Удалить ответ" class="link-delete" href="/admin/answers/delmess/<?=$answer['id'];?>?token=<?=$_SESSION['admin_token'];?>"><span class="icon-remove"></span></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;?>
                    <?php endif;?>
                </div>
            <?php endforeach;?>

            <div class="message_box">
                <?php if(!Course::getStatusCompleteLesson($user['user_id'], $lesson['lesson_id'])):?>
                    <form action="" method="POST">
                        <div class="task-accepted">
                            <label>
                                <input type="hidden" name="success" value="1">
                                <input type="hidden" name="user_name" value="<?=$user['user_name'];?>">
                                <input type="hidden" name="user_email" value="<?=$user['email'];?>">
                                <input type="submit" value="Принять" class="noaccepted" name="post_message">
                            </label>
                        </div>
                    </form>
                <?php else:?>
                    <div class="task-accepted">
                        <label>
                            <span class="accepted"><i class="icon-check"></i>Принято</span>
                        </label>
                    </div>
                <?php endif;?>

                <form enctype="multipart/form-data" action="" method="POST">
                    <div class="textarea__row">
                        <div class="textarea__left">
                            <textarea name="message" class="editor"></textarea>
                            <div class="attach" style="margin-top:17px;">
                                <label>Пикрепить файлы:</label>
                                <input type="file" multiple name="lesson_attach[]">
                            </div>
                        </div>

                        <div class="textarea__righr">
                            <input type="hidden" name="user_name" value="<?=$user['user_name'];?>">
                            <input type="hidden" name="user_email" value="<?=$user['email'];?>">
                            <input type="submit" value="Ответить" class="button-yellow-border" name="post_message">
                        </div>
                    </div>
                </form>
            </div>
        <?php endif;?>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>