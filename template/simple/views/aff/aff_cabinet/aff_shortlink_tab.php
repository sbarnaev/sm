<?php defined('BILLINGMASTER') or die;?>
<!-- 3 Короткие ссылки-->
<div>
    <!--div class="create_promo">
        <h3>Купоны на скидку</h3>
        <p>Вы можете выписать купоны на скидку для ваших клиентов.<br />
        - Скидка берется из вашей комиссии.<br />
        - Купон привязывает пользователя к вам.</p>
        
        <div class="create-short-link">
            <div class="create-short-link__input"><input type="text" name="promo_name" placeholder="Код купона"></div>
            <div class="create-short-link__input"><input type="text" name="promo_desc" placeholder="Описание"></div>
            <div class="create-short-link__input"><input type="text" name="percent" placeholder="% скидки "></div>
            <div><input type="submit" class="button btn-blue-thin" name="addpromo" value="Создать купон"></div>
        </div>
        
    </div-->
    
    
    
    <form action="" method="POST">
        <!--h3>Короткие ссылки</h3-->
        <p><strong><?=System::Lang('CREAT_A_PARTER_LINK_HERE');?></strong></p>
        <p><?=System::Lang('HOW_IT_WORK');?><br><?=System::Lang('HOW_IT_WORK_DESCRIPTION');?></p>
        <div class="create-short-link">
            <div class="create-short-link__input"><input type="url" placeholder="Ссылка, которую нужно сократить или сделать партнерской" name="url" required="required"></div>
            <div class="create-short-link__input"><input name="desc" type="text" placeholder="Описание ссылки"></div>
            <div><input type="submit" class="button btn-blue-thin" name="addlink" value="Создать"></div>
        </div>
    </form>

    <?php if($short_links):?>
        <h3 class="table-short-link__title"><?=System::Lang('LINK_CREATION');?></h3>
        <div class="table-responsive">
            <table class="usertable table-short-link">
                <tr>
                    <th><?=System::Lang('SHORT_LINK');?></th>
                    <th><?=System::Lang('DESCRIPTION');?></th>
                    <th><?=System::Lang('TARGET');?></th>
                    <th><?=System::Lang('DELETE');?></th>
                </tr>

                <?php foreach($short_links as $short_link):?>
                    <tr>
                        <td><input class="table-short-link__input" type="text" value="<?=$setting['script_url'].'/pr/'; echo $short_link['link_id'];?>"></td>
                        <td><?=$short_link['link_desc'];?></td>
                        <td><a target="_blank" href="<?=$short_link['url'];?>"><?=$short_link['url'];?></a></td>
                        <td><form action="" method="POST"><input type="hidden" name="link_id" value="<?=$short_link['link_id'];?>">
                            <button class="table-short-link__delete" type="submit" name="deletelink"><span class="icon-remove"></span></button>
                        </form></td>
                    </tr>
                <?php endforeach;?>
            </table>
        </div>
    <?php endif;?>
    
    
</div>