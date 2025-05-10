<?php defined('BILLINGMASTER') or die;?>
<!-- 2 Партнёрские ссылки -->
<div>
    <div class="table-responsive">
        <?php if($params['params']['aff_2_level'] > 0):?>
            <p><?=System::Lang('LINK_FOR_PARTNERS');?> <input style="min-width: 320px;max-width: 400px;" readonly onclick="this.select()" type="text" value="<?=$setting['script_url'];?>/aff?pr=<?=$userId;?>"></p>
            <br />
        <?php endif;?>

        <table class="usertable">
            <tr>
                <th><?=System::Lang('PRODUCT_NAME');?></th>
                <th><?=System::Lang('PRICE');?></th>
                <th><?=System::Lang('COMMISSION');?></th>
                <th><?=System::Lang('LINKS');?></th>
                <th><?=System::Lang('MAT');?></th>
            </tr>

            <?php if($links && $user['spec_aff'] == 0) {
                
                // Без особого режима партнёра
                foreach($links as $link):
                    if($link['external_landing'] == 0) {
                        
                        // внутренний лендинг
                        $url = $setting['script_url'].'/catalog/'.$link['product_alias'].'?pr='.$user['user_id'];
                        
                    } else {
                        
                        // внешний лендинг
                        if(isset($params['params']['get_params']) && $params['params']['get_params'] == 1 && $link['external_url'] != null){
                            
                            $url = $link['external_url'].'?pr='.$user['user_id'];
                            
                        } else $url = $setting['script_url'].'/ext/'.$link['product_id'].'/'.$user['user_id'];
                    }
                    $order_url = $setting['script_url'].'/buy/'.$link['product_id'].'?pr='.$user['user_id']; ?>
                    <tr>
                        <td class="not-aff_links"><?=$link['product_name'];?></td>
                        <td class="not-aff_links">
                        <?php 
                            $price = Price::getFinalPrice($link['product_id']);
                            if($price['real_price'] < $price['price']):?>
                            <div style="text-decoration: line-through;"><?=$price['price'];?> <?=$setting['currency'];?></div>
                            <?=$price['red_price'];?> <?=$setting['currency'];?>
                        <?php else:?>
                            <?=$price['real_price'];?> <?=$setting['currency'];?>
                        <?php endif;?>
                        </td>
                        <?php if($link['run_aff']==1):
                            if($req['custom_comiss']>0):?> 
                                <td class="not-aff_links"><?=$req['custom_comiss'];?>%</td>
                            <?php elseif(isset($link['product_comiss']) && $link['product_comiss']>0):?>
                                <td class="not-aff_links"><?=$link['product_comiss'];?>%</td>
                            <?php else:?>                                                            
                                <td class="not-aff_links">
                                    <?=$params['params']['aff_1_level'] ? "1 уровень - {$params['params']['aff_1_level']}%<br>" : '';?>
                                    <?=$params['params']['aff_2_level'] ? "2 уровень - {$params['params']['aff_2_level']}%<br>" : '';?>
                                    <?=$params['params']['aff_3_level'] ? "3 уровень - {$params['params']['aff_3_level']}%<br>" : '';?>
                                </td>
                            <?php endif;?>
                        <?php else:?>
                            <td class="not-aff_links"><?=System::Lang('NOT_CHARGED');?></td>
                        <?php endif;?>
                        <td class="aff_links">
                            <?php if($setting['enable_landing'] == 1 && $url == true):
                                if(isset($params['params']['speclinks']) && $params['params']['speclinks'] == 1 && $link['price'] == 0){

                                    $replace = array(
                                    '[PID]' => $user['user_id'],
                                    '[PROD_ID]' => $link['product_id'],
                                    );

                                    $ender = strtr($params['params']['speclinks_url'], $replace);

                                    $url = $link['external_url'].'?'.$ender;

                                }
                            ?>
                            <div class="table-form-line">
                                <span class="text-right"><?=System::Lang('LENDING');?></span><div class="table-form-input"><input readonly onclick="this.select()" type="text" value="<?=$url;?>" class="link_input"></div>
                            </div>
                            <?php endif; ?>
                            <div class="table-form-line">
                                <span class="text-right"><?=System::Lang('ORDER');?></span><div class="table-form-input"><input readonly onclick="this.select()" type="text" value="<?=$order_url;?>" class="order_link_input"></div>
                            </div>
                        </td>
                        <td class="not-aff_links"><?php if($link['ads'] != null):?><a class="text-decoration-none" target="_blank" href="/load/ads/<?=$link['ads']?>"><i class="icon-attach-1"></i>&nbsp;<?=System::Lang('DOWNLOAD');?></a>
                        <?php endif;?></td>
                    </tr>
                <?php endforeach;
            } else {
                // ОСОБЫЙ РЕЖИМ
                $aff_params = User::getProductsForSpecAff($userId); // список продуктов
                if($aff_params):
                    foreach($aff_params as $item):
                        $link = Product::getProductById($item['product_id']);
                        if($link['in_partner']==1):
                            if($link['external_landing'] == 0) {
                                $url = $setting['script_url'].'/catalog/'.$link['product_alias'].'?pr='.$user['user_id'];
                            } else {
                                
                                if(isset($params['params']['get_params']) && $params['params']['get_params'] == 1 && $link['external_url'] != null){
                            
                                    $url = $link['external_url'].'?pr='.$user['user_id'];
                                    
                                } else $url = $setting['script_url'].'/ext/'.$link['product_id'].'/'.$user['user_id'];
                            }
                            $order_url = $setting['script_url'].'/buy/'.$link['product_id'].'?pr='.$user['user_id'];?>
                            <tr>
                                <td class="not-aff_links"><?=$link['product_name'];?></td>
                                <td class="not-aff_links">
                                <?php if($link['red_price']>0):?>
                                    <div style="text-decoration: line-through;"><?=$link['price'];?> <?=$setting['currency'];?></div>
                                    <?=$link['red_price'];?> <?=$setting['currency'];?>
                                <?php else:?>
                                    <?=$link['price'];?> <?=$setting['currency'];?>
                                <?php endif;?>
                                </td>
                                <?php if($link['run_aff']==1):
                                    if($item['type']==1):?>
                                        <td class="not-aff_links"><?=System::Lang('FROM_FIRST_ORDER');?> <?=$item['comiss'];?>%</td>
                                    <?php elseif($item['type']==2):?>
                                        <td class="not-aff_links"><?=System::Lang('FROM_SECOND_ORDER');?> <?=$item['comiss'];?>%</td>
                                    <?php else:
                                        $lines = explode("\r\n",$item['float_scheme']);?>
                                        <td class="not-aff_links">
                                        <?php foreach ($lines as $line):
                                            $value = explode("=", $line);
                                            echo $value[0]." платеж - ".$value[1]."%<br>";
                                        endforeach;?>
                                        </td>
                                    <?php endif;?>
                                <?php else:?>
                                    <td class="not-aff_links"><?=System::Lang('NOT_CHARGED');?></td>
                                <?php endif;?>
                                <td class="aff_links">
                                    <?php if($setting['enable_landing'] == 1 && $url == true):
                                        if (isset($params['params']['speclinks']) && $params['params']['speclinks'] == 1 && $link['price'] == 0) {
                                            $replace = array(
                                                '[PID]' => $user['user_id'],
                                                '[PROD_ID]' => $link['product_id'],
                                            );

                                            $ender = strtr($params['params']['speclinks_url'], $replace);
                                            $url = $link['external_url'].'?'.$ender;
                                        }?>
                                        <div class="table-form-line"><span class="text-right"><?=System::Lang('LENDING');?></span>
                                            <div class="table-form-input">
                                                <input readonly onclick="this.select()" type="text" value="<?=$url;?>" class="link_input">
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="table-form-line"><span class="text-right"><?=System::Lang('ORDER');?></span>
                                        <div class="table-form-input">
                                            <input readonly onclick="this.select()" type="text" value="<?=$order_url;?>" class="order_link_input">
                                        </div>
                                    </div>
                                </td>

                                <td class="not-aff_links">
                                    <?php if($link['ads'] != null):?>
                                        <a class="text-decoration-none" target="_blank" href="/load/ads/<?=$link['ads']?>"><i class="icon-attach-1"></i>&nbsp;<?=System::Lang('DOWNLOAD');?></a>
                                    <?php endif;?>
                                </td>
                            </tr>
                        <?php endif;
                    endforeach;
                endif;
            }?>
        </table>
    </div>
</div>