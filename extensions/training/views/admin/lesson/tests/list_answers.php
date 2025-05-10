<?php defined('BILLINGMASTER') or die;?>

<div class="col-1-1" id="answers_for_question">
    <?php if($options):?>
        <table>
            <?php foreach($options as $key => $option):?>
                <tr>
                    <td class="text-center">
                        <label class="custom-chekbox-wrap">
                            <input type="hidden" name="answers[answer_<?=$key+1;?>][option_id]" value="<?=$option['option_id'];?>">
                            <input type="checkbox" value="1" class="answer-valid" name="answers[answer_<?=$key+1;?>][valid]"<?php if($option['valid']) echo ' checked="checked"';?>>
                            <span class="custom-chekbox"></span>
                        </label>
                    </td>

                    <td class="text-center">
                        <div style="width: 41px; height:31px;">
                            <?php if($option['cover']):?>
                                <img src="<?=$option['cover'];?>" style="max-height:100%;" alt="">
                            <?php endif;?>
                        </div>
                    </td>

                    <td class="text-center">
                        <input type="text" value="<?=$option['title'];?>" name="answers[answer_<?=$key+1;?>][title]" required="required">
                    </td>

                    <td class="text-center">
                        <input style="width: 70px;" type="text" value="<?=$option['points'];?>" name="answers[answer_<?=$key+1;?>][points]">
                    </td>

                    <td class="td-last">
                        <a class="link_delete ajax" href="/admin/training/test/answer/del/<?="$training_id/$lesson_id/{$quest_id}";?>" data-id="<?=$option['option_id'];?>" data-replace_block="#answers_for_question" title="Удалить">
                            <span class="icon-remove"</span>
                        </a>
                    </td>
                </tr>
            <?php endforeach;?>
        </table>
    <?php endif;?>
</div>