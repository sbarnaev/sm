<?php defined('BILLINGMASTER') or die;

class trainingMainPage {

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

    public function __construct() {
        $this->setting = System::getSetting();
        $this->tr_settings = Training::getSettings();
        $this->en_extension = System::CheckExtensension('training', 1);

        if (!$this->en_extension) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $now = time();
        $cat_list = $training_list = false;

        $filter  = [
            'access' => isset($_GET['acc']) && $_GET['acc'] != 'all' ? $_GET['acc'] : false,
            'author' => isset($_GET['aut']) && is_array($_GET['aut'])  ? $_GET['aut'] : false,
            'category' => isset($_GET['cat']) && is_array($_GET['cat'])  ? $_GET['cat'] : false,
        ];

        $cat_list = $training_list = false;

        if ($this->tr_settings['show_list'] == 'all') {
            $cat_list = TrainingCategory::getCatList(false);
            if (!$cat_list) {
                $training_list = Training::getTrainingList(null, null, $filter);
            }
        } elseif($this->tr_settings['show_list'] == 'without_categories') {
            $training_list = Training::getTrainingList(null, null, $filter);

        } elseif ($this->tr_settings['show_list'] == 'content_separate_category' && $this->tr_settings['categories_to_content']) {
            $cat_list = TrainingCategory::getSubCategoriesByParentsIds($this->tr_settings['categories_to_content']);
            if (!$cat_list) {
                $training_list = Training::getTrainingList($this->tr_settings['categories_to_content'], null, $filter);
            }
        }

        $user_id = intval(User::isAuth());
        $user_groups = $user_id ? User::getGroupByUser($user_id) : false;
        $user_planes = $user_id ? Member::getPlanesByUser($user_id, 1, true) : false;

        $this->setPageSettings($this->tr_settings, 'training_index', 'training/list.php');


        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }

    /**
     * ЗАДАТЬ ОСНОВНЫЕ НАСТРОЙКИ СТРАНИЦЫ
     * @param $data
     * @param $is_page
     * @param bool $use_css
     * @param null $view_path
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
}

$tr = new trainingMainPage();
