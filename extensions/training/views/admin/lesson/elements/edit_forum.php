<?php defined('BILLINGMASTER') or die;?>

<form enctype="multipart/form-data" action="/admin/training/lessons/element/edit/<?=$element['id'];?>" method="POST">
    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    <input type="hidden" name="training_id" value="<?=$lesson['training_id'];?>">

    <div class="modal-admin_top">
        <h3 class="modal-traning-title"><span class="modal-traning-title-icon">
            <img width="22" src="/extensions/training/web/admin/images/icons/forum.svg" alt=""></span>Форум
        </h3>
        <ul class="modal-nav_button">
            <li><input type="submit" name="edit_element" value="Сохранить" class="button save button-green"></li>
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
                        <label class="custom-radio"><input name="params[is_allow_create_topics]" type="radio" value="1"<?if($element['params']['is_allow_create_topics']) echo ' checked="checked"';?>>
                            <span>Вкл</span>
                        </label>
                        <label class="custom-radio"><input name="params[is_allow_create_topics]" type="radio" value="0"<?if(!$element['params']['is_allow_create_topics']) echo ' checked="checked"';?>>
                            <span>Выкл</span>
                        </label>
                    </span>
                </div>
            </div>

            <div class="col-1-2">
                <div class="width-100"><label>Служебное название</label>
                    <input type="text" name="params[name]" value="<?=$element['params']['name'];?>" placeholder="Название форума в списке" required="required">
                </div>
            </div>

            <div class="col-1-2">
                <div class="width-100"><label>Форум</label>
                    <div class="select-wrap">
                        <select name="params[branch_id]">
                            <?php $branches = Forum2::getBranchList();
                            if($branches):
                                foreach($branches as $branch):?>
                                    <option value="<?=$branch['branch_id'];?>"<?php if($element['params']['branch_id'] == $branch['branch_id']) echo ' selected="selected"';?>><?=$branch['name'];?></option>
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
                        <?php $branch_id = $element['params']['branch_id'] ?: 0;
                        $topics = $branches ? Forum2::getTopics($branch_id) : null;
                        if($topics):
                            $attached_topics = Forum2::getTopicsIds2Lesson($lesson['lesson_id']);
                            foreach($topics as $topic):?>
                                <option value="<?=$topic['topic_id'];?>"<?php if($attached_topics && in_array($topic['topic_id'], $attached_topics)) echo ' selected="selected"';?>><?=$topic['name'];?></option>
                            <?php endforeach;
                        endif;?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</form>

<script type="text/javascript" src="/extensions/forum/web/admin/js/training-element.js"></script>