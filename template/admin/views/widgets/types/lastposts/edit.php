<?php defined('BILLINGMASTER') or die; ?>

<div class="row-line">
    <div class="col-1-1 mb-0">
        <h4>Содержимое</h4>
    </div>

    <div class="col-1-2">
        <p><label>Кол-во записей:</label>
            <input type="text" value="<?php echo $params['params']['countpost']?>" name="widget[params][countpost]">
        </p>

        <p><label>Вывести статьи по ID (через запятую):</label>
            <input type="text" value="<?php echo $params['params']['from_id']?>" name="widget[params][from_id]">
        </p>
    </div>
</div>