<?php defined('BILLINGMASTER') or die;


class trainingAjaxController {

    private $resp;

    public function __construct() {
        $this->resp = [
            'status' => false,
            'error' => '',
        ];
    }


    /**
     * ПОЛУЧИТЬ СПИСОК УРОКОВ ДЛЯ ФИЛЬТРА
     */
    public function actionLessonlist() {
        $userId = intval(User::checkLogged());
        $user = User::getUserById($userId);

        if ($user['is_curator'] && isset($_POST['training_id'])) {
            $lessons = TrainingLesson::getLessons((int)$_POST['training_id']);
            $lesson_list = [];

            if ($lessons) {
                foreach ($lessons as $lesson) {
                    $lesson_list[$lesson['lesson_id']] = $lesson['name'];
                }
            }

            $this->resp['status'] = true;
            $this->resp['list']  = $lesson_list;
            echo json_encode($this->resp, true);
        }
    }


    /**
     * ПОЛУЧИТЬ ФОРМУ ДЛЯ РЕДАКТИРОВАНИЕ ОТВЕТА/КОММЕНТАРИЯ
     */
    public function actionAnswer() {
        if (isset($_POST['answer_id'])) {
            $answer = TrainingLesson::getAnswer((int)$_POST['answer_id']);
            if ($answer) {
                $settings = System::getSetting();
                $task = TrainingLesson::getTask2Lesson($answer['lesson_id']);

                require_once (ROOT . "/extensions/training/views/frontend/lesson/edit_answer.php");
            }
        }
    }


    /**
     * ПОЛУЧИТЬ ФОРМУ ДЛЯ РЕДАКТИРОВАНИЕ ОТВЕТА/КОММЕНТАРИЯ
     */
    public function actionComment() {
        if (isset($_POST['comment_id']) && isset($_POST['lesson_id'])) {
            $comment = TrainingLesson::getComment((int)$_POST['comment_id']);
            if ($comment) {
                $settings = System::getSetting();
                $lesson_id = (int)$_POST['lesson_id'];
                $task = TrainingLesson::getTask2Lesson($lesson_id);
                $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;

                if (strpos($_SERVER['HTTP_REFERER'], '/lk/curator/answers') !== false) {
                    require_once (ROOT . "/extensions/training/views/frontend/users/edit_curator_comment.php");
                } else {
                    require_once (ROOT . "/extensions/training/views/frontend/lesson/edit_comment.php");
                }
            }
        }
    }


    /**
     * ЛАЙКНУТЬ/ДИЗЛАЙКНУТЬ ОТВЕТ/КОММЕНТАРИЙ К ДЗ
     */
    public function actionAnswerlike() {
        $answer_id = (int)$_POST['answer_id'];
        $user_id = intval(User::checkLogged());

        if ($user_id && $answer_id && isset($_POST['is_like'])) {
            if ($_POST['is_like']) {
                if (!TrainingLesson::getLikeAnswer($answer_id, $user_id)) {
                    $this->resp['status'] = TrainingLesson::addLikeAnswer($answer_id, $user_id);
                }
            } else {
                $this->resp['status'] = TrainingLesson::delLikeAnswer($answer_id, $user_id);
            }

            $this->resp['is_like'] = $_POST['is_like'];
            echo json_encode($this->resp, true);
        }
    }


    /**
     * ПОЛУЧИТЬ КНОПКИ "КУПИТЬ"
     */
    public function actionRenderbybuttons() {
        $buttons = false;
        $lesson = $training = $section = null;

        $lesson_id = isset($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : null;
        $section_id = isset($_POST['section_id']) ? (int)$_POST['section_id'] : null;


        if ($lesson_id) {
            $lesson = TrainingLesson::getLesson($lesson_id);
            $training = Training::getTraining($lesson['training_id']);
            $section = TrainingSection::getSection($lesson['section_id']);
        } elseif($section_id) {
            $section = TrainingSection::getSection($section_id);
            $training = Training::getTraining($section['training_id']);
        }

        $user_id = intval(User::isAuth());
        if ($user_id) {
            $user_groups = User::getGroupByUser($user_id);
            $user_planes = Member::getPlanesByUser($user_id, 1, true);
            $access = Training::getAccessData($user_groups, $user_planes, $training, $section, $lesson);
            $buttons = Training::renderByButtons($access['status'], $training, $section, $lesson);
        } else {
            $buttons = Training::renderByButtons(false, $training, $section, $lesson);
        }

        require_once (ROOT . "/extensions/training/views/frontend/layouts/modal_access.php");
    }

     /**
     * ПОЛУЧИТЬ ДАННЫЕ ТЕСТА
     */
    public function actionGetTestAnswer() {
      
        $lesson_id = isset($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : null;
        $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;

        $data = TrainingTest::getDetailedTestResult($lesson_id, $user_id, true);
        
        if ($data){
            $test = TrainingTest::getTestByTestID($data[0]['test_id']);
            $check_test = TrainingTest::getTestResultData($test['test_id'], $user_id, $test['finish']);
            $start_test = TrainingTest::getTestResult($lesson_id, $user_id);
            require_once (ROOT . "/extensions/training/views/frontend/layouts/test_answer_modal_form.php");
        }
    }
}
