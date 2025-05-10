<?php defined('BILLINGMASTER') or die;?>

<div class="row-line">
    <div class="col-1-1">
        <?php foreach($uniq_trainings as $training_a):?>
            <div class="block-collapse">
                <h4 class="block-collapse-head">
                    <span class="section-name"><?=Training::getTrainingNameByID($training_a['training_id']);?></span>
                    <?php $sections = TrainingSection::getSections($training_a['training_id'], null);
                    if($user_curators && array_key_exists($training_a['training_id'], $user_curators)):
                        $key_curators_trainig = array_search("0", array_column($user_curators[$training_a['training_id']], 'section_id'));
                        if($key_curators_trainig !== false):?>
                            <div class="kurator-line">
                                <span>Куратор:</span>
                                <span><?=$user_curators[$training_a['training_id']][$key_curators_trainig]['user_name'];?></span>
                                <span>
                                    <a class="kurator-edit" href="#ModalCurator" onclick="ChangeCurator(this);" data-set-training-id="<?=$training_a['training_id'];?>" data-set-section-id="0" data-set-curator-id="<?=$user_curators[$training_a['training_id']][$key_curators_trainig]['curator_id'];?>" data-uk-modal="{center:true}"><i class="icon-pencil"></i></a>
                                </span>
                            </div>
                        <?php else:?>
                            <div class="kurator-line">
                                <span>Куратор:</span>
                                <span>
                                    <a href="#ModalCurator" onclick="ChangeCurator(this);" data-set-training-id="<?=$training_a['training_id'];?>" data-set-section-id="0" data-uk-modal="{center:true}">Назначить</a>
                                </span>
                            </div>
                        <?php endif;
                    else:?>
                        <div class="kurator-line"><span>Куратор:</span>
                            <span>
                                <a href="#ModalCurator" onclick="ChangeCurator(this);" data-set-training-id="<?=$training_a['training_id'];?>" data-set-section-id="0" data-uk-modal="{center:true}">Назначить</a>
                            </span>
                        </div>
                    <?php endif;?>
                    
                    <span class="icon-down"></span>
                </h4>

                <div class="block-collapse-inner" style="display: none;">
                    <div class="overflow-container">
                        <?php if($sections):
                            foreach($sections as $section):?>
                                <div class="block-collapse">
                                    <h4 class="block-collapse-head">
                                        <span class="section-name"><?=$section['name'];?></span>
                                        <?php if($user_curators && array_key_exists($training_a['training_id'], $user_curators)):
                                            $key_curator = array_search($section['section_id'], array_column($user_curators[$training_a['training_id']], 'section_id'));
                                            if($key_curator !== false):?>
                                                <div class="kurator-line">
                                                    <span>Куратор:</span>
                                                    <span><?=$user_curators[$training_a['training_id']][$key_curator]['user_name'];?></span>
                                                    <span>
                                                        <a class="kurator-edit" href="#ModalCurator" onclick="ChangeCurator(this);" data-set-training-id="<?=$training_a['training_id'];?>" data-set-section-id="<?=$section['section_id'];?>" data-set-curator-id="<?=$user_curators[$training_a['training_id']][$key_curator]['curator_id'];?>" data-uk-modal="{center:true}"><i class="icon-pencil"></i></a>
                                                    </span>
                                                </div>
                                            <?php else:?>
                                                <div class="kurator-line">
                                                    <span>Куратор:</span>
                                                    <span>
                                                        <a href="#ModalCurator" onclick="ChangeCurator(this);" data-set-training-id="<?=$training_a['training_id'];?>" data-set-section-id="<?=$section['section_id'];?>" data-uk-modal="{center:true}">Назначить</a>
                                                    </span>
                                                </div>
                                            <?php endif;
                                        else:?>
                                            <div class="kurator-line">
                                                <span>Куратор:</span>
                                                <span>
                                                    <a href="#ModalCurator" onclick="ChangeCurator(this);" data-set-training-id="<?=$training_a['training_id'];?>" data-set-section-id="<?=$section['section_id'];?>" data-uk-modal="{center:true}">Назначить</a>
                                                </span>
                                            </div>
                                        <?php endif;?>

                                        <span class="icon-down"></span>
                                    </h4>

                                    <div class="block-collapse-inner" style="display: none;">
                                        <!-- ***************  БЛОКИ которые внутри раздела ******************* -->
                                        <?php $blocks = TrainingBlock::getBlocks($training_a['training_id'],  $section['section_id'], null);
                                        if($blocks):?>
                                            <?php foreach($blocks as $block):?>
                                                <div class="block-collapse">
                                                    <h4 class="block-collapse-head"><span class="section-name"><?=$block['name'];?></span>
                                                        <span class="icon-down"></span></h4>
                                                    <div class="block-collapse-inner" style="display: none;">
                                                        <div class="overflow-container">
                                                            <table class="table kurator-table">
                                                                <?php $order_by = 2;
                                                                $lessons_list = TrainingLesson::getLessons($training_a['training_id'], $section['section_id'], $block['block_id']);
                                                                if($lessons_list):
                                                                    foreach($lessons_list as $lesson):
                                                                        $lesson_complet = TrainingLesson::getLessonCompleteStatus($lesson['lesson_id'], $id);?>
                                                                        <tr>
                                                                            <td class="td-100"><?=$lesson['name'];?>
                                                                                <span style="display: none"><?='ID: '.$lesson['lesson_id'];?></span>
                                                                            </td>
                                                                            <!--<td>Авто</td>
                                                                        <td>01.03.2020</td>-->
                                                                        <td class=""><?=$lesson_complet == 3 ? 'выполнен' : ($lesson_complet === 0 ? 'вошел в урок' : 
                                                                                        ($lesson_complet == 2 ? 'не сдал' : ($lesson_complet === false ? 'не вошел в урок' : 'не проверен')));?>
                                                                            </td>
                                                                            <?php if ($lesson_complet || $lesson_complet === 0):?> 
                                                                                <td class="text-right">
                                                                                    <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/users/delcompletelesson/<?=$lesson['lesson_id'];?>?token=<?=$_SESSION['admin_token'];?>&user_id=<?=$user['user_id'];?>&newtr" 
                                                                                    title="Удалить прохождение урока, включая содержимое ответов и всю историю">
                                                                                        <i class="fas fa-times" aria-hidden="true"></i>
                                                                                    </a>        
                                                                                </td>
                                                                            <?php endif;?>
                                                                        </tr>
                                                                    <?php endforeach;
                                                                endif;?>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach;?>
                                        <?php endif;?>

                                        <!-- ***************  БЛОКИ которые внутри раздела ******************* -->
                                        <div class="overflow-container">
                                            <table class="table kurator-table">
                                                <?php $order_by = 2;
                                                $lessons_list = TrainingLesson::getLessons($training_a['training_id'], $section['section_id'], 0);
                                                if($lessons_list):
                                                    foreach($lessons_list as $lesson):
                                                        $lesson_complet = TrainingLesson::getLessonCompleteStatus($lesson['lesson_id'], $id);?>
                                                        <tr>
                                                            <td class="td-100"><?=$lesson['name'];?>
                                                                <span style="display: none"><?='ID: '.$lesson['lesson_id'];?></span>
                                                            </td>
                                                            <!--<td>Авто</td>
                                                           <td>01.03.2020</td>-->
                                                           <td class=""><?=$lesson_complet == 3 ? 'выполнен' : ($lesson_complet === 0 ? 'вошел в урок' : 
                                                                           ($lesson_complet == 2 ? 'не сдал' : ($lesson_complet === false ? 'не вошел в урок' : 'не проверен')));?>
                                                            </td>
                                                            <?php if ($lesson_complet || $lesson_complet === 0):?> 
                                                                <td class="text-right">
                                                                    <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/users/delcompletelesson/<?=$lesson['lesson_id'];?>?token=<?=$_SESSION['admin_token'];?>&user_id=<?=$user['user_id'];?>&newtr" 
                                                                    title="Удалить прохождение урока, включая содержимое ответов и всю историю">
                                                                        <i class="fas fa-times" aria-hidden="true"></i>
                                                                    </a>        
                                                                </td>
                                                            <?php endif;?>
                                                        </tr>
                                                    <?php endforeach;
                                                endif;?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;
                        endif;?>

                        <!-- Тут блоки без разделов/секций -->
                        <?php $blocks = TrainingBlock::getBlocks($training_a['training_id'], 0, null);
                        if($blocks):?>
                            <?php foreach($blocks as $block):?>
                                <div class="block-collapse">
                                    <h4 class="block-collapse-head">
                                        <span class="section-name"><?=$block['name'];?></span>
                                        <span class="icon-down"></span>
                                    </h4>

                                    <div class="block-collapse-inner" style="display: none;">
                                        <div class="overflow-container">
                                            <table class="table kurator-table">
                                                <?php $order_by = 2;
                                                $lessons_list = TrainingLesson::getLessons($training_a['training_id'], null, $block['block_id']);
                                                if($lessons_list):
                                                    foreach($lessons_list as $lesson):
                                                        $lesson_complet = TrainingLesson::getLessonCompleteStatus($lesson['lesson_id'], $id);?>
                                                        <tr>
                                                            <td class="td-100"><?=$lesson['name'];?>
                                                                <span style="display: none"><?='ID: '.$lesson['lesson_id'];?></span>
                                                            </td>
                                                            <!--<td>Авто</td>
                                                           <td>01.03.2020</td>-->
                                                           <td class=""><?=$lesson_complet == 3 ? 'выполнен' : ($lesson_complet === 0 ? 'вошел в урок' : 
                                                                            ($lesson_complet == 2 ? 'не сдал' : ($lesson_complet === false ? 'не вошел в урок' : 'не проверен')));?>
                                                            </td>
                                                            <?php if ($lesson_complet || $lesson_complet === 0):?> 
                                                                <td class="text-right">
                                                                    <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/users/delcompletelesson/<?=$lesson['lesson_id'];?>?token=<?=$_SESSION['admin_token'];?>&user_id=<?=$user['user_id'];?>&newtr" 
                                                                    title="Удалить прохождение урока, включая содержимое ответов и всю историю">
                                                                        <i class="fas fa-times" aria-hidden="true"></i>
                                                                    </a>        
                                                                </td>
                                                            <?php endif;?>
                                                        </tr>
                                                    <?php endforeach;
                                                endif;?>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;
                        endif;?>

                        <table class="table kurator-table">
                            <?php $order_by = 2;
                            $lessons_list = TrainingLesson::getLessons($training_a['training_id'], 0, 0);
                            if($lessons_list):
                                foreach($lessons_list as $lesson):
                                    $lesson_complet = TrainingLesson::getLessonCompleteStatus($lesson['lesson_id'], $id);?>
                                    <tr>
                                        <td class="td-100"><?=$lesson['name'];?><br />
                                            <span style="display: none"><?='ID: '.$lesson['lesson_id'];?></span>
                                        </td>
                                        <!--<td>Авто</td>
                                        <td>01.03.2020</td>-->
                                        <td class=""><?=$lesson_complet == 3 ? 'выполнен' : ($lesson_complet === 0 ? 'вошел в урок' : 
                                                    ($lesson_complet == 2 ? 'не сдал' : ($lesson_complet === false ? 'не вошел в урок' : 'не проверен')));?>
                                        </td>
                                        <?php if ($lesson_complet || $lesson_complet === 0):?> 
                                            <td class="text-right">
                                                <a class="link-delete" onclick="return confirm('Вы уверены?')" href="/admin/users/delcompletelesson/<?=$lesson['lesson_id'];?>?token=<?=$_SESSION['admin_token'];?>&user_id=<?=$user['user_id'];?>&newtr" 
                                                title="Удалить прохождение урока, включая содержимое ответов и всю историю">
                                                    <i class="fas fa-times" aria-hidden="true"></i>
                                                </a>        
                                            </td>
                                        <?php endif;?>
                                    </tr>
                                <?php endforeach;
                            endif;?>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach;?>
    </div>
</div>

