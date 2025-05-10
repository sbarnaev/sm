<?php defined('BILLINGMASTER') or die;?>
<!-- 7 POSTBACK -->
<div>
    <div class="table-responsive">
        <form action="" method="POST" id="postbacks">
            <?/*<div>
                <label>Тип запросов:</label>
                <select name="postback[type]">
                    <option value="post"<?php if($postbacks['type'] == 'post') echo ' selected="selected"';?>>POST</option>
                    <option value="get"<?php if($postbacks['type'] == 'get') echo ' selected="selected"';?>>GET</option>
                </select>
            </div>*/?>

            <h3><?=System::Lang('POSTBACKS');?></h3>
            <div class="h4 requisites__subtitle"><?=System::Lang('USER_REG');?></div>
            <div class="modal-form-line">
                <input type="text" name="postback[register]" value="<?= $postbacks['register'];?>">
            </div>

            <div class="h4 requisites__subtitle"><?=System::Lang('OREDE_CREATION');?></div>
            <div class="modal-form-line">
                <input type="text" name="postback[add_order]" value="<?= $postbacks['add_order'];?>">
            </div>

            <div class="h4 requisites__subtitle"><?=System::Lang('OREDE_PAYMENT');?></div>
            <div class="modal-form-line">
                <input type="text" name="postback[pay_order]" value="<?= $postbacks['pay_order'];?>">
            </div>

            <div><label>
                    <input type="checkbox" name="postback[only_paid]"<?php if(isset($postbacks['only_paid']) && $postbacks['only_paid'] == 1) echo ' checked="checked"';?> value="1"> <?=System::Lang('FREE_ORDERS_DO_NOT_USE');?>
                </label>
            </div>

            <hr />
            <p><?=System::Lang('TAGS_FOR_PUTTING');?></p>
            <p>{NAME} - <?=System::Lang('USER_NAME_TAG');?><br />
                {EMAIL} - <?=System::Lang('USER_EMAIL_TAG');?><br />
                {PHONE} - <?=System::Lang('USER_PHONE_TAG');?><br />
                {USER_ID} - <?=System::Lang('USER_ID_TAG');?><br />
                {ORDER_ID} - <?=System::Lang('ORDER_ID_TAG');?><br />
                {ORDER_NUM} - <?=System::Lang('ORDER_NUM_TAG');?><br />
                {SUMM} - <?=System::Lang('ORDER_SUMM_TAG');?>
            </p>

            <?php $fb_api = System::CheckExtensension('facebookapi', 1);
            if($fb_api):?>
                <h3><?=System::Lang('EXTENSIONS');?></h3>
                <div class="h4 requisites__subtitle"><?=System::Lang('PIX_FACEBOOK_ID');?></div>
                <div class="modal-form-line"><input type="text" name="fb_pixel[pixel_fb_id]" value="<?= $fb_pixel['pixel_fb_id'];?>"></div>
                <div class="h4 requisites__subtitle"><?=System::Lang('ACSSES_MARK_PIX_FACEBOOK');?></div>
                <div class="modal-form-line"><input type="text" name="fb_pixel[access_token_fb]" value="<?= $fb_pixel['access_token_fb'];?>"></div>
            <?php endif;?>

            <div class="requisites__button">
                <input type="submit" class="button btn-blue" value="Сохранить" name="save_postback">
            </div>
        </form>
    </div>
</div>