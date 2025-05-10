<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
    <h1><?php echo System::Lang('USER_LIST'.$role);?></h1>
    <div class="logout">
        <a href="<?php echo $setting['script_url'];?>" target="_blank"><?php echo System::Lang('GO_SITE');?></a><a href="<?php echo $setting['script_url'];?>/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
    </div>
    </div>
    <ul class="breadcrumb">
        <li>
            <a href="/admin">Дашбоард</a>
        </li>
        <li>Пользователи</li>
    </ul>

    <div class="nav_gorizontal">
        <ul class="nav_gorizontal__ul flex-right">
            <li class="nav_gorizontal__parent-wrap"><a class="button-red-rounding" href="<?php echo $setting['script_url'];?>/admin/users/create/">Добавить пользователя</a></li>
            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent nav_gorizontal__parent-yellow">
                    <a href="javascript:void(0);" class="nav-click button-yellow-rounding">Группы</a>
                    <span class="nav-click icon-arrow-down"></span>
                </div>
                <ul class="drop_down">
                    <li><a href="<?php echo $setting['script_url'];?>/admin/usergroups/add/">Создать группу</a></li>
                    <li><a href="<?php echo $setting['script_url'];?>/admin/usergroups/">Список групп</a></li>
                </ul>
            </li>
            <li class="nav_gorizontal__parent-wrap">
                <div class="nav_gorizontal__parent nav_gorizontal__parent-yellow">
                    <a href="javascript:void(0);" class="nav-click button-yellow-rounding">Действие</a>
                    <span class="nav-click icon-arrow-down"></span>
                </div>
                <ul class="drop_down">
                    <li><a href="<?php echo $setting['script_url'];?>/admin/users/export/">Экспорт →</a></li>
                    <li><a href="<?php echo $setting['script_url'];?>/admin/users/import/">Импорт ←</a></li>
                </ul>
            </li>
        </ul>
    </div>

    <div class="filter admin_form">
        <form action="" method="POST">
            <ul class="list">
                <li><a href="#">Фильтр по типу</a>
                    <ul>
                        <li><a href="/admin/users?role=admin"><?php echo System::Lang('ADMINS');?></a></li>
                        <li><a href="/admin/users?role=manager"><?php echo System::Lang('MANAGERS');?></a></li>
                        <li><a href="/admin/users?role=is_client"><?php echo System::Lang('CLIENTS');?></a></li>
                        <li><a href="/admin/users?role=is_partner"><?php echo System::Lang('PARTNERS');?></a></li>
                        <li><a href="/admin/users?role=is_author"><?php echo System::Lang('AUTHORS');?></a></li>
                        <li><a href="/admin/users?role=is_curator"><?php echo System::Lang('CURATORS');?></a></li>
                        <li><a href="/admin/users"><?php echo System::Lang('ALL_USERS');?></a></li>
                    </ul>
                </li>
            </ul>
            <div class="filter-row">
                <div class="">
                    <input type="text" name="text[email]" placeholder="E-mail" value="<?php echo (isset($_POST['text']['email']))?$_POST['text']['email']:''; ?>">
                </div>
                <div>
                    <div class="select-wrap">
                        <select name="user_group">
                            <option value="">Выбрать группу</option>
                            <option value="without" <?php if(isset($_POST['user_group']) && $_POST['user_group'] == 'without') echo 'selected="selected"';?>>
                                Без группы
                            </option>
                            <?php $groups = User::getUserGroups();
                            foreach($groups as $group):?>
                                <option value="<?=$group['group_id'];?>"<?php if(isset($_POST['user_group']) && $group['group_id'] == $_POST['user_group']) echo 'selected="selected"';?>>
                                    <?php echo $group['group_title'];?>
                                </option>
                            <?php endforeach;?>
                        </select>
                    </div>
                </div>
                <div class="order-filter-1-2">
                    <input type="text" name="text[name]" placeholder="Имя" value="<?php echo (isset($_POST['text']['name']))?$_POST['text']['name']:''; ?>">
                </div>
                <div class="order-filter-1-2">
                    <input type="text" name="text[vk_url]" placeholder="ВКонтакте" value="<?php echo (isset($_POST['text']['vk_url']))?$_POST['text']['vk_url']:''; ?>">
                </div>
                <div class="order-filter-1-2 mr-auto">
                    <input type="text" name="numbers[id]" placeholder="ID" value="<?php echo (isset($_POST['numbers']['id']))?$_POST['numbers']['id']:''; ?>">
                </div>
                <div>
                    <div class="button-group">
                        <a class="red-link-rounding" href="/admin/users/">Сбросить</a>
                        <input class="button-blue-rounding" type="submit" name="filter" value="Найти">
                    </div>
                </div>
            </div>
        </form>
        <?php if($is_pagination == false):?>
            <p>Всего по фильтру: <?php if($users) echo count($users);?></p>
        <?php endif;?>
    </div>
    
    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>

    <div class="admin_form admin_form--margin-top">
        <div class="overflow-container">
            <table class="table">
                <tr>
                    <th>ID</th>
                    <th class="text-left">Имя</th>
                    <th class="text-left">Дата регистрации</th>
                    <th class="td-last">Заказы</th>
                    <th class="td-last">Покупки</th>
                </tr>

                <?php if ($users):
                    foreach($users as $user):?>
                        <tr<?php if($user['status'] == 0) echo ' class="off" style="color:#d0cdce"'; if($user['status'] == 6) echo ' class="refund"'; ?>>
                            <td><?php echo $user['user_id'];?></td>
                            <td class="text-left">
                                <div class="table-user-name__wrap">
                                    <div class="table-user-name">
                                    <a href="<?php echo $setting['script_url'];?>/admin/users/edit/<?php echo $user['user_id'];?>"><?php echo $user['user_name'];?></a>
                                    <?php if($user['role'] == 'admin') echo '<span><i class="icon-businessman-1"></i></span>'; if($user['role'] == 'manager') echo '<span>Manager</span>';?>
                                    </div>
                                    <div class="table-user-mail">
                                    <?php echo $user['email'];?>
                                    </div>
                                </div>
                            </td>
                            <td class="text-left"><?php echo date("d.m.Y", $user['reg_date']);?></td>
                            <td class="td-last"><a href="/admin/users/edit/<?php echo $user['user_id'];?>" target="_blank">Смотреть</a></td>
                            <td class="td-last">
                                <?php if($user['is_client'] == 1):?>
                                    <span class="stat-yes"><i class="icon-stat-yes"></i></span>
                                <?php else:?>
                                    <span class="stat-no"></span>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach;
                else:
                    echo 'No users';
                endif;?>
            </table>
        </div>
    </div>

    <?php if(isset($is_pagination) && $is_pagination == true) {
        echo $pagination->get();
    }?>

    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>