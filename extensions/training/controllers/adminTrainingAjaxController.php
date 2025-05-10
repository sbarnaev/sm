<?php defined('BILLINGMASTER') or die;


class adminTrainingAjaxController extends AdminBase {

    private $resp;

    public function __construct() {
        $this->resp = [
            'status' => false,
            'error' => '',
        ];
    }

    
    /**
     * СОРТИРОВКА ТРЕНИНГОВ
     */
    public function actionUpdsortrainings() {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            exit(json_encode($this->resp));
        }

        if (!empty($_POST['sort_items'])) {
            $this->resp['status'] = true;

            foreach ($_POST['sort_items'] as $sort => $item_id) {
                $result = Training::updSorTraining(intval($item_id), intval($sort)+1);
                if (!$result) {
                    $this->resp['status'] = false;
                    $this->resp['error'] = 'Не удалось сохранить сортировку для тренинга с ID = ' . $item_id;
                    break;
                };
            }
            echo json_encode($this->resp);
        }
    }


    /**
     * УДАЛИТЬ СОБЫТИЕ ДЛЯ ОКОНЧАНИЯ ТРЕНИНГА
     */
    public function actionDeltrainingeventfinish() {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            exit(json_encode($this->resp));
        }


        if (isset($_POST['id']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $id = (int)$_POST['id'];
            $event_finish = Training::getEventFinish($id);
            if (!$event_finish) {
                exit(json_encode($this->resp));
            }

            $del = Training::delEventFinish($id);
            $this->resp['status'] = $del;
            $this->resp['redirect'] = "/admin/training/edit/{$event_finish['training_id']}?success";
        }

        header("Content-type: application/json; charset=utf-8");
        echo json_encode($this->resp);
    }



    public function actionUpdsortstructure() {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            exit(json_encode($this->resp));
        }

        if (isset($_POST['sort_items']) && !empty($_POST['sort_items']) && isset($_GET['item_type'])) {
            $item_type = $_GET['item_type'];
            $sort_items = $_POST['sort_items'];

            if (in_array($item_type, ['section', 'block', 'lesson'])) {
                $this->resp['status'] = true;

                foreach ($sort_items as $key => $item_id) {
                    $controller_name = 'Training' . ucfirst($item_type);
                    $method_name = 'updSort' . ucfirst($item_type);

                    $result = $controller_name::$method_name((int)$item_id, $key + 1);
                    if (!$result) {
                        $this->resp['status'] = false;
                        $this->resp['error'] = "Не удалось сохранить сортировку для элемента «{$item_type}» с ID = $item_id";
                        break;
                    }
                }
            }

            echo json_encode($this->resp);
        }
    }


    /**
     * ПОЛУЧИТЬ СПИСОК УРОКОВ ДЛЯ ФИЛЬТРА
     */
    public function actionLessonlist() {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            exit(json_encode($this->resp));
        }

        if (isset($_POST['training_id']) && isset($_POST['admin_token']) && $_POST['admin_token'] == $_SESSION['admin_token']) {
            $lessons = TrainingLesson::getLessons((int)$_POST['training_id']);
            $lesson_list = [];

            if ($lessons) {
                foreach ($lessons as $lesson) {
                    $lesson_list[$lesson['lesson_id']] = $lesson['name'];
                }
            }

            echo json_encode($lesson_list, true);
        }
    }


    /**
     * ПОЛУЧИТЬ ФОРМУ ДЛЯ ВЛОЖЕНИЯ
     */
    public function actionLessonattachform() {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            exit(json_encode($this->resp));
        }

        if (isset($_GET['attach_id']) && isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $attach = Training::getLessonAttachment(((int)$_GET['attach_id']));
            if ($attach) {
                $lesson = TrainingLesson::getLesson($attach['lesson_id']);
                require_once (ROOT . '/extensions/training/views/admin/lesson/elements/edit_attach.php');
            }
        }
    }


    /**
     * ПОЛУЧИТЬ ФОРМУ ДЛЯ ЭЛЕМЕНТА
     */
    public function actionElementform() {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            exit(json_encode($this->resp));
        }

        if (isset($_GET['id']) && isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $element = TrainingLesson::getElement((int)$_GET['id']);

            if ($element) {
                $lesson = TrainingLesson::getLesson($element['lesson_id']);
                $element_name = TrainingLesson::getElementName($element['type']);
                require_once (ROOT . "/extensions/training/views/admin/lesson/elements/edit_$element_name.php");
            }
        }
    }


    /**
     * ОБНОВИТЬ СОРТИРОВКУ ДЛЯ ЭЛЕМЕНТОВ
     */
    public function actionUpdsortelements() {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            exit(json_encode($this->resp));
        }

        if (!empty($_POST['sort_items'])) {
            $this->resp['status'] = true;

            foreach ($_POST['sort_items'] as $sort => $item_id) {
                $result = TrainingLesson::updSortElement(intval($item_id), intval($sort)+1);
                if (!$result) {
                    $this->resp['status'] = false;
                    $this->resp['error'] = 'Не удалось сохранить сортировку для элемента с ID = ' . $item_id;
                    break;
                };
            }
            echo json_encode($this->resp);
        }
    }


    /**
     * УДАЛИТЬ ЭЛЕМЕНТ УРОКА
     */
    public function actionDellessonelement() {
        $acl = self::checkAdmin();
        if (!isset($acl['del_courses'])) {
            header("Content-type: application/json; charset=utf-8");
            exit(json_encode($this->resp));
        }

        if (isset($_POST['id']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $id = (int)$_POST['id'];
            $el = TrainingLesson::getElement($id);
            if (!$el) {
                header("Content-type: application/json; charset=utf-8");
                exit(json_encode($this->resp));
            }

            $del = TrainingLesson::delElement($id);
            if ($del) {
                if ($el['type'] == TrainingLesson::ELEMENT_TYPE_ATTACH && $el['params']['type'] == 1) {
                    TrainingLesson::delLessonAttach($el['lesson_id'], $el['params']['attach']);
                } elseif ($el['type'] == TrainingLesson::ELEMENT_TYPE_PLAYLIST) {
                    $del = TrainingLesson::delPlaylistItems($id);
                }
            }

            $lesson = TrainingLesson::getLesson($el['lesson_id']);
            $this->resp['status'] = $del;
            $this->resp['redirect'] = "/admin/training/editlesson/{$lesson['training_id']}/{$lesson['lesson_id']}?success";

            header("Content-type: application/json; charset=utf-8");
            echo json_encode($this->resp);
        }
    }


    /**
     * ПОЛУЧИТЬ ФОРМУ ДЛЯ ЭЛЕМЕНТА ПЛЭЙЛИСТА
     */
    public function actionPlaylistitemform() {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            exit(json_encode($this->resp));
        }

        if (isset($_GET['id']) && isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $playlist_item = TrainingLesson::getPlaylistItem((int)$_GET['id']);
            if ($playlist_item) {
                $playlist = TrainingLesson::getElement($playlist_item['playlist_id']);
                $lesson = TrainingLesson::getLesson($playlist['lesson_id']);
                require_once (ROOT . "/extensions/training/views/admin/lesson/elements/edit_playlist_item.php");
            }
        }
    }


    /**
     * ПОЛУЧИТЬ ФОРМУ ДЛЯ ВОПРОСА ТЕСТА
     */
    public function actionTestquestionform() {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            exit(json_encode($this->resp));
        }

        if (isset($_GET['quest_id']) && isset($_GET['lesson_id']) && isset($_GET['training_id']) && isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $quest_id = (int)$_GET['quest_id'];
            $lesson_id = (int)$_GET['lesson_id'];
            $training_id = (int)$_GET['training_id'];

            $question = TrainingTest::getQuestion($quest_id);
            $options = TrainingTest::getOptionsByQuest($quest_id);

            require_once (ROOT . '/extensions/training/views/admin/lesson/tests/edit_question.php');
        }
    }


    /**
     * ПОЛУЧИТЬ ФОРМУ ДЛЯ ОТВЕТА ВОПРОСА ТЕСТА
     */
    public function actionTestanswerform() {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            exit(json_encode($this->resp));
        }

        if (isset($_GET['quest_id']) && isset($_GET['lesson_id']) && isset($_GET['training_id'])) {
            $quest_id = (int)$_GET['quest_id'];
            $lesson_id = (int)$_GET['lesson_id'];
            $training_id = (int)$_GET['training_id'];

            $question = TrainingTest::getQuestion($quest_id);
            $options = TrainingTest::getOptionsByQuest($quest_id);

            require_once (ROOT . '/extensions/training/views/admin/lesson/tests/add_answer.php');
        }
    }


    /**
     * ОБНОВИТЬ СОРТИРОВКУ ДЛЯ ПЛЕЙЛИСТА
     */
    public function actionUpdsortplaylist() {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            exit(json_encode($this->resp));
        }

        if (!empty($_POST['sort_items'])) {
            $this->resp['status'] = true;

            foreach ($_POST['sort_items'] as $sort => $item_id) {
                $result = TrainingLesson::updSortPlaylistItem(intval($item_id), intval($sort)+1);
                if (!$result) {
                    $this->resp['status'] = false;
                    $this->resp['error'] = 'Не удалось сохранить сортировку для элемента с ID = ' . $item_id;
                    break;
                };
            }

            echo json_encode($this->resp);
        }
    }

    /**
     * ОБНОВИТЬ СОРТИРОВКУ ДЛЯ ВОПРОСОВ ТЕСТА
     */
    public function actionUpdsorttestquestions() {
        $acl = self::checkAdmin();
        if (!isset($acl['change_courses'])) {
            exit(json_encode($this->resp));
        }

        if (!empty($_POST['sort_items'])) {
            $this->resp['status'] = true;

            foreach ($_POST['sort_items'] as $sort => $item_id) {
                $result = TrainingTest::updSortTestQuestions(intval($item_id), intval($sort)+1);
                if (!$result) {
                    $this->resp['status'] = false;
                    $this->resp['error'] = 'Не удалось сохранить сортировку для элемента с ID = ' . $item_id;
                    break;
                };
            }

            echo json_encode($this->resp);
        }
    }
}
