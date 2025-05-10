<?php defined('BILLINGMASTER') or die;
if (isset($events_finish['give_access'])):
    $params = json_decode($events_finish['give_access']['params'], true);?>

    <div id="modal_edit_give_access" class="uk-modal">
        <div class="uk-modal-dialog uk-modal-dialog-3">
            <div class="userbox modal-userbox-3">
                <form enctype="multipart/form-data" action="/admin/training/eventsfinish/edit/<?=$events_finish['give_access']['id'];?>" method="POST">
                    <input type="hidden" name="training_id" value="<?=$training['training_id'];?>">
                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                    <input type="hidden" name="event_type" value="give_access">
                    <input type="hidden" name="params[title]" value="<?=$params['title'];?>">

                    <div class="admin_top admin_top-flex">
                        <h3 class="traning-title">Выдать доступ</h3>
                        <ul class="nav_button">
                            <li><input type="submit" name="events_save" value="Сохранить" class="button save button-white font-bold"></li>
                            <li class="nav_button__last">
                                <a class="button red-link uk-modal-close uk-close" href="#close">Закрыть</a>
                            </li>
                        </ul>
                    </div>

                    <div class="admin_form">
                        <div class="row-line">
                            <div class="col-1-2">
                                <div class="width-100"><label>Выберите группу:</label>
                                    <select class="multiple-select" name="params[access_groups][]" multiple="multiple">
                                        <?php if($group_list):
                                            foreach($group_list as $group):?>
                                                <option value="<?=$group['group_id'];?>"<?php if($params && in_array($group['group_id'], $params['access_groups'])) echo ' selected="selected"';?>><?=$group['group_title'];?></option>
                                            <?php endforeach;
                                        endif?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-1-2">
                                <?php if($membership):?>
                                    <div class="width-100"><label>Выберите подписку:</label>
                                        <select class="multiple-select" name="params[access_planes][]" multiple="multiple">
                                            <?php if($planes):
                                                foreach($planes as $plane):?>
                                                    <option value="<?=$plane['id'];?>"<?php if($params && in_array($plane['id'], $params['access_planes'])) echo ' selected="selected"';?>><?=$plane['service_name'] ? $plane['service_name'] : $plane['name'];?></option>
                                                <?php endforeach;
                                            endif;?>
                                        </select>
                                    </div>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif;