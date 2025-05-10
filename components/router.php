<?php defined('BILLINGMASTER') or die;

defined('BILLINGMASTER') or die;

class Router
{
    private $routes;
    
    public function __construct()
    {
        $this->routes = array_merge(
            include(ROOT . '/config/routes.php'),
            file_exists(ROOT . '/config/custom_routes.php') ? include(ROOT . '/config/custom_routes.php') : []
        );

        $extensions_list = scandir(ROOT . '/extensions');
        foreach ($extensions_list as $extension_dir) {
            if ($extension_dir != '.' && $extension_dir != '..') {
                $route_path = ROOT . "/extensions/$extension_dir/config/routes.php";
                if (file_exists($route_path)) {
                    $this->routes = array_merge($this->routes, include($route_path));
                }
            }
        }
    }
    
    
    // Возвращает URL запрос строкой без /
    
    private function getURI()
    {
        $paramPath = ROOT . '/config/config.php';
        $params = include($paramPath);
        
        if(!empty($_SERVER['REQUEST_URI'])) {
			$uri = ltrim($_SERVER['REQUEST_URI'], $folder); // удаляем папку из URL, типа: site.ru/bm/
			$uri = explode('?', $uri);
			
            $uri = array_splice($uri, 0,1);
            return trim($uri[0], '/');
            
        }
        /*if(!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }*/
    }
    
    
    public function run()
    {
        
        $uri = $this->getURI(); // Запрос в URL 
        $err404 = true;

        
        // Перебираем массив роутов 
        
        foreach ($this->routes as $uriPattern => $path) {  
            
            if (preg_match("~^$uriPattern$~", $uri)){ // проверяет соответствие регулярки к запросу uri
                
                $err404 = false;
                $internalRoute = preg_replace("~$uriPattern~", $path, $uri); // поиск $uriPattern в $uri и замена на $path 
                
                $segments = explode('/', $internalRoute);
                $part_path = '';
                if ($segments[0] == 'extensions') {
                    $part_path = array_shift($segments) . '/' . array_shift($segments);
                }
                
                $controllerName = array_shift($segments).'Controller';
                $actionName = 'action'. ucfirst(array_shift($segments));
                $parameters = $segments;
                
                $controllerFile = ROOT . "/$part_path/controllers/$controllerName.php";
               
                if(file_exists($controllerFile)) {
                    require_once($controllerFile);
                    
                    $controllerObject = new $controllerName;
                    $result = call_user_func_array(array($controllerObject, $actionName), $parameters);
                    if ($result != null) {
                        break;
                    }
                } else {
                    require_once (ROOT . '/template/404.php');
                }
                
                
        }
            
        }
        
        if ($err404) {
            //echo 'Ошибочка 404';
            require_once (ROOT . '/template/404.php');
        }
        
    }
    
    
}