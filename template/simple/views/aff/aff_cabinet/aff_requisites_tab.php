<?php defined('BILLINGMASTER') or die;?>

<!-- 6 Реквизиты -->
<div class="requisites">
        <?php $req = unserialize($req['requsits']);
        $req_data = explode("\r\n", $params['params']['req']);
        if($req_data && array_filter($req_data, 'strlen')):?>
            <form action="" method="POST">
                <?php foreach($req_data as $req_item):
                    if (strpos($req_item, '=') === false) {
                        continue;
                    }

                    list($req_key, $req_val) = explode("=", $req_item);
                    if($req_key != 'rs'):?>
                        <div class="h4 requisites__subtitle"><?=$req_val;?></div>
                        <div class="modal-form-line">
                            <input type="text" name="req[<?=$req_key;?>]" value="<?=!empty($req) && array_key_exists($req_key, $req) ? $req[$req_key] : '';?>">
                        </div>
                    <?php else:?>
                        <div class="h4 requisites__subtitle"><?=$req_val;?></div>
                        <div class="modal-form-line">
                            <input placeholder="Номер счета" type="text" name="req[rs][rs]" value="<?=!empty($req) && array_key_exists($req_key, $req) ? $req[$req_key]['rs'] : ''?>">
                        </div>

                        <div class="modal-form-line">
                            <input placeholder="Название организации" type="text" name="req[<?=$req_key;?>][name]" value="<?=!empty($req) && array_key_exists($req_key, $req) ? $req[$req_key]['name'] : '';?>">
                        </div>

                        <div class="modal-form-line">
                            <input placeholder="БИК" type="text" name="req[<?=$req_key;?>][bik]" value="<?=!empty($req) && array_key_exists($req_key, $req) ? $req[$req_key]['bik'] : '';?>">
                        </div>

                        <div class="modal-form-line">
                            <input placeholder="ИНН" type="text" name="req[<?=$req_key;?>][itn]" value="<?=!empty($req) && isset($req[$req_key]['itn']) && array_key_exists($req_key, $req) ? $req[$req_key]['itn'] : '';?>">
                        </div>
                    <?php endif;
                endforeach;?>

                <div class="requisites__button"><label> </label>
                    <input type="submit" class="button btn-blue" value="Сохранить" name="save_req">
                </div>
            </form>
        <?php endif;?>
    </div>
</div>