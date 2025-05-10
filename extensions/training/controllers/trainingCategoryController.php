<?php defined('BILLINGMASTER') or die;

class trainingCategoryController {

    private $setting;
    private $tr_settings;
    private $en_extension;

    private $title;
    private $meta_desc;
    private $meta_keys;
    private $h1;
    private $h2;
    private $is_page;
    private $view_path;
    private $use_css;

    public function __construct()
    {
        $this->setting = System::getSetting();
        $this->tr_settings = Training::getSettings();
        $this->en_extension = System::CheckExtensension('training', 1);

        if (!$this->en_extension) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }
    }


    /**
     * КАТЕГОРИЯ ТРЕНИНГОВ
     * @param $cat_alias
     */
    public function actionCategory($cat_alias)
    {
        if (!$this->en_extension) {
            require_once (ROOT . '/template/'.$this->setting['template'].'/404.php');
        }

        $category = TrainingCategory::getCategoryByAlias(htmlentities($cat_alias));
        if (!$category) {
            require_once (ROOT . '/template/'.$this->setting['template'].'/404.php');
        }

        $filter  = [
            'access' => isset($_GET['acc']) && $_GET['acc'] != 'all' ? $_GET['acc'] : false,
            'author' => isset($_GET['aut']) && is_array($_GET['aut'])  ? $_GET['aut'] : false,
            'category' => isset($_GET['cat']) && is_array($_GET['cat'])  ? $_GET['cat'] : false,
        ];

        $subcategory_list = TrainingCategory::getSubCategories($category['cat_id']);
        if (!$subcategory_list) {
            $training_list = Training::getTrainingList($category['cat_id'], null, $filter);
        }

        $user_id = intval(User::isAuth());
        $user_groups = $user_id ? User::getGroupByUser($user_id) : false;
        $user_planes = $user_id ? Member::getPlanesByUser($user_id, 1, true) : false;

        $this->setPageSettings($category, 'training_category', 'category/index.php');
        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }


    /**
     * ПОДКАТЕГОРИИ ТРЕНИНГОВ
     * @param $cat_alias
     * @param $sub_cat_alias
     */
    public function actionSubcategory($cat_alias, $sub_cat_alias)
    {
        $category = TrainingCategory::getCategoryByAlias(htmlentities($cat_alias));
        $sub_category = TrainingCategory::getCategoryByAlias(htmlentities($sub_cat_alias));

        if (!$this->en_extension || !$category || !$sub_category) {
            require_once (ROOT . '/template/'.$this->setting['template'].'/404.php');
        }

        $filter  = [
            'access' => isset($_GET['acc']) && $_GET['acc'] != 'all' ? $_GET['acc'] : false,
            'author' => isset($_GET['aut']) && is_array($_GET['aut'])  ? $_GET['aut'] : false,
            'category' => isset($_GET['cat']) && is_array($_GET['cat'])  ? $_GET['cat'] : false,
        ];
        $training_list = Training::getTrainingList($sub_category['cat_id'], 1, $filter);

        $user_id = intval(User::isAuth());
        $user_groups = $user_id ? User::getGroupByUser($user_id) : false;
        $user_planes = $user_id ? Member::getPlanesByUser($user_id, 1, true) : false;

        $this->setPageSettings($category, 'training_subcategory', 'category/subcategory/index.php');
        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }


    /**
     * ЗАДАТЬ ОСНОВНЫЕ НАСТРОЙКИ СТРАНИЦЫ
     * @param $data
     * @param $is_page
     * @param null $view_path
     * @param bool $use_css
     */
    public function setPageSettings($data, $is_page, $view_path = null, $use_css = true) {
        $this->is_page = $is_page;
        $this->title = $data['title'];
        $this->meta_desc = isset($data['meta_desc']) ? $data['meta_desc'] : $data['desc'];
        $this->meta_keys = isset($data['meta_keys']) ? $data['meta_keys'] : $data['keys'];
        $this->h1 = isset($data['h1']) ? $data['h1'] : $data['name'];
        if ($view_path) {
            $this->view_path = $view_path;
        }
        $this->use_css = $use_css;
    }


    /**
     * ЗАДАТЬ ПУТЬ ДО VIEW ФАЙЛА
     * @param $view_path
     */
    public function setViewPath($view_path) {
        $this->view_path = $view_path;
    }
}