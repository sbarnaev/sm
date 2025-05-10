<?php defined('BILLINGMASTER') or die;?>

<div id="modal_add_forum" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-add-elem">
        <div class="userbox modal-userbox-3">
            <form enctype="multipart/form-data" action="/admin/training/lessons/element/add" method="POST">
                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                <input type="hidden" name="training_id" value="<?=$training_id;?>">
                <input type="hidden" name="lesson_id" value="<?=$lesson_id;?>">
                <input type="hidden" name="element_type" value="<?=TrainingLesson::ELEMENT_TYPE_FORUM;?>">

                <div class="modal-admin_top">
                    <h3 class="modal-traning-title"><span class="modal-traning-title-icon">
                        <img width="22" src="/extensions/training/web/admin/images/icons/forum.svg" alt=""></span>Форум
                    </h3>

                    <ul class="modal-nav_button">
                        <li><input type="submit" name="add_element" value="Добавить" class="button save button-green"></li>
                        <li class="modal-nav_button__last">
                            <a class="button uk-modal-close uk-close modal-nav_button__close" href="#close"><i class="icon-close"></i></a>
                        </li>
                    </ul>
                </div>

                <div class="admin_form">
                    <div class="row-line">
                        <div class="col-1-2">
                            <div class="width-100"><label>Разрешить пользователям создавать темы</label>
                                <span class="custom-radio-wrap">
                                    <label class="custom-radio"><input name="params[is_allow_create_topics]" type="radio" value="1" checked>
                                        <span>Вкл</span>
                                    </label>
                                    <label class="custom-radio"><input name="params[is_allow_create_topics]" type="radio" value="0">
                                        <span>Выкл</span>
                                    </label>
                                </span>
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Служебное название</label>
                                <input type="text" name="params[name]" placeholder="Название форума в списке" required="required">
                            </div>
                        </div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Форум</label>
                                <div class="select-wrap">
                                    <select name="params[branch_id]">
                                        <?php $branches = Forum2::getBranchList();
                                        if($branches):
                                            foreach($branches as $branch):?>
                                                <option value="<?=$branch['branch_id'];?>"><?=$branch['name'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-1-2"></div>

                        <div class="col-1-2">
                            <div class="width-100"><label>Прикрепить темы к уроку</label>
                                <select size="6" class="multiple-select" name="topics[]" multiple="multiple">
                                    <?php $topics = $branches ? Forum2::getTopics($branches[0]['branch_id']) : null;
                                    if($topics):
                                        foreach($topics as $topic):?>
                                            <option value="<?=$topic['topic_id'];?>"><?=$topic['name'];?></option>
                                        <?php endforeach;
                                    endif;?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript" src="/extensions/forum/web/admin/js/training-element.js"></script>