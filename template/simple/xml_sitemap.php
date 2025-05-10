<?php defined('BILLINGMASTER') or die; 
header("Content-Type: application/xml;");
echo '<?xml version="1.0" encoding="UTF-8"?>';?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">


<?php if($menu_items):
foreach($menu_items as $item):
if($item['sitemap'] == 0) continue;?>

<?php 
switch($item['type']){
    
    // ========== Главная 
    case "main": 
    $url = $setting['script_url'];
    $changefreq = $item['changefreq'];
    $priority = $item['priority'];
    echo "
<url>
    <loc>$url</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>
";
break;
        
        
    // ======== Каталог 
    case "catalog": 
    $url = $setting['script_url']. '/catalog';
    $changefreq = $item['changefreq'];
    $priority = $item['priority'];
    echo "
<url>
    <loc>$url</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>  
";
    
    $products = Product::getProductInCatalog();
    if($products){
        foreach($products as $product){
            echo '
            <url>
                <loc>'.$url.'/'.$product['product_alias'].'</loc>
                <changefreq>'.$changefreq.'</changefreq>
                <priority>'.$priority.'</priority>
            </url>  
            ';
        }
    }
    break;
    
    
    // Обратная связь
    case "feedback":
    $url = $setting['script_url']. '/'.$item['link'];
    $changefreq = $item['changefreq'];
    $priority = $item['priority'];
    echo "
<url>
    <loc>$url</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>  
";
    break;
    
    
    
    // Блог 
    case "blog":
    $url = $setting['script_url']. '/'.$item['link'];
    $changefreq = $item['changefreq'];
    $priority = $item['priority'];
    
    $posts = Blog::getPostListFromSitemap();
    if($posts){
        foreach($posts as $post){
            $rubric_alias = Blog::getRubricAlias($post['rubric_id']);
            echo '
            <url>
                <loc>'.$url.'/'.$rubric_alias. '/'. $post['alias'].'</loc>
                <changefreq>'.$changefreq.'</changefreq>
                <priority>'.$priority.'</priority>
            </url>  
            ';
        }
    }
    break;
    
    
	
	// Тренинги 2.0
    case "training":
    $url = $setting['script_url']. '/'.$item['link'];
    $changefreq = $item['changefreq'];
    $priority = $item['priority'];
    echo "
<url>
    <loc>$url</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>  
";


    $training = Training::getTrainingList();
    if($training){
        foreach($training as $training){
            echo '
            <url>
                <loc>'.$url.'/view/'.$training['alias'].'</loc>
                <changefreq>'.$changefreq.'</changefreq>
                <priority>'.$priority.'</priority>
            </url>  
            ';
        }
    }
    break; 
	
	
    
    // Курсы
    case "courses":
    $url = $setting['script_url']. '/'.$item['link'];
    $changefreq = $item['changefreq'];
    $priority = $item['priority'];
    echo "
<url>
    <loc>$url</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>  
";
    
    
    $courses = Course::getCourseListFromSitemap();
    if($courses){
        foreach($courses as $course){
            echo '
            <url>
                <loc>'.$url.'/'.$course['alias'].'</loc>
                <changefreq>'.$changefreq.'</changefreq>
                <priority>'.$priority.'</priority>
            </url>  
            ';
        }
    }
    
    $lessons = Course::getLessonListFromSitemap();
    if($lessons){
        foreach($lessons as $lesson){
            $course_alias = Course::getCourseByID($lesson['course_id']);
            echo '
            <url>
                <loc>'.$url.'/'.$course_alias['alias']. '/'. $lesson['alias'].'</loc>
                <changefreq>'.$changefreq.'</changefreq>
                <priority>'.$priority.'</priority>
            </url>  
            ';
        }
    }
    
    break; 
    
    
    
    // Партнёрка
    case "aff":
    $url = $setting['script_url']. '/'.$item['link'];
    $changefreq = $item['changefreq'];
    $priority = $item['priority'];
    echo "
<url>
    <loc>$url</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>  
";
    break;
    
    
    // Форум
    case "forum":
    $url = $setting['script_url']. '/'.$item['link'];
    $changefreq = $item['changefreq'];
    $priority = $item['priority'];
    echo "
<url>
    <loc>$url</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>
";
    break;

    // Форум2
    case "forum2":
        $url = "{$setting['script_url']}/{$item['link']}";
        $changefreq = $item['changefreq'];
        $priority = $item['priority'];
        echo "
<url>
    <loc>$url</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>"
;

    $categories = Forum2::getCatList(1);
    if ($categories) {
        foreach ($categories as $category) {
            echo "
<url>
    <url>
        <loc>$url/category/{$category['alias']}</loc>
        <changefreq>$changefreq</changefreq>
        <priority>$priority</priority>
    </url>
</url>"
;
        }
    }

    $branches = Forum2::getBranchList();
    if ($branches) {
        foreach ($branches as $branch) {
            echo "
<url>
    <loc>$url/branch/{$branch['alias']}</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>  
";
        }
    };

    $topics = Forum2::getTopics(null, 1);
    if ($topics) {
        foreach ($topics as $topic) {
            $branch = Forum2::getBranchData($topic['branch_id']);
            echo "
<url>
    <loc>$url/branch/{$branch['alias']}/topic/{$topic['topic_id']}</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>  
";
        }
    };
    break;



    // Галерея
    case "gallery":
    $url = $setting['script_url']. '/'.$item['link'];
    $changefreq = $item['changefreq'];
    $priority = $item['priority'];
    echo "
<url>
    <loc>$url</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>
";
    break;
    
    
    
    // Отзывы
    case "reviews":
    $url = $setting['script_url']. '/'.$item['link'];
    $changefreq = $item['changefreq'];
    $priority = $item['priority'];
    echo "
<url>
    <loc>$url</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>
";
    break;
    
    
    
    
    case "static":
    $url = $setting['script_url'].'/'.$item['link'];
    $changefreq = $item['changefreq'];
    $priority = $item['priority'];
    echo "
<url>
    <loc>$url</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>  
";
    break;
    
    
    
    case "custom":
    $url = $item['link'];
    $changefreq = $item['changefreq'];
    $priority = $item['priority'];
    echo "
<url>
    <loc>$url</loc>
    <changefreq>$changefreq</changefreq>
    <priority>$priority</priority>
</url>  
";
    break;
    }
?>

<?php endforeach;
endif; ?>

</urlset>