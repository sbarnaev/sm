<?php defined('BILLINGMASTER') or die;


class adminTrainingTestController  extends AdminBase
{

    protected $setting;
    protected $tr_settings;
    protected $admin_name;
    protected $user_type;
    protected $resp;

    public function __construct()
    {
        $this->setting = System::getSetting();
        $this->tr_settings = Training::getSettings();
        $this->admin_name = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : null;
        $this->resp = ['status' => false];
    }


    /**
     * ДОБАВИТЬ ВОПРОС ДЛЯ ТЕСТА
     * @param $training_id
     * @param $lesson_id
     */
    public function actionAddQuest($training_id, $lesson_id) {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['add_quest']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $test = TrainingLesson::getTest2Lesson($lesson_id);
            if ($test) {
                $question = htmlentities($_POST['quest']['name']);
                $help = htmlentities($_POST['quest']['help']);
                $true_answer = intval($_POST['quest']['true_answer']);
                $require_all_true = intval($_POST['quest']['require_all_true']);
                $sort = TrainingTest::getFreeSort2Question($lesson_id);
                $cover = $_POST['quest']['cover'];

                $add = TrainingTest::addQuestion($test['test_id'], $question, $help, $true_answer, $require_all_true, $sort, $cover);
                System::redirectUrl("/admin/training/editlesson/$training_id/$lesson_id", $add);
            }
        }
    }


    /**
     * ИЗМЕНИТЬ ВОПРОС ТЕСТА
     * @param $training_id
     * @param $lesson_id
     * @param $quest_id
     */
    public function actionEditQuest($training_id, $lesson_id, $quest_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            exit(json_encode($this->resp));
        }

        if (isset($_POST['save_quest']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) { // изменить вопрос с ответами
            $question = htmlentities($_POST['quest']['name']);
            $help = htmlentities($_POST['quest']['help']);
            $true_answer = intval($_POST['quest']['true_answer']);
            $require_all_true = intval($_POST['quest']['require_all_true']);
            $cover = $_POST['quest']['cover'];

            $edit = TrainingTest::editQuestion($quest_id, $question, $help, $true_answer, $require_all_true, $cover);

            if ($edit && isset($_POST['answers'])) {
                foreach ($_POST['answers'] as $answer) {
                    $option_id = intval($answer['option_id']);
                    $title = htmlentities($answer['title']);
                    $value = System::Translit($title);
                    $valid = isset($answer['valid']) ? intval($answer['valid']) : false;
                    $points = intval($answer['points']);

                    $upd = TrainingTest::updAnswer($option_id, $title, $value, $valid, $points);
                    if (!$upd) {
                        $edit = false;
                    }
                }

                $this->resp['status'] = $edit;
            }

            exit(json_encode($this->resp));
        }
    }


    /**
     * УДАЛИТЬ ВОПРОС У ТЕСТА
     * @param $training_id
     * @param $lesson_id
     * @param $quest_id
     */
    public function actionDelQuest($training_id, $lesson_id, $quest_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_courses'])) {
            System::redirectUrl('/admin');
        }

        $training_id = intval($training_id);
        $lesson_id = intval($lesson_id);

        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = TrainingTest::delQuestion($quest_id);
            System::redirectUrl("/admin/training/editlesson/$training_id/$lesson_id", $del);
        }
    }


    /**
     * ДОБАВИТЬ ОТВЕТ К ТЕСТУ
     * @param $training_id
     * @param $lesson_id
     * @param $quest_id
     */
    public function actionAddAnswer($training_id, $lesson_id, $quest_id) {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['add_answer']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) { // добавить ответ к вопросу
            $title = htmlentities($_POST['title']);
            $value = System::Translit($title);
            $valid = intval($_POST['valid']);
            $points = intval($_POST['points']);
            $sort = TrainingTest::getFreeSort2QuestionOption($quest_id);
            $cover = $_POST['cover'];
            $this->resp['status'] = TrainingTest::AddAnswer($quest_id, $title, $value, $valid, $points, $sort, $cover);

            if (isset($_POST['show_form'])) {
                $this->resp['show_modal_form'] = $_POST['show_form'];
                $this->resp['modal_form_url'] = "/admin/trainingajax/testquestionform?quest_id=$quest_id&lesson_id=$lesson_id&training_id=$training_id&token={$_POST['token']}";
            }
        }

        exit(json_encode($this->resp));
    }


    /**
     * УДАЛИТЬ ОТВЕТ У ТЕСТА
     * @param $training_id
     * @param $lesson_id
     * @param $quest_id
     */
    public function actionDelAnswer($training_id, $lesson_id, $quest_id) {
        $acl = self::checkAdmin();
        if (!isset($acl['del_courses'])) {
            header("Content-type: application/json; charset=utf-8");
            exit(json_encode($this->resp));
        }

        if (isset($_POST['id']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) { //удалить ответ для вопроса
            $option_id = (int)$_POST['id'];
            $option = TrainingTest::getOption($option_id);
            if (!$option) {
                header("Content-type: application/json; charset=utf-8");
                exit(json_encode($this->resp));
            }

            $del = TrainingTest::deleteAnswer($option_id);
            if ($del) {
                $options = TrainingTest::getOptionsByQuest($option['quest_id']);
                require_once (ROOT . '/extensions/training/views/admin/lesson/tests/list_answers.php');
            } else {
                header("Content-type: application/json; charset=utf-8");
                echo json_encode($this->resp);
            }
        }
    }
}