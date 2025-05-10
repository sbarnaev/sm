<?php defined('BILLINGMASTER') or die;?>
<!-- 5 Привлчечённые клиенты -->
<div>
    <div class="table-responsive">
        <?php if($clients):?>
            <p><?=System::Lang('TOTAL');?> <?=count($clients);?></p>

            <table class="usertable">
                <tr>
                    <th><?=System::Lang('NAME');?></th>
                    <th><?=System::Lang('EMAIL');?></th>
                    <th><?=System::Lang('REGISTRATION_DATE');?></th>
                </tr>

                <?php foreach($clients as $client):?>
                    <tr>
                        <td><?=$client['user_name'];?></td>
                        <td><?=$params['params']['hidden_email'] == 1 ? System::hideEmail($client['email']) : $client['email']; ?></td>
                        <td><?=date("d.m.Y H:i:s", $client['reg_date']);?></td>
                    </tr>
                <?php endforeach;?>
            </table>
        <?php endif;?>
    </div>
</div>