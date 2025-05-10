<?php

defined('BILLINGMASTER') or die;


class adminTrainingBlockController extends AdminBase {

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
     * ДОБАВИТЬ БЛОК
     * @param $training_id
     */
    public function actionAddBlock($training_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $training = Training::getTraining($training_id);
        if (!$training) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (isset($_POST['add_block']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $name = htmlentities($_POST['name']);
            $training_id = intval($_POST['training_id']);
            $section_id = isset($_POST['section_id']) ? intval($_POST['section_id']) : 0;
            $sort = intval($_POST['sort']);

            $add_block_id = TrainingBlock::addBlock($name, $training_id, $section_id, $sort);
            if ($add_block_id) {
                System::redirectUrl("/admin/training/editblock/$training_id/$add_block_id", true);
            }
        }

        $trainings_list = Training::getTrainingListToList();
        $sections = TrainingSection::getSections($training_id);
        require_once (ROOT . '/extensions/training/views/admin/block/add.php');
    }


    /**
     * ИЗМЕНИТЬ БЛОК
     * @param $training_id
     * @param $block_id
     */
    public function actionEditBlock($training_id, $block_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $training = Training::getTraining($training_id);
        if (!$training) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        $block = TrainingBlock::getBlock($block_id);
        if (!$block) {
            require_once (ROOT . "/template/{$this->setting['template']}/404.php");
        }

        if (isset($_POST['editblock']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $name = htmlentities($_POST['name']);
            $section_id = intval($_POST['section_id']);
            $sort = intval($_POST['sort']);

            $edit = TrainingBlock::editBlock($block_id, $name, $training_id, $section_id, $sort);
            if ($edit && $block['section_id'] != $section_id) { // обновить раздел у уроков блока
                $edit = TrainingLesson::updSectionLessons($block_id, $section_id);
            }

            System::redirectUrl("/admin/training/editblock/$training_id/$block_id", $edit);
        }

        $trainings_list = Training::getTrainingListToList();
        $sections = TrainingSection::getSections($training_id);
        require_once (ROOT . '/extensions/training/views/admin/block/edit.php');
    }


    /**
     * УДАЛИТЬ БЛОК ТРЕНИНГА
     * @param $training_id
     * @param $block_id
     */


    public function actionDelBlock($training_id, $block_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_courses'])) {
            System::redirectUrl('/admin');
        }

        $training_id = intval($training_id);
        $block_id = intval($block_id);

        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = TrainingBlock::DelBlock($block_id);
            System::redirectUrl("/admin/training/structure/$training_id", $del);
        }
    }
}