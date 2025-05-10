<?php defined('BILLINGMASTER') or die;
$events_finish_types = [
    'give_access' => 'Выдать доступ',
    'give_sertificate' => 'Выдать сертификат/диплом',
    'send_message' => 'Отправить сообщение'
];?>

<div id="modal_add_event" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-add-elem">
        <div class="userbox modal-userbox-3">
            <form enctype="multipart/form-data" action="/admin/training/eventsfinish/add" method="POST">
                <input type="hidden" name="training_id" value="<?=$training['training_id'];?>">
                <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">

                <div class="modal-admin_top">
                    <h3 class="modal-traning-title">Событие по окончанию тренинга</h3>
                    <ul class="modal-nav_button">
                        <li><input type="submit" name="events_save" value="Сохранить" class="button save button-white font-bold"></li>
                        <li class="modal-nav_button__last">
                            <a class="button uk-modal-close uk-close modal-nav_button__close" href="#close"><i class="icon-close"></i></a>
                        </li>
                    </ul>
                </div>

                <div class="admin_form">
                    <div class="row-line">
                        <div class="col-1-1">
                            <div class="width-100"><label>Тип события</label>
                                <div class="select-wrap">
                                    <select name="event_type">
                                        <?php foreach ($events_finish_types as $event_type => $event_title):
                                            if (Training::getEventFinishByType($training['training_id'], $event_type)) {
                                                continue;
                                            }?>
                                            <option value="<?=$event_type;?>" data-show_on="<?=$event_type;?>"><?=$event_title;?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row-line hidden" id="give_access">
                        <div class="col-1-1">
                            <div class="width-100"><label>Выберите группу</label>
                                <select class="multiple-select" name="give_access[access_groups][]" multiple="multiple">
                                    <?php if($group_list):
                                        foreach($group_list as $group):?>
                                            <option value="<?=$group['group_id'];?>"<?php if(isset($event_finish['access_groups']) && in_array($group['group_id'], $event_finish['access_groups'])) echo ' selected="selected"';?>><?=$group['group_title'];?></option>
                                        <?php endforeach;
                                    endif?>
                                </select>
                            </div>
                        </div>

                        <div class="col-1-1">
                            <?php if($membership):?>
                                <div class="width-100"><label>Выберите подписку</label>
                                    <select class="multiple-select" name="give_access[access_planes][]" multiple="multiple">
                                        <?php if($planes):
                                            foreach($planes as $plane):?>
                                                <option value="<?=$plane['id'];?>"<?php if(isset($event_finish['access_planes']) && in_array($plane['id'], $event_finish['access_planes'])) echo ' selected="selected"';?>><?=$plane['service_name'] ? $plane['service_name'] : $plane['name'];?></option>
                                            <?php endforeach;
                                        endif;?>
                                    </select>
                                </div>
                            <?php endif;?>
                        </div>
                    </div>

                    <div class="row-line hidden" id="send_message">
                        <div class="col-1-1">
                            <div class="width-100"><label>Сообщение</label>
                                <textarea class="editor" name="send_message[text]" rows="3" cols="40"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>