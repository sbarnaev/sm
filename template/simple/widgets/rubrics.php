<?php defined('BILLINGMASTER') or die;
$blog = System::CheckExtensension('blog', 1);
if($blog){
    $rubrics = Blog::getRubricList(1);
    if($rubrics):?>
    <ul class="rubrics_list">
    <?php foreach($rubrics as $rubric):?>
        <li><a href="<?php echo $setting['script_url'];?>/blog/<?php echo $rubric['alias']?>"><?php echo $rubric['name'];?></a></li>
    <?php endforeach;?>
    </ul>
    <?php endif;?>



<?php } else echo 'Блог не установлен'; ?>
