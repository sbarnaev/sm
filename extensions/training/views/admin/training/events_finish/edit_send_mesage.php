<?php defined('BILLINGMASTER') or die;
if(isset($events_finish['send_message'])):
    $params = json_decode($events_finish['send_message']['params'], true);?>

    <div id="modal_edit_send_message" class="uk-modal">
        <div class="uk-modal-dialog uk-modal-dialog-3">
            <div class="userbox modal-userbox-3">
                <form enctype="multipart/form-data" action="/admin/training/eventsfinish/edit/<?=$events_finish['send_message']['id'];?>" method="POST">
                    <input type="hidden" name="training_id" value="<?=$training['training_id'];?>">
                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                    <input type="hidden" name="event_type" value="send_message">
                    <input type="hidden" name="params[title]" value="<?=$params['title'];?>">

                    <div class="admin_top admin_top-flex">
                        <h3 class="traning-title">Отправить сообщение</h3>
                        <ul class="nav_button">
                            <li><input type="submit" name="events_save" value="Сохранить" class="button save button-white font-bold"></li>
                            <li class="nav_button__last">
                                <a class="button red-link uk-modal-close uk-close" href="#close">Закрыть</a>
                            </li>
                        </ul>
                    </div>

                    <div class="admin_form">
                        <div class="row-line">
                            <div class="col-1-1">
                                <div class="width-100"><label>Сообщение:</label>
                                    <textarea class="editor" name="params[text]" rows="3" cols="40"><?=$params['text'];?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif;