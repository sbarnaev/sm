<?php defined('BILLINGMASTER') or die; ?>
<div class="row-line">
    <div class="col-1-1 mb-0">
        <h4>Содержимое</h4>
    </div>
    
    <div class="col-1-2">
        <div class="width-100"><label>Ориентация модуля:</label>
            <div class="select-wrap">
                <select name="widget[params][orient]">
                    <option value="gorizontal">Горизонтально</option>
                    <option value="vertical">Вертикально</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="col-1-2">
        <div class="width-100"><label>Разрешить загрузку аватарок:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][allow_avatar]" type="radio" value="1"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][allow_avatar]" type="radio" value="0"><span>Нет</span></label>
            </span>
        </div>
    </div>
    
    
    <div class="col-1-1 mb-0">
        <h4>Ссылки</h4>
    </div>

    <div class="col-1-2">
        <div class="width-100"><label>Ссылка редактировать профиль:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][editprofile]" type="radio" value="1" checked="checked"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][editprofile]" type="radio" value="0"><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки редактировать профиль:</label>
            <input type="text" name="widget[params][editprofile_title]" value="Редактировать">
        </div>
        <p>------</p>
        
        
        <div class="width-100"><label>Ссылка изменить пароль:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][editpass]" type="radio" value="1" checked="checked"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][editpass]" type="radio" value="0"><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки изменить пароль:</label>
            <input type="text" name="widget[params][editpass_title]" value="Пароль">
        </div>
        <p>------</p>
        
        <div class="width-100"><label>Партнёрка:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][partners]" type="radio" value="1" checked="checked"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][partners]" type="radio" value="0"><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на партнёрку:</label>
            <input type="text" name="widget[params][partners_title]" value="Партнёрка">
        </div>
        <p>------</p>
        
        
        <div class="width-100"><label>Авторская:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][authors]" type="radio" value="1" checked="checked"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][authors]" type="radio" value="0"><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на авторскую:</label>
            <input type="text" name="widget[params][authors_title]" value="Авторская">
        </div>
        <p>------</p>
        
        
        <div class="width-100"><label>Кураторская 2.0:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][curators2]" type="radio" value="1" checked="checked"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][curators2]" type="radio" value="0"><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на кураторскую 2.0:</label>
            <input type="text" name="widget[params][curators2_title]" value="Кураторская">
        </div>
        
        <p>------</p>
        <div class="width-100"><label>Форум:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][forum]" type="radio" value="1"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][forum]" type="radio" value="0" checked="checked"><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на форум:</label>
            <input type="text" name="widget[params][forum_title]" value="Форум">
        </div>

        <p>------</p>
        <div class="width-100"><label>Своя ссылка:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][custom_link]" type="radio" value="1"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][custom_link]" type="radio" value="0" checked="checked"><span>Нет</span></label>
            </span>
        </div>
        <div id="custom_link_url">
            <div class="width-100"><label>URL:</label>
                <input type="text" name="widget[params][custom_link_url]" value="">
            </div>
            <div class="width-100"><label>Анкор:</label>
                <input type="text" name="widget[params][custom_link_title]" value="">
            </div>
        </div>
        
    </div>
    
    <div class="col-1-2">
        <? // Тренинги 2.0 ?>
        <div class="width-100"><label>Ссылка на мои тренинги 2.0:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][mytraining2]" type="radio" value="1" checked="checked"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][mytraining2]" type="radio" value="0"><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на мои тренинги 2.0:</label>
            <input type="text" name="widget[params][mytraining2_title]" value="Мои тренинги">
        </div>
        <p>------</p>
        
        <? // Тренинги 1.0 ?>
        <div class="width-100"><label>Ссылка на мои тренинги 1.0:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][mytraining]" type="radio" value="1"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][mytraining]" type="radio" value="0" checked="checked"><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на мои тренинги 1.0:</label>
            <input type="text" name="widget[params][mytraining_title]" value="Мои курсы">
        </div>
        <p>------</p>
        
        
        <? // Мои заказы ?>
        <div class="width-100"><label>Ссылка на мои заказы:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][myorders]" type="radio" value="1" checked="checked"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][myorders]" type="radio" value="0"><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на мои заказы:</label>
            <input type="text" name="widget[params][myorders_title]" value="Мои заказы">
        </div>
        <p>------</p>
        
        
        <div class="width-100"><label>Ссылка на мои подписки:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][mymembership]" type="radio" value="1" checked="checked"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][mymembership]" type="radio" value="0"><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на мои подписки:</label>
            <input type="text" name="widget[params][mymembership_title]" value="Подписки">
        </div>

        <p>------</p>
        <div class="width-100"><label>Кураторская 1.0:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][curators]" type="radio" value="1"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][curators]" type="radio" value="0" checked="checked"><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки редактировать профиль:</label>
            <input type="text" name="widget[params][curators_title]" value="Кураторская старая">
        </div>
        
        <p>------</p>
        <!--div class="width-100"><label>Мои темы на форуме:</label>
            <span class="custom-radio-wrap">
                <label class="custom-radio"><input name="widget[params][forum_topics]" type="radio" value="1"><span>Да</span></label>
                <label class="custom-radio"><input name="widget[params][forum_topics]" type="radio" value="0" checked="checked"><span>Нет</span></label>
            </span>
        </div>
        <div class="width-100"><label>Название ссылки на форум:</label>
            <input type="text" name="widget[params][forum_topics_title]" value="Мои темы на форуме">
        </div-->
        
        
    </div>
</div>