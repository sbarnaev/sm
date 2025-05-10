<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Импорт пользователей</h1>
        <div class="logout">
            <a href="<?=$setting['script_url'];?>" target="_blank"><?=System::Lang('GO_SITE');?></a><a href="<?=$setting['script_url'];?>/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/users/">Пользователи</a></li>
        <li>Импорт пользователей</li>
    </ul>

    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="is_new" value="0">
        <?php if(isset($_GET['success'])):?>
            <div class="admin_message">
                Всего записей:  <?=intval($_GET['total']);?>
                <br />Успешно добавлено пользователей: <?=intval($_GET['success']);?>
                <br />Дублей: <?=$_GET['dupl'];?>
                <br />Исключены: <?=$_GET['wrong'];?>
            </div>
        <?php endif;?>
    
        <?php if(isset($_GET['fail'])):?>
            <div class="admin_warning">Ни один пользователь не добавлен</div>
        <?php endif;?>

        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div>
                    <img src="/template/admin/images/icons/import-user.svg" alt="">
                </div>
                <div>
                    <h3 class="traning-title mb-0">Импорт</h3>
                </div>
            </div>

            <ul class="nav_button">
                <li><input type="submit" name="import" value="Импортировать" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a href="<?=$setting['script_url'];?>/admin/users/">Закрыть</a></li>
            </ul>
        </div>

        <div class="admin_form">
            <h4 class="h4-border"><?=System::Lang('BASIC');?></h4>
            <div class="row-line">
                <div class="col-1-1">
                    <div class="width-100"><label>Выберите файл: (формат .csv или .txt) </label>
                        <input type="file" name="file">
                    </div>
                </div>

                <div class="col-1-2">
                    <h4><strong>Порядок полей</strong></h4>

                    <div class="width-100"><label>1 поле: </label>
                        <div class="select-wrap">
                            <select name="first_field">
                                <option value="email">Email</option>
                                <option value="name">Имя</option>
                                <option value="surname">Фамилия</option>
                                <option value="phone">Телефон</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100"><label>2 поле: </label>
                        <div class="select-wrap">
                            <select name="second_field">
                                <option value="name">Имя</option>
                                <option value="email">Email</option>
                                <option value="surname">Фамилия</option>
                                <option value="phone">Телефон</option>
                                <option value="none">Нет</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100"><label>3 поле:</label>
                        <div class="select-wrap">
                            <select name="third_field">
                                <option value="none">Нет</option>
                                <option value="email">Email</option>
                                <option value="name">Имя</option>
                                <option value="surname">Фамилия</option>
                                <option value="phone">Телефон</option>
                                <option value="city">Город</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100"><label>4 поле:</label>
                        <div class="select-wrap">
                            <select name="fourth_field">
                                <option value="none">Нет</option>
                                <option value="email">Email</option>
                                <option value="name">Имя</option>
                                <option value="surname">Фамилия</option>
                                <option value="phone">Телефон</option>
                                <option value="city">Город</option>
                            </select>
                        </div>
                    </div>

                    <div class="width-100"><label>5 поле:</label>
                        <div class="select-wrap">
                            <select name="five_field">
                                <option value="none">Нет</option>
                                <option value="email">Email</option>
                                <option value="name">Имя</option>
                                <option value="surname">Фамилия</option>
                                <option value="phone">Телефон</option>
                                <option value="city">Город</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="col-1-2">
                    <h4><strong>Настройки</strong></h4>
                    <div class="width-100"><label>Разделитель</label>
                        <input type="text" name="separator" value=",">
                    </div>

                    <div class="width-100"><label>Если имя отсутствует, заменять на:</label>
                        <input type="text" name="empty_name" value="Дорогой друг">
                    </div>

                    <div class="width-100"><label>Проверять правильность е-маил</label>
                        <span class="custom-radio-wrap">
                            <label class="custom-radio"><input name="validate" type="radio" value="1"><span>Да</span></label>
                            <label class="custom-radio"><input name="validate" type="radio" value="0" checked=""><span>Нет</span></label>
                        </span>
                    </div>

                    <div class="width-100"><label class="custom-chekbox-wrap">
                        <input type="checkbox" value="1" name="is_client">
                        <span class="custom-chekbox"></span>Клиент?
                    </label></div>

                    <div class="width-100"><label class="custom-chekbox-wrap">
                        <input type="checkbox" value="1" name="is_partner">
                        <span class="custom-chekbox"></span>Партнер?
                    </label></div>
                    <div class="width-100"><label class="custom-chekbox-wrap" title="Если галочка стоит, то все импортированые пользователи будут подписаны на рассылку">
                        <input type="checkbox" value="1" name="is_subs">
                        <span class="custom-chekbox"></span>Получать рассылку?
                    </label></div>
                </div>
            </div>

            <h4 class="h4-border mt-30">Действие после подписки</h4>
            <div class="row-line">
                <div class="col-1-2 mb-0">
                    <div class="width-100"><label>Добавить в группы: </label>
                        <select class="multiple-select" size="8" multiple="multiple" name="groups[]">
                            <?php $groups = User::getUserGroups();
                            foreach($groups as $group):?>
                                <option value="<?=$group['group_id'];?>"><?=$group['group_title'];?></option>
                            <?php endforeach;?>
                        </select>
                    </div>

                    <?php $responder = System::CheckExtensension('responder', 1);
                    if($responder && isset($acl['show_responder'])):?>
                        <div class="width-100"><label>Подписать на автосерию:</label>
                            <div class="select-wrap">
                                <select name="responder">
                                    <option value="0">-- Выберите --</option>
                                    <?php $delivery_list = Responder::getDeliveryList(2);
                                    foreach($delivery_list as $delivery):?>
                                        <option value="<?=$delivery['delivery_id'];?>"><?=$delivery['name'];?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="width-100"><label>Отправить письмо с паролем</label>
                        <div class="select-wrap">
                            <select name="send_letter">
                                <option value="0">Не отправлять</option>
                                <option value="1">Отправить только новым пользователям</option>
                                <option value="2">Отправить всем пользователям</option>
                            </select>
                        </div>
                    </div>

                    <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>">
                </div>

                <div class="col-1-1">
                    <div class="width-100"><label>Письмо с паролем:</label>
                        <textarea class="editor" name="letter"><p>Здравствуйте, [CLIENT_NAME]!</p>
                        <p>Вы зарегистрированы на нашем сайте.</p>
                        <p>Ваш логин: это ваш email.</p>
                        <p>Пароль:&nbsp; [PASS]</p>
                        <p>Ссылка для входа в личный кабинет: [LINK]</p>
                        <p>-------</p>
                        <p>С уважением, администрация сайта.</p></textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
    <?php $title = 'Импорт пользователей';require_once(ROOT . '/lib/progressbar/html.php');?>
</div>
</body>

<script>
  $(document).ready(function() {
    let $file = $('[name="file"]');
    let $form = $file.parents('form');

    $form.find('input[name="import"]').click(function() {
      $form.find('input[name="is_new"]').val('1');
    });

    $form.submit(function(e) {
      e.preventDefault();

      if (!$file.val() || $file[0].files[0].size == 0) {
        return false;
      }

      let formData = new FormData();

      $form.find('input, select, textarea').each(function() {
        let name = $(this).attr('name');
        let value = null;
        if ($(this)[0].tagName != 'textarea') {
          value = $(this).attr('type') != 'checkbox' || $(this).is(':checked') ? $(this).val() : 0;
        } else {
          value = $(this).text();
        }

        if ($(this).attr('type') != 'file') {
          formData.append(name, value);
        } else {
          formData.append(name, $(this)[0].files[0]);
        }
      });

      $.ajax({
        url: "/admin/users/import",
        type: "POST",
        dataType : "json",
        cache: false,
        contentType: false,
        processData: false,
        data: formData,
        success: function(data) {
          if (data) {
            if (data.show_progress_bar) {
              $('.progressbar-wrap').show();
              $form.find('input[name="is_new"]').val('0');
              $form.submit();

              return false;
            }

            if (data.redirect) {
              window.location = data.redirect;
              return false;
            }

            $(".progressbar-loader").css('width', data.progress + '%');
            $(".progressbar-counter").html(data.progress + '%');

            if (data.is_finish) {
              setTimeout(function () {
                $('.progressbar-wrap').hide();
                $(".progressbar-loader").css('width', '1%');
                $(".progressbar-counter").html('1%');
              }, 500);
              window.location = '/admin/users/import?total='+data.total+'&success='+data.success+'&wrong='+data.wrong+'&dupl='+data.dupl;
            } else {
              $form.find('input[name="is_new"]').val('0');
              $form.submit();
            }
          }
        }
      });
    });
  });
</script>
</html>