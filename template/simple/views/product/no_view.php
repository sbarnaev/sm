<?php defined('BILLINGMASTER') or die; 
require_once (ROOT . '/template/'.$setting['template'].'/layouts/head.php');
?>
<body id="page">
<?php echo System::renderContent($text_lp);?>
<?php require_once (ROOT . '/template/'.$setting['template'].'/layouts/tech-footer.php');
echo $product["$text_bottom"];?>
</body>
</html>