<?php defined('BILLINGMASTER') or die; ?>
<div class="row-line">
    <div class="col-1-1 mb-0">
        <h4>Содержимое</h4>
    </div>
    
    <div class="col-1-2">
        <div class="width-100"><label>Ориентация модуля:</label>
            <div class="select-wrap">
                <select name="widget[params][orient]">
                    <option value="gorizontal"<?php if($params['params']['orient'] == 'gorizontal') echo ' selected="selected"';?>>Горизонтально</option>
                    <option value="vertical"<?php if($params['params']['orient'] == 'vertical') echo ' selected="selected"';?>>Вертикально</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="col-1-2">
        <div class="width-100"><label>Разрешить загрузку аватарок:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][allow_avatar]" type="radio" value="1" <?php if(isset($params['params']['allow_avatar']) && $params['params']['allow_avatar'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][allow_avatar]" type="radio" value="0" <?php if(isset($params['params']['allow_avatar']) && $params['params']['allow_avatar'] == 0) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
    </div>
    
    <div class="col-1-1 mb-0">
        <h4>Ссылки</h4>
    </div>

    <div class="col-1-2">
        <div class="width-100"><label>Ссылка редактировать профиль:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][editprofile]" type="radio" value="1" <?php if(isset($params['params']['editprofile']) && $params['params']['editprofile'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][editprofile]" type="radio" value="0" <?php if(isset($params['params']['editprofile']) && $params['params']['editprofile'] == 0) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки редактировать профиль:</label>
            <input type="text" name="widget[params][editprofile_title]" value="<?php if(isset($params['params']['editprofile_title'])) echo $params['params']['editprofile_title'];?>">
        </div>
        <p>------</p>
        
        
        <div class="width-100"><label>Ссылка изменить пароль:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][editpass]" type="radio" value="1" <?php if(isset($params['params']['editpass']) && $params['params']['editpass'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][editpass]" type="radio" value="0" <?php if(isset($params['params']['editpass']) && $params['params']['editpass'] == 0) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки изменить пароль:</label>
            <input type="text" name="widget[params][editpass_title]" value="<?php if(isset($params['params']['editpass_title'])) echo $params['params']['editpass_title'];?>">
        </div>
        <p>------</p>
        
        <div class="width-100"><label>Партнёрка:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][partners]" type="radio" value="1" <?php if(isset($params['params']['partners']) && $params['params']['partners'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][partners]" type="radio" value="0" <?php if(isset($params['params']['partners']) && $params['params']['partners'] == 0) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на партнёрку:</label>
            <input type="text" name="widget[params][partners_title]" value="<?php if(isset($params['params']['partners_title'])) echo $params['params']['partners_title'];?>">
        </div>
        <p>------</p>
        
        
        <div class="width-100"><label>Авторская:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][authors]" type="radio" value="1" <?php if(isset($params['params']['authors']) && $params['params']['authors'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][authors]" type="radio" value="0" <?php if(isset($params['params']['authors']) && $params['params']['authors'] == 0) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на авторскую:</label>
            <input type="text" name="widget[params][authors_title]" value="<?php if(isset($params['params']['authors_title'])) echo $params['params']['authors_title'];?>">
        </div>
        <p>------</p>
        
        
        <div class="width-100"><label>Кураторская 2.0:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][curators2]" type="radio" value="1" <?php if(isset($params['params']['curators2']) && $params['params']['curators2'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][curators2]" type="radio" value="0" <?php if(isset($params['params']['curators2']) && $params['params']['curators2'] == 0) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на кураторскую 2.0:</label>
            <input type="text" name="widget[params][curators2_title]" value="<?php if(isset($params['params']['curators2_title'])) echo $params['params']['curators2_title'];?>">
        </div>
        <p>------</p>
        
        <?php $forum = System::CheckExtensension('forum2');
        if($forum):?>
        <div class="width-100"><label>Форум:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][forum]" type="radio" value="1" <?php if(isset($params['params']['forum']) && $params['params']['forum'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][forum]" type="radio" value="0" <?php if(isset($params['params']['forum']) && $params['params']['forum'] == 0) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на форум:</label>
            <input type="text" name="widget[params][forum_title]" value="<?php if(isset($params['params']['forum_title'])) echo $params['params']['forum_title'];?>">
        </div>
        <p>------</p>
        <?php endif;?>
        
        <div class="width-100"><label>Своя ссылка:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][custom_link]" type="radio" value="1"<?php if(isset($params['params']['custom_link']) && $params['params']['custom_link'] == 1) echo ' checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][custom_link]" type="radio" value="0"<?php if(!isset($params['params']['custom_link']) || !$params['params']['custom_link']) echo ' checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div id="custom_link_url">
            <div class="width-100"><label>URL:</label>
                <input type="text" name="widget[params][custom_link_url]" value="<?php if(isset($params['params']['custom_link_url'])) echo $params['params']['custom_link_url'];?>">
            </div>
            <div class="width-100"><label>Анкор:</label>
                <input type="text" name="widget[params][custom_link_title]" value="<?php if(isset($params['params']['custom_link_title'])) echo $params['params']['custom_link_title'];?>">
            </div>
        </div>
    </div>
    
    <div class="col-1-2">
        <? // Тренинги 2.0 ?>
        <div class="width-100"><label>Ссылка на мои тренинги 2.0:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][mytraining2]" type="radio" value="1" <?php if(isset($params['params']['mytraining2']) && $params['params']['mytraining2'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][mytraining2]" type="radio" value="0" <?php if(isset($params['params']['mytraining2']) && $params['params']['mytraining2'] == 0) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на мои тренинги 2.0:</label>
            <input type="text" name="widget[params][mytraining2_title]" value="<?php if(isset($params['params']['mytraining2_title'])) echo $params['params']['mytraining2_title'];?>">
        </div>
        <p>------</p>
        
        <? // Тренинги 1.0 ?>
        <div class="width-100"><label>Ссылка на мои тренинги 1.0:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][mytraining]" type="radio" value="1" <?php if(isset($params['params']['mytraining']) && $params['params']['mytraining'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][mytraining]" type="radio" value="0" <?php if(isset($params['params']['mytraining']) && $params['params']['mytraining'] == 0) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на мои тренинги 1.0:</label>
            <input type="text" name="widget[params][mytraining_title]" value="<?php if(isset($params['params']['mytraining_title'])) echo $params['params']['mytraining_title'];?>">
        </div>
        <p>------</p>
        
        
        <? // Мои заказы ?>
        <div class="width-100"><label>Ссылка на мои заказы:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][myorders]" type="radio" value="1" <?php if($params['params']['myorders'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][myorders]" type="radio" value="0" <?php if($params['params']['myorders'] == 0) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на мои заказы:</label>
            <input type="text" name="widget[params][myorders_title]" value="<?php if(isset($params['params']['myorders_title'])) echo $params['params']['myorders_title'];?>">
        </div>
        <p>------</p>
        
        
        <div class="width-100"><label>Ссылка на мои подписки:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][mymembership]" type="radio" value="1" <?php if(isset($params['params']['mymembership']) && $params['params']['mymembership'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][mymembership]" type="radio" value="0" <?php if(isset($params['params']['mymembership']) && $params['params']['mymembership'] == 0) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на мои подписки:</label>
            <input type="text" name="widget[params][mymembership_title]" value="<?php if(isset($params['params']['mymembership_title'])) echo $params['params']['mymembership_title'];?>">
        </div>
        <p>------</p>

        <div class="width-100"><label>Кураторская 1.0:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][curators]" type="radio" value="1" <?php if(isset($params['params']['curators']) && $params['params']['curators'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][curators]" type="radio" value="0" <?php if(!isset($params['params']['curators']) || !$params['params']['curators']) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки кураторскую 1.0:</label>
            <input type="text" name="widget[params][curators_title]" value="<?php if(isset($params['params']['curators_title'])) echo $params['params']['curators_title'];?>">
        </div>
        
        <?php if($forum):?>
        <p>------</p>
        <!--div class="width-100"><label>Мои темы на форуме:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][forum_topics]" type="radio" value="1" <?php if(isset($params['params']['forum_topics']) && $params['params']['forum_topics'] == 1) echo 'checked';?>><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][forum_topics]" type="radio" value="0" <?php if(isset($params['params']['forum_topics']) && $params['params']['forum_topics'] == 0) echo 'checked';?>><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на форум:</label>
            <input type="text" name="widget[params][forum_topics_title]" value="<?php if(isset($params['params']['forum_topics_title'])) echo $params['params']['forum_topics_title'];?>">
        </div-->
        <?php endif;?>
        
    </div>
</div>