<?php defined('BILLINGMASTER') or die;?>

<div class="admin_result">
    <p><strong>Список выданных сертификатов</strong></p>
    <?php if($stats):?>
    <div class="overflow-container">
        <table class="table fz-12">
            <thead>
                <tr>
                    <th class="text-left">ID</th>
                    <th class="text-left">Имя Фамилия</th>
                    <th class="text-left">Дата выдачи</th>
                    <th class="text-right"></th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($stats as $stat):?>
                    <tr>
                        <td class="text-left"><?=$stat['id'];?></td>
                        <td class="text-left"><a class="user-link" href="/admin/users/edit/<?=$stat['user_id'];?>" target="_blank"><?=$stat['user_name']?></a></td>
                        <td class="text-left"><?=date("d.m.Y H:i", $stat['date']);?> </td>
                        <td class="text-right">
                        <a target="_blank" href="<?=$this->setting['script_url'];?>/training/showcertificate/<?=$stat['url'];?>"> 
                        Посмотреть</td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
    <?php else:?>
        <p>Выданных сертификатов нет</p>
    <?php endif;?>
</div>