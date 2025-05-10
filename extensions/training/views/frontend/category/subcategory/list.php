<?php defined('BILLINGMASTER') or die;?>

<div class="course_category">
    <div class="row course_category__row" data-uk-grid-match=" { target:'.category_cover' } ">
        <?php foreach($subcategory_list as $subcat):?>
            <div class="col-1-3 course_category_item">
                <div class="category_cover">
                    <?php if(!empty($subcat['cover'])):?>
                        <a href="/training/category/<?="{$category['alias']}/{$subcat['alias']}";?>">
                            <img src="/images/training/category/<?=$subcat['cover'];?>" alt="<?=$subcat['img_alt'];?>">
                        </a>
                    <?php endif; ?>
                </div>

                <div class="category_desc">
                    <h3 class="category_desc__title">
                        <a href="/training/category/<?="{$category['alias']}/{$subcat['alias']}";?>"><?=$subcat['name'];?></a>
                    </h3>
                    <div class="course_count">Курсов: <?=Training::countTrainingInCategory($subcat['cat_id'], 1);?></div>
                    <?=html_entity_decode($subcat['cat_desc']);?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>