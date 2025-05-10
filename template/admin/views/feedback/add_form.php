<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1>Создать форму</h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank">Перейти на сайт</a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red">Выход</a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>
            <a href="/admin/feedback/">Обратная связь</a>
        </li>
        <li>Создать форму</li>
    </ul>
    <form action="" method="POST">

        <div class="admin_top admin_top-flex">
            <h3 class="traning-title">Создать форму</h3>
            <ul class="nav_button">
                <li><input type="submit" name="addform" value="Создать" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/feedback/">Закрыть</a></li>
            </ul>
        </div>

        <div class="admin_form">
            <div class="row-line">
                <div class="col-1-2">
                    <h4>Основное</h4>
                    <p class="width-100"><label>Название: </label><input type="text" name="name" placeholder="Название формы" required="required"></p>
                    <p class="width-100"><label>Заголовок страницы: </label><input type="text" name="form[params][h1]" placeholder="Заголовок страницы"></p>
                    <p class="width-100"><label>Описание формы:</label><textarea name="form_desc" rows="3" cols="40"></textarea></p>
                    
                    <p class="width-100"><label>Редирект после отправки: </label><input type="text" name="form[params][redirect]" placeholder="URL редиректа"></p>
                    <p class="width-100" title="Если поле пустое, то письмо получит админ"><label>Email кому отправить данные с формы: </label><input type="email" name="form[params][recipient]" pattern="^\w+([.-]?\w+)*@\w+([.-]?\w+)*(\.\w{2,})+$" placeholder="Email"></p>
                    
                    <div class="width-100"><label>Статус: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="status" type="radio" value="1" checked=""><span>Вкл</span></label>
                            <label class="custom-radio"><input name="status" type="radio" value="0"><span>Откл</span></label>
                        </span>
                    </div>
                    
                    <p class="width-100"><label>Текст перед формой:</label><textarea name="form[params][before]" rows="3" cols="40"></textarea></p>
                    <p class="width-100"><label>Текст внизу формы:</label><textarea name="form[params][after]" rows="3" cols="40"></textarea></p>
                    
                    <div class="width-100"><label>Использовать по-умолчанию: </label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="default_form" type="radio" value="1" checked=""><span>Вкл</span></label>
                            <label class="custom-radio"><input name="default_form" type="radio" value="0"><span>Откл</span></label>
                        </span>
                    </div>
                    <input type="hidden" name="token" value="<?php echo $_SESSION['admin_token'];?>">
                </div>
                
                
                
                <div class="col-1-2">
                    <h4>SEO</h4>
                    <p class="width-100"><label>Title: </label><input type="text" name="form[params][title]" placeholder="Title страницы"></p>
                    <p class="width-100"><label>Meta Description:</label><textarea name="form[params][meta_desc]" rows="3" cols="40"></textarea></p>
                    <p class="width-100"><label>Meta Keys:</label><textarea name="form[params][meta_keys]" rows="3" cols="40"></textarea></p>
                </div>
                
                
                <div class="col-1-2">
                <h4>Поля формы</h4>
                
                <div class="width-100"><label>Поле имя: </label>
                    <div class="select-wrap">
                    <select name="form[params][name]">
                    <option value="1">Да, показывать</option>
                    <option value="2">Да, заполнять обязательно</option>
                    <option value="0">Нет</option>
                </select>
                </div>
                </div>
                
                <div class="width-100"><label>Поле телефон: </label>
                    <div class="select-wrap">
                    <select name="form[params][phone]">
                    <option value="1">Да, показывать</option>
                    <option value="2">Да, заполнять обязательно</option>
                    <option value="0">Нет</option>
                </select>
                </div>
                </div>
                
                
                <div class="width-100"><label>Поле email: </label>
                    <div class="select-wrap">
                    <select name="form[params][email]">
                    <option value="1">Да, показывать</option>
                    <option value="2">Да, заполнять обязательно</option>
                    <option value="0">Нет</option>
                </select>
                </div>
                </div>
                
                
                <div class="width-100"><label>Поле сообщение: </label>
                    <div class="select-wrap">
                    <select name="form[params][message]">
                    <option value="1">Да, показывать</option>
                    <option value="2">Да, заполнять обязательно</option>
                    <option value="0">Нет</option>
                </select>
                </div>
                </div>
                
                
                <div class="width-100"><label>Показывать политику: </label>
                    <div class="select-wrap">
                    <select name="form[params][politika]">
                    <option value="1">Да, показывать</option>
                    <option value="0">Нет</option>
                </select>
                </div>
                </div>
                
                <p class="width-100"><label>Надпись на кнопке Отправить: </label><input type="text" name="form[params][button_text]"></p>
                
                <p class="width-100"><label>Мин. время заполнения формы, сек.: </label><input type="text" name="form[params][min_time]"></p>
                    
                
                </div>
                
                
                <div class="col-1-2">
                <h4>Доп. поля формы</h4>
                
                    <div class="width-100"><label>Поле 1: </label>
                        <div class="select-wrap">
                        <select name="form[params][field1]">
                        <option value="no">Нет</option>
                        <option value="text">text</option>
                        <option value="radio">radio</option>
                        <option value="select">select</option>
                        <option value="chekbox">chekbox</option>
                    </select>
                    </div>
                    </div>
                    <p class="width-100"><label>Название поля 1: </label><input type="text" name="form[params][field1_name]" placeholder="Название поля"></p>
                    <p class="width-100"><label>Значения:</label><textarea name="form[params][field1_data]" rows="3" cols="40" placeholder="название опции=значение;"></textarea></p>
                    
                    <div class="width-100"><label>Поле 2: </label>
                        <div class="select-wrap">
                        <select name="form[params][field2]">
                        <option value="no">Нет</option>
                        <option value="text">text</option>
                        <option value="radio">radio</option>
                        <option value="select">select</option>
                        <option value="chekbox">chekbox</option>
                    </select>
                    </div>
                    </div>
                    <p class="width-100"><label>Название поля 2: </label><input type="text" name="form[params][field2_name]" placeholder="Название поля"></p>
                    <p class="width-100"><label>Значения:</label><textarea name="form[params][field2_data]" rows="3" cols="40" placeholder="название опции=значение;"></textarea></p>
                
                    
                </div>
                
                <div class="col-1-1">
                    <p class="width-100"><label>Сообщение после отправки: </label><textarea class="editor" name="form[params][message]"></textarea></p> 
                </div>
                
                <div class="col-1-1">
                    <p class="width-100"><label>Тема ответного письма: </label><input type="text" name="form[params][letter_subj]"></p>
                    <p class="width-100"><label>Ответное письмо: </label><textarea class="editor" name="form[params][letter]"></textarea></p> 
                    <p>[NAME] - имя пользователя</p>
                    <p>[EMAIL] - емейл пользователя</p>
                    <p>[PHONE] - телефон пользователя</p>
                </div>
                
                
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    </div>
</body>
</html>