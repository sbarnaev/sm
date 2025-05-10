<?php defined('BILLINGMASTER') or die;

class trainingSectionController {

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
     * ПРОСМОТР РАЗДЕЛА ТРЕНИНГА
     * @param $tr_alias
     * @param $section_alias
     * @throws Exception
     */
    public function actionSection($tr_alias, $section_alias)
    {
        $training = Training::getTrainingByAlias(htmlentities($tr_alias));
        if (!$training) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $section = TrainingSection::getSectionByAlias($training['training_id'], htmlentities($section_alias));
        if (!$section) {
            require_once(ROOT . "/template/{$this->setting['template']}/404.php");
        }
		
		require_once (ROOT ."/lib/mobile_detect/Mobile_Detect.php");
        $detect = new Mobile_Detect;

        $user_id = intval(User::isAuth());
        $user_groups = $user_id ? User::getGroupByUser($user_id) : false;
        $user_planes = $user_id ? Member::getPlanesByUser($user_id, 1, true) : false;
        $user_is_curator = Training::isCuratorInTrainingInSection($user_id, $training['training_id'], $section['section_id']);

        $this->setPageSettings($section, 'section', 'section/index.php');

        $access = Training::getAccessData($user_groups, $user_planes, $training, $section);
        if (!Training::checkUserAccess($access)) {
            $this->setViewPath('layouts/no_access.php');
            require_once (ROOT . "/extensions/training/views/frontend/view.php");
        }

        if ($user_id && !isset($_SESSION['training_save_uv']['section'][$section['section_id']])) {
            TrainingUserVisits::saveVisit($user_id, $training['training_id'], $section['section_id']);
            $_SESSION['training_save_uv']['section'][$section['section_id']] = true;
        }


        $sub_category = $category = false;
        if ($training['cat_id'] != 0) {
            $category = TrainingCategory::getCategory($training['cat_id']);
            if ($category && $category['parent_cat'] != 0) {
                $sub_category = $category;
                $category = TrainingCategory::getCategory($category['parent_cat']);
            }
        }

        $block_list = TrainingBlock::getBlocks($training['training_id'], $section['section_id']); // получаем обычные блоки для секции
        $lesson_list = TrainingLesson::getLessons($training['training_id'], $section['section_id']); // получаем уроки без блоков

        require_once (ROOT . "/extensions/training/views/frontend/view.php");
    }


    /**
     * СТРАНИЦА ВЫБОРА ТАРИФА в разделах (Продуктов)
     * @param $section_id
     */
    public function actionOptions($section_id)
    {
        $section_id = intval($section_id);
        $section_id = $section = TrainingSection::getSection($section_id);

        if (!$section_id) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $user_id = intval(User::isAuth());
        $h1 = $section_id['name'];
        $training = Training::getTraining($section_id['training_id']);

        $lesson = null;
        $sub_category = $category = false;

        if ($training['cat_id'] != 0) {
            $category = TrainingCategory::getCategory($training['cat_id']);
            if ($category && $category['parent_cat'] != 0) {
                $sub_category = $category;
                $category = TrainingCategory::getCategory($sub_category['parent_cat']);
            }
        }

        $big_button = json_decode($section_id['by_button'], true);
        $list_product = $big_button['rate'];

        $this->setViewPath('layouts/rates.php');
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