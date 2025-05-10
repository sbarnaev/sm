<?php defined('BILLINGMASTER') or die;
$_SESSION['feedback'] = 1;
$now = time();
$form_id = $widget_params['params']['form_id'];
$form = System::getFormDataByDefault($form_id);

if($form):
    $params = unserialize(base64_decode($form['params']));?>
    
    <p><?=$params['params']['before'];?></p>
    
    <div class="userbox">
        <form action="/feedback?id=<?=$form_id;?>" method="POST">
            <?php if($params['params']['name'] > 0):?>
                <p><input type="text" name="name" placeholder="Имя" <?php if($params['params']['name'] == 2) echo ' required="required"';?>></p>
            <?php endif; ?>

            <?php if($params['params']['email'] == 1):?>
                <p><script>document.write(window.atob("PGlucHV0IHR5cGU9ImVtYWlsIiBuYW1lPSJlbWFpbCIgcGF0dGVybj0iXlx3KyhbLi1dP1x3KykqQFx3KyhbLi1dP1x3KykqKFwuXHd7Mix9KSskIiBwbGFjZWhvbGRlcj0iRS1tYWlsIj4="));</script></p>
            <?php elseif($params['params']['email'] == 2):?>
                <p><script>document.write(window.atob("PGlucHV0IHR5cGU9ImVtYWlsIiBuYW1lPSJlbWFpbCIgcGF0dGVybj0iXlx3KyhbLi1dP1x3KykqQFx3KyhbLi1dP1x3KykqKFwuXHd7Mix9KSskIiByZXF1aXJlZD0icmVxdWlyZWQiIHBsYWNlaG9sZGVyPSJFLW1haWwiPg=="));</script></p>
            <?php endif;?>

            <?php if($params['params']['phone'] > 0):?>
                <p><input type="text" name="phone" placeholder="Телефон" <?php if($params['params']['phone'] == 2) echo ' required="required"';?>></p>
            <?php endif; ?>
        
            <?php if($params['params']['field1'] != 'no') {
                echo renderField($params['params']['field1'], 1, $params['params']['field1_name'], $params['params']['field1_data']);
            }
        
            if ($params['params']['field2'] != 'no') {
                echo renderField($params['params']['field2'], 2, $params['params']['field2_name'], $params['params']['field2_data']);
            }?>
        
        
            <?php if($params['params']['message'] > 0):?>
                <p><textarea name="text" cols="55" rows="5" placeholder="Ваше сообщение"<?php if($params['params']['message'] == 2) echo ' required="required"';?>></textarea></p>
            <?php endif; ?>

            <?php if($params['params']['politika'] == 1):?>
                <p><label class="check_label" style="width: 100%;">
                        <input type="checkbox" name="politika" required="required"> <span><?=System::Lang('AGREED_TO_WRITE_PERSONAL_DATA');?></span>
                    </label>
                </p>
            <?php endif; ?>
        
            <p><input type="hidden" name="time" value="<?=time();?>"/>
				<input type="hidden" name="token_sm" value="<?php echo md5($now.'+'.$setting['secret_key']);?>"/>
                <input type="submit" class="btn-yellow text-uppercase font-bold button" name="feedback" value="<?=$params['params']['button_text'];?>">
            </p>
        </form>

        <p><?=$params['params']['after'];?></p>
    </div>
<?php endif;?>

<?php function renderField($type, $num, $name, $data) {
    switch($type){
        case 'text': 
            if($data == 'required') {
                $attr = ' required="required"';
            }

            $html = '<p><input type="text" name="field'.$num.'" placeholder="'.$name.'" '.$attr.'></p>';
            break;
        
        case 'radio':
            $options = explode(";", $data);
            $count = 1;
            $html = "<p><strong>$name</strong></p><ul>";

            foreach($options as $option){
                $data = explode('=', $option);
                $html .= '<li><input type="radio" id="field'.$num.$count.'" name="field'.$num.'" value="'.$data[1].'"> <label for="field'.$num.$count.'">'.$data[0].'</label></li>';
                $count++;
            }

            $html .= '</ul>';
            break;

        case 'select':
            $options = explode(";", $data);
            $html = '<p><strong>'.$name.'</strong></p><p><select name="field'.$num.'">';
            foreach($options as $option){
                $data = explode('=', $option);
                $html .= '<option value="'.$data[1].'">'.$data[0].'</option>';
            }

            $html .= '</select></p>';
            break;

        case 'chekbox':
            $options = explode(";", $data);
            $count = 1;
            $html = "<p><strong>$name</strong></p><ul>";

            foreach($options as $option) {
                $data = explode('=', $option);
                $html .= '<li><input type="checkbox" id="field'.$num.$count.'" name="field'.$num.'[]" value="'.$data[1].'"> <label for="field'.$num.$count.'">'.$data[0].'</label></li>';
                $count++;
            }

            $html .= '</ul>';
            break;
    }
    
    return $html;
}?>