<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Настройки интеграции с Автопилотом</h1>
        <div class="logout">
            <a href="/" target="_blank"><?=System::Lang('GO_SITE');?></a>
            <a href="<?=$setting['script_url'];?>/admin/logout" class="red"><?=System::Lang('QUIT');?></a>
        </div>
    </div>

    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li>Настройки интеграции с Автопилотом</li>
    </ul>

    <form action="" method="POST">
        <div class="admin_top admin_top-flex">
            <div class="admin_top-inner">
                <div><img src="/template/admin/images/icons/nastr-tren.svg" alt=""></div>
                <div><h3 class="traning-title mb-0">Интеграция с Автопилотом и ВК</h3></div>
            </div>

            <ul class="nav_button">
                <li><input type="submit" name="save" value="Сохранить" class="button save button-white font-bold"></li>
                <li class="nav_button__last"><a class="button red-link" href="/admin/extensions/">Закрыть</a></li>
            </ul>
        </div>

        <?php if(isset($_GET['success'])):?>
            <div class="admin_message">Успешно!</div>
        <?php endif;?>

        <div class="admin_form">
            <h4 class="h4-border">Управление расширением</h4>
            <div class="row-line">
                <div class="col-1-2">
                    <p class="width-100">
                        <label>Включить расширение (должно быть настроено)</label>
                        <span class="custom-radio-wrap" style="min-height: 31px;margin-top: 13px;">
                            <label class="custom-radio">
                                <input name="status" type="radio" value="1" <?php if($autopilot['status'] == 1) echo 'checked';?>><span>Вкл</span>
                            </label>
                            <label class="custom-radio">
                                <input name="status" type="radio" value="0" <?php if($autopilot['status'] == 0) echo 'checked';?>><span>Откл</span>
                            </label>
                        </span>
                    </p>
                </div>

                <div class="col-1-2">
                    <p class="width-100"> </p>
                </div>
            </div>

            <h4 class="h4-border">Авторизация через ВК</h4>
            <div class="row-line">
                <div class="col-1-2">
                    <p class="width-100">
                        <label>Включить кнопку входа через ВКонтакте</label>
                        <span class="custom-radio-wrap" style="min-height: 31px;margin-top: 13px;">
                            <label class="custom-radio">
                                <input name="modules[login]" type="radio" value="1" <?php if($autopilot['modules']['login']) echo 'checked';?>><span>Вкл</span>
                            </label>
                            <label class="custom-radio">
                                <input name="modules[login]" type="radio" value="0" <?php if(!$autopilot['modules']['login']) echo 'checked';?>><span>Откл</span>
                            </label>
                        </span>
                    </p>

                    <p class="width-100"><label>Права доступа (список <a href="https://vk.com/dev.php?method=permissions" target="_blank">прав</a> через запятую)</label>
                        <input type="text" name="vk_auth_params[scope]" value="<?=$autopilot['vk_auth_params']['scope'];?>">
                    </p>

                    <p class="width-100"><label>Версия VK API</label>
                        <input type="text" name="vk_app[v]" value="<?=$autopilot['vk_app']['v'];?>">
                    </p>

                    <p class="width-100"><label>URL-адрес для перенаправления:</label>
                        <input type="text" disabled="" value="<?=$setting['script_url'].$autopilot['vk_auth_params']['redirect_uri'];?>" >
                    </p>
                </div>

                <div class="col-1-2">
                    <p class="width-100"><label>ID приложения</label>
                        <input type="text" name="vk_app[id]" value="<?=$autopilot['vk_app']['id'];?>" class="blur">
                    </p>

                    <p class="width-100"><label>Защищённый ключ</label>
                        <input type="text" name="vk_app[secret]" value="<?=$autopilot['vk_app']['secret'];?>" class="blur">
                    </p>

                    <p class="width-100"><label>Сервисный ключ доступа</label>
                        <input type="text" name="vk_app[service_key]" value="<?=$autopilot['vk_app']['service_key'];?>" class="blur">
                    </p>

                    <?php if (isset($autopilot['vk_app']['id']) && $autopilot['vk_app']['id']):?>
                        <p class="width-100"><label>Приложение ВКонтакте:</label>
                            <a href="https://vk.com/editapp?id=<?=$autopilot['vk_app']['id'];?>&section=options" rel="noopener noreferrer" target="_blank" style="margin-top: 5px;display: inline-block;"> Открыть настройки приложения</a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <h4 class="h4-border" style="margin-top: 40px;">Сообщения ВКонтакте</h4>
            <div class="row-line">
                <div class="col-1-2">
                    <p class="width-100">
                        <label>ID сообщества (отправителя) числом</label>
                        <input type="text" name="vk_club[id]" value="<?=$autopilot['vk_club']['id'];?>" class="blur">
                    </p>

                    <p class="width-100">
                        <label>Дублировать все уведомления в ВК</label>
                        <span class="custom-radio-wrap" style="min-height: 31px;margin-top: 13px;">
                            <label class="custom-radio"><input name="vk_club[notify]" type="radio" value="1" <?php if($autopilot['vk_club']['notify'] == 1) echo 'checked';?>> <span>Вкл</span> </label>
                            <label class="custom-radio"><input name="vk_club[notify]" type="radio" value="0" <?php if($autopilot['vk_club']['notify'] == 0) echo 'checked';?>> <span>Откл</span> </label>
                        </span>
                    </p>
                </div>

                <div class="col-1-2">
                    <p class="width-100">
                        <label>Ключ доступа сообщества</label>
                        <input type="text" name="vk_club[key]" value="<?=$autopilot['vk_club']['key'];?>" class="blur">
                    </p>

                    <p class="width-100">
                        <em>Примечание: на данный момент функция работает только если отправка почты настроена через Swift Mailer</em>
                    </p>
                </div>
            </div>

            <h4 class="h4-border" style="margin-top: 40px;">Интеграция с Автопилотом</h4>
            <div class="row-line">
                <div class="col-1-2">
                    <p class="width-100"><label>URL-адрес для запросов:</label>
                        <input type="text" disabled="" value="<?=$setting['script_url'];?>/autopilot/api" >
                    </p>

                    <p class="width-100">
                        <a href="/admin/settings/" rel="noopener noreferrer" target="_blank">Настроить поля интеграции</a>
                    </p>
                </div>

                <div class="col-1-2">
                    <p class="width-100">
                        <label>Ключ API:</label>
                        <input type="text" disabled="" value="<?=$setting['secret_key'];?>" class="blur">
                    </p>

                    <p class="width-100">
                        <?php if (isset($autopilot['vk_club']['id']) && $autopilot['vk_club']['id']): ?>
                             <a href="https://skyauto.me/groups/edit/<?=$autopilot['vk_club']['id'];?>#input-sm_url" rel="noopener noreferrer" target="_blank">Открыть настройки Автопилота</a>
                        <?php endif ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="reference-link">
            <a class="button-blue-rounding" target="_blank" href="https://support.school-master.ru/knowledge_base/item/232127"><i class="icon-info"></i>Справка по расширению</a>
        </div>

        <input type="hidden" name="token" value="<?=$_SESSION['admin_token'];?>" class="hide">
        <input type="hidden" name="vk_auth_params[redirect_uri]" value="<?=$autopilot['vk_auth_params']['redirect_uri'];?>" class="hide">
        <input type="hidden" name="vk_auth_params[response_type]" value="<?=$autopilot['vk_auth_params']['response_type'];?>" class="hide">
    </form>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>

</body>
</html>