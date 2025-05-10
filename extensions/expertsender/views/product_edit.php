<h4 class="h4-border">Интеграция с ExpertSender</h4>
<div class="width-100" title="Рассылка писем при оформлении заказа"><label>Рассылка писем при оформлении заказа</label>
    <div class="select-wrap">
        <select name="rspndr_order">
            <option value="">Нет</option>
            <?php foreach ($expsndr['list'] as $sbscr):?>
                <option value="<?=$sbscr->Id;?>"<?=$expsndr['rspndr'] && $sbscr->Id == $expsndr['rspndr']['rspndr_order'] ? ' selected="selected"' : '';?>>
                    <?=$sbscr->Name;?>
                </option>
            <?php endforeach;?>
        </select>
    </div>
</div>

<div class="width-100" title="Рассылка писем при оплате заказа">
    <label>Рассылка писем при оплате заказа</label>
    <div class="select-wrap">
        <select name="rspndr_pay">
            <option value="">Нет</option>
            <?php foreach ($expsndr['list'] as $sbscr):?>
                <option value="<?=$sbscr->Id;?>"<?=$expsndr['rspndr'] && $sbscr->Id == $expsndr['rspndr']['rspndr_pay'] ? ' selected="selected"' : '';?>>
                    <?=$sbscr->Name;?>
                </option>
            <?php endforeach;?>
        </select>
    </div>
</div>