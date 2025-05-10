<?php defined('BILLINGMASTER') or die;
require_once (ROOT . '/template/admin/layouts/admin-head.php'); ?>

<body id="page">
<?php require_once (ROOT . '/template/admin/layouts/admin-sidebar.php'); ?>

<div class="main">
    <div class="top-wrap">
        <h1>Cписок участников</h1>
        <div class="logout">
            <a href="/" target="_blank"><?php echo System::Lang('GO_SITE');?></a>
            <a href="/admin/logout" class="red"><?php echo System::Lang('QUIT');?></a>
        </div>
    </div>
    
    <ul class="breadcrumb">
        <li><a href="/admin">Дашбоард</a></li>
        <li><a href="/admin/extensions/">Расширения</a></li>
        <li><a href="/admin/telegramsetting/">Настройки Telegram</a></li>
        <li>Cписок участников</li>
    </ul>

    <?php if(isset($_GET['success'])):?>
        <div class="admin_message">Успешно!</div>
    <?php endif;?>

    <div class="admin_form admin_form--margin-top">
        <table class="table">
            <thead>
                <th>ID</th>
                <th class="text-left">ID в Telegram</th>
                <th class="text-left">Имя пользователя</th>
                <th class="text-left">Имя</th>
                <th class="text-left">Фамилия</th>
                <th class="td-last">Act</th>
            </thead>

            <tbody>
                <?php if($members):
                    foreach($members as $member):?>
                        <tr>
                            <td><a href="/admin/users/edit/<?=$member['sm_user_id'];?>" target="_blank"><?=$member['sm_user_id'];?></a></td>
                            <td class="text-left"><?=$member['user_id'] ? $member['user_id'] : '--';?></td>
                            <td class="text-left"><?=$member['user_name'] ? $member['user_name'] : '--';?></td>
                            <td class="text-left"><?=$member['first_name'] ? $member['first_name'] : '--';?></td>
                            <td class="text-left"><?=$member['last_name'] ? $member['last_name'] : '--';?></td>
                            <td><a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/telegramsetting/delmember/<?=$member['sm_user_id'];?>?token=<?=$_SESSION['admin_token'];?>" title="Удалить"><i class="fas fa-times" aria-hidden="true"></i></a></td>
                        </tr>
                    <?php endforeach;
                else:?>
                    <p>Участников пока нет</p>
                <?php endif;?>
            </tbody>
        </table>
    </div>
    <?php require_once(ROOT . '/template/admin/layouts/admin-footer.php');?>
</div>
</body>
</html>