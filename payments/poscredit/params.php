<?php defined('BILLINGMASTER') or die;
$params = unserialize(base64_decode($payment['params']));?>
<div class="row-line">
    <div class="col-1-2">
        <h4 class="h4-border">Параметры</h4>
        <p><label>Идентификатор магазина — accessID:</label>
            <input type="text" name="params[access_id]" value="<?=$params['access_id'];?>">
        </p>

        <p><label>Код партнера — userID:</label>
            <input type="text" name="params[user_id]" value="<?=$params['user_id'];?>">
        </p>

        <p><label>Хэш-пароль партнера — userToken:</label>
            <input type="text" name="params[user_token]" value="<?=$params['user_token'];?>">
        </p>
    </div>

    <div class="col-1-1">
        <p><label>Текст письма при изменении статуса по кредиту:</label>
            <textarea class="editor" name="params[letter2status_change]"><?=$params['letter2status_change'];?></textarea>
        </p>

        <p><strong>Переменные для подстановки в письма:</strong></p>
        <p class="small">
            [CLIENT_NAME] - имя клиента<br>
            [ORDER_ID] - ID заказа<br>
            [PROFILE_ID] - ID заявки<br>
            [CLIENT_EMAIL] - емейл клиента<br>
            [ORDER_SUM] - сумма заказа<br>
            [CURRENCY] - валюта заказа<br>
            [OLD_STATUS] - старый статус по кредиту<br>
            [NEW_STATUS] - новый статус по кредиту
        </p>
    </div>
</div>