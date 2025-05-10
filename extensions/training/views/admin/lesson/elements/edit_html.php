<?php defined('BILLINGMASTER') or die;?>

<form enctype="multipart/form-data" action="/admin/training/lessons/element/edit/<?=$element['id'];?>" method="POST">
    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
    <input type="hidden" name="training_id" value="<?=$lesson['training_id'];?>">

    <div class="modal-admin_top">
        <h3 class="modal-traning-title"><span class="modal-traning-title-icon"><img width="22" src="/extensions/training/web/admin/images/icons/html.svg" alt=""></span>Редактировать код</h3>
        <ul class="modal-nav_button">
            <li><input type="submit" name="edit_element" value="Сохранить" class="button save button-green"></li>
            <li class="modal-nav_button__last">
                <a class="button uk-modal-close uk-close modal-nav_button__close" href="#close"><i class="icon-close"></i></a>
            </li>
        </ul>
    </div>

    <div class="admin_form">
        <div class="row-line">
            <div class="col-1-1">
                <div class="width-100"><label>Произвольный html-код</label>
                    <textarea class="html-code" name="params[html]" rows="3" cols="40"><?=$element['params']['html'];?></textarea>
                </div>
            </div>

            <div class="col-1-2">
                <div class="width-100"><label>Служебное название</label>
                    <input type="text" name="params[name]" placeholder="Название элемента в списке" value="<?=$element['params']['name'];?>" required="required">
                </div>
            </div>

        </div>
    </div>
</form>