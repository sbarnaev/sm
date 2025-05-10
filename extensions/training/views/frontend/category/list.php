<?php defined('BILLINGMASTER') or die;?>

<div class="course_category">
    <div class="row course_category__row" data-uk-grid-match=" { target:'.category_cover' } ">
        <?php foreach($cat_list as $cat):?>
            <div class="col-1-3 course_category_item">
                <div class="category_cover">
                    <?php if(!empty($cat['cover'])):?>
                        <a href="/training/category/<?=$cat['alias'];?>">
                            <img src="/images/training/category/<?=$cat['cover'];?>" alt="<?=$cat['img_alt'];?>">
                        </a>
                    <?php endif; ?>
                </div>

                <div class="category_desc">
                    <h3 class="category_desc__title">
                        <a href="/training/category/<?=$cat['alias'];?>"><?=$cat['name'];?></a>
                    </h3>
                    <div class="course_count">Курсов: <?=Training::countTrainingInCategory($cat['cat_id'], 1);?></div>
                    <?=html_entity_decode($cat['cat_desc']);?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>