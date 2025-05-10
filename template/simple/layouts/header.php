<?php defined('BILLINGMASTER') or die; 
$setting_main = System::getSettingMainpage();?>
<?php if(isset($_SESSION['admin_user']) && !isset($_SESSION['user'])):?>
<div class="admin_warning_front"><?=System::Lang('SYSTEM_ADMIN_SESSION_MESS');?> <a class="btn-blue-small" href="/admin/logout"><?=System::Lang('QUIT');?></a></div>
<?php endif;?>
<header class="header header__pressed-footer<?php if($setting['fix_head'] == 1) echo ' header-sticky';?>">
  <div class="layout">
    <div class="header__inner">
      <div class="logo">
        <a href="<?php echo $setting['script_url'];?>"><img
          src="<?php echo $setting['logotype'];?>" alt=""></a>
        <?php if(!empty($setting_main['slogan'])):?><span
        class="slogan"><?php echo $setting_main['slogan'];?></span><?php endif;?>
      </div>
      <?php if($setting['enable_cabinet'] == 1) require_once (ROOT . '/template/'.$setting['template'].'/layouts/top.php');?>
    </div>
  </div>
</header>