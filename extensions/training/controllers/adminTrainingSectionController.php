<?php defined('BILLINGMASTER') or die;


class adminTrainingSectionController  extends AdminBase {

    protected $setting;
    protected $tr_settings;
    protected $admin_name;
    protected $user_type;

    public function __construct()
    {
        $this->setting = System::getSetting();
        $this->tr_settings = Training::getSettings();
        $this->admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : null;
    }


    /**
     * ДОБАВИТЬ РАЗДЕЛ К ТРЕНИНГУ
     * @param $training_id
     */
    public function actionAddSection($training_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $training = Training::getTraining($training_id);
        if (!$training) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (isset($_POST['add_section']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $data = TrainingSection::beforeSaveSectionData2Admin($_POST);

            if (isset($_FILES['cover'])) {
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $data['img'] = $_FILES["cover"]["name"]; // Имя картинки при загрузке

                $folder = ROOT . '/images/training/structure/'; // папка для сохранения
                $path = $folder . $data['img']; // Полный путь с именем файла
                if (is_uploaded_file($tmp_name)) {
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $data['img'] = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                    //$resize = System::imgResize($path, 550, false);
                }
            } else {
                $data['img'] = null;
            }

            $add_section_id = TrainingSection::addSection($training_id, $data);
            if ($add_section_id) {
                System::redirectUrl("/admin/training/editsection/$training_id/$add_section_id", true);
            }
        }

        $curators = Training::getCuratorsTrainingForSection($training_id);
        $lesson_list = TrainingLesson::getLessons2Training($training_id);
        $product_list = Product::getProductListOnlySelect();
        $membership = System::CheckExtensension('membership', 1);

        require_once (ROOT . '/extensions/training/views/admin/section/add.php');
    }


    /**
     * РЕДАКТИРОВАТЬ РАЗДЕЛ ТРЕНИНГА
     * @param $training_id
     * @param $section_id
     */
    public function actionEditSection($training_id, $section_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $training = Training::getTraining($training_id);
        $section = TrainingSection::getSection($section_id, null);

        if (!$training || !$section) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (isset($_POST['edit_section']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $data = TrainingSection::beforeSaveSectionData2Admin($_POST, $section_id);

            if (isset($_FILES['cover']) && $_FILES["cover"]["size"] != 0) {
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $data['img'] = $_FILES["cover"]["name"]; // Имя картинки при загрузке

                $folder = ROOT . '/images/training/sections/'; // папка для сохранения
                $path = $folder . $data['img']; // Полный путь с именем файла

                if (is_uploaded_file($tmp_name)) {
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $data['img'] = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                    $resize = System::imgResize($path, 550, false);
                }
            } else {
                $data['img'] = $_POST['current_img'];
            }

            $edit = TrainingSection::editSection($section_id, $training_id, $data);
            System::redirectUrl("/admin/training/editsection/$training_id/$section_id", $edit);
        }

        $section_curators = Training::getCuratorsTraining($training_id, $section['section_id']);
        $curators = Training::getCuratorsTrainingForSection($training_id);
        $lesson_list = TrainingLesson::getLessons2Training($training_id);
        $by_button = $section['by_button'] ? json_decode($section['by_button'], true) : null;
        $product_list = Product::getProductListOnlySelect();
        $membership = System::CheckExtensension('membership', 1);

        require_once (ROOT . '/extensions/training/views/admin/section/edit.php');
    }


    /**
     * УДАЛИТЬ РАЗДЕЛ ТРЕНИНГА
     * @param $training_id
     * @param $section_id
     */
    public function actionDelSection($training_id, $section_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_courses'])) {
            System::redirectUrl('/admin');
        }

        $training_id = intval($training_id);
        $section_id = intval($section_id);

        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = TrainingSection::delSection($section_id);
            Training::delCuratorsTraining($training_id, $section_id);
            System::redirectUrl("/admin/training/structure/$training_id", $del);
        }
    }
}