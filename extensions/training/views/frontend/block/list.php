<?php defined('BILLINGMASTER') or die;?>

<div class="block_list">
    <?php foreach ($block_list as $key => $block):?>
        <div class="cut training-block">
            <div class="block-heading__click">
                <div class="module-number"><?=$key+1;?> <?=System::Lang('MODULE');?></div>
                <h4 id="block_<?=$block['block_id'];?>" class="block-heading"><?=$block['name'];?></h4>
            </div>

            <div style="display: none;" class="mini_cut">
                <?php if ($lesson_list):
                    require(__DIR__ . '/../lesson/list.php');
                endif;?>
            </div>
        </div>
    <?php endforeach;
    unset($block)?>
</div>