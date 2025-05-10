<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/extensions/training/layouts/admin/admin-head.php');?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Диалог с пользователем</h1>
        <div class="logout">
            <a href="/" target="_blank">Перейти на сайт</a>
            <a href="/admin/logout" class="red">Выход</a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/training/">Тренинги</a></li>
        <li><a href="/admin/training/answers/">Список ответов</a></li>
        <li>Диалог с пользователем</li>
    </ul>

    <div class="admin_top admin_top-flex">
        <div>
            <h3 class="traning-title">Задание:</h3>
            <?php if($task) echo $task['text'];?>
        </div>
        <a title="Закрыть" class="close-link" href="/admin/answers/"><i class="icon-close"></i></a>
    </div>

    <div class="admin_form relative admin_form-2">
        <?php if($answer_list):
            foreach($answer_list as $answer):
                $user_answer = User::getUserById($answer['user_id']);?>
                <div class="dialog_item-one">
                    <div class="dialog_item">
                        <div class="dialog_item__left">
                            <img src="<?=User::getAvatarUrl($user_answer, $setting);?>" alt="" />
                        </div>

                        <div class="dialog_item__left">
                            <ul class="dialog_item__crumbs">
                                <li><?=$answer['training_name'];?></li>
                                <li><?=$answer['lesson_name'];?></li>
                            </ul>

                            <div class="dialog_item__user_name">
                                <p class="font-bold">
                                    <a target="_blank" href="/admin/users/edit/<?=$answer['user_id'];?>"><?=$answer['user_name'];?></a>
                                </p>
                                <span class="small"><?=date("d.m.Y H:i:s", $answer['create_date']);?></span>
                            </div>

                            <div class="user_message">
                                <?=html_entity_decode(base64_decode($answer['answer']));?>
                            </div>

                            <?php if (!empty($answer['attach'])):?>
                                <div class="attach mt-5">
                                    <div>
                                        <span class="small">Прикрепленные файлы:</span>
                                    </div>

                                    <div>
                                        <?php foreach(json_decode($answer['attach'], true) as $attach):?>
                                            <a class="answer_attach_link" target="_blank" href="<?=urldecode($attach['path']);?>">
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
                            <a title="Удалить" class="link-delete" href="/admin/training/answers/del/<?=$answer['id'];?>?token=<?=$_SESSION['admin_token'];?>"><span class="icon-remove"></span></a>
                        </div>
                    </div>
                </div>

                <div class="dialog_item_answer__wrap">
                    <?php $sub_answers = TrainingLesson::getCommentsByHomeworkID($answer['homework_id']);
                    if($sub_answers):
                        foreach($sub_answers as $sub_answer):
                            $user_sub_answer = User::getUserNameByID($sub_answer['user_id']);?>
                            <div class="dialog_item_answer">
                                <div class="dialog_item answer">
                                    <div class="dialog_item__left">
                                        <img src="<?=User::getAvatarUrl($user_sub_answer, $setting);?>" alt="" />
                                    </div>

                                    <div class="dialog_item__right">
                                        <div class="dialog_item__user_name">
                                            <h4>
                                                <a target="_blank" href="/admin/users/edit/<?=$sub_answer['user_id'];?>"><?=$sub_answer['user_name'];?></a>
                                            </h4>
                                            <span class="small"># <?=$sub_answer['comment_id'];?></span>
                                            <span class="small"><?=date("d.m.Y H:i:s", $sub_answer['create_date']);?></span>
                                        </div>

                                        <div class="user_message">
                                            <?=html_entity_decode(base64_decode($sub_answer['comment_text']));?>
                                        </div>

                                        <?php if (!empty($sub_answer['attach'])):?>
                                            <div class="attach mt-5">
                                                <div>
                                                    <span class="small">Прикрепленные файлы:</span>
                                                </div>

                                                <div>
                                                    <?php foreach(json_decode($sub_answer['attach'], true) as $attach):?>
                                                        <a class="answer_attach_link" target="_blank" href="<?=urldecode($attach['path']);?>">
                                                            <nobr><img src="/template/admin/images/attachment.png" alt="" style="width:16px;margin-right:5px;">
                                                                <span><?=$attach['name'];?></span>
                                                            </nobr>
                                                        </a>
                                                    <?php endforeach?>
                                                </div>
                                            </div>
                                        <?php endif?>
                                    </div>

                                    <div class="del_mess">
                                        <a title="Удалить ответ" class="link-delete" href="/admin/answers/delmess/<?=$sub_answer['id'];?>?token=<?=$_SESSION['admin_token'];?>"><span class="icon-remove"></span></a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach;?>
                    <?php endif;?>
                </div>
            <?php endforeach; ?>

            <div class="message_box">
                <div class="task-status">
                    <?php if($lesson_complete_status == TrainingLesson::HOMEWORK_SUBMITTED):?>
                        <form action="" method="POST">
                            <div style="display: flex; flex-wrap: nowrap">
                                <label>
                                    <input type="submit" value="Принять" class="task-accept" name="accept">
                                </label>
                                <label style="margin-left: 10px;">
                                    <input type="submit" value="Отклонить" class="task-noaccept" name="noaccept">
                                </label>
                            </div>

                            <input type="hidden" name="admin_token" value="<?=$_SESSION['admin_token'];?>">
                        </form>
                    <?php elseif($lesson_complete_status == TrainingLesson::HOMEWORK_DECLINE):?>
                        <label>
                            <span class="task-noaccepted">Отклонено</span>
                        </label>
                    <?php elseif($lesson_complete_status == TrainingLesson::HOMEWORK_ACCEPTED):?>
                        <label>
                            <span class="task-accepted"><i class="icon-check"></i>Принято</span>
                        </label>
                    <?php endif;?>
                </div>

                <form enctype="multipart/form-data" action="" method="POST">
                    <div class="textarea__row">
                        <div class="textarea__left">
                            <textarea name="reply" class="editor"></textarea>

                            <div class="attach" style="margin-top:17px;">
                                <label>Прикрепить файлы:</label>
                                <input type="file" multiple name="lesson_attach[]">
                            </div>
                        </div>

                        <div class="textarea__righr">
                            <input type="hidden" name="user_name" value="<?=$last_answer['user_name'];?>">
                            <input type="hidden" name="user_email" value="<?=$last_answer['user_email'];?>">
                            <input type="hidden" name="answer_id" value="<?=$last_answer['id'];?>">
                            <input type="submit" value="Ответить" class="button-yellow-border" name="post_message">
                        </div>
                    </div>

                    <input type="hidden" name="admin_token" value="<?=$_SESSION['admin_token'];?>">
                </form>
            </div>
        <?php endif?>
    </div>

    <?php require_once (ROOT . '/extensions/training/layouts/admin/admin-footer.php');?>
</div>
</body>
</html>