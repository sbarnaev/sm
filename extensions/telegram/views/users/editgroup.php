<?php defined('BILLINGMASTER') or die;?>
<div class="row-line">
    <div class="col-1-1 mb-0">
        <h4>Интеграция с Telegram</h4>
    </div>

    <div class="col-1-2">
        <div class="width-100"><label title="ID чатов, если их несколько разделите запятой">ID чатов для удаления из них пользователей, при удалении группы</label>
            <input type="text" name="del_tg_chats" value="<?php if($group['del_tg_chats']) echo $group['del_tg_chats'];?>">
        </div>
    </div>
</div>