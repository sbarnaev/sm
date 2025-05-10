<?php defined('BILLINGMASTER') or die;

class trainingTestController
{
    private $setting;
    private $tr_settings;
    private $en_extension;
    private $resp;

    private $user_id;
    private $lesson_id;
    private $lesson;
    private $training;
    private $section;
    private $homework;
    private $task;
    private $test;
    private $count_questions;
    private $show_questions_count;
    private $time;
    private $test_finish;
    private $test_expired;

    /**
     * trainingTestController constructor.
     */
    public function __construct()
    {
        $this->setting = System::getSetting();
        $this->tr_settings = Training::getSettings();
        $this->en_extension = System::CheckExtensension('training', 1);
        $this->resp = ['status' => false];

        if (!$this->en_extension) {
            header("Content-type: application/json; charset=utf-8");
            exit(json_encode($this->resp));
        }

        $this->user_id = intval(User::isAuth());
        $this->lesson_id = isset($_POST['lesson_id']) ? (int)$_POST['lesson_id'] : null;
        $this->lesson = $this->lesson_id ? TrainingLesson::getLesson($this->lesson_id) : null;
        $this->training = $this->lesson ? Training::getTraining($this->lesson['training_id']) : null;
        if (!$this->user_id || !$this->lesson || !$this->training) {
            header("Content-type: application/json; charset=utf-8");
            exit(json_encode($this->resp));
        }

        $this->homework = TrainingLesson::getHomeWork($this->user_id, $this->lesson['lesson_id']);
        if (!$this->homework || !$this->homework['test_start']) {
            $this->section = $this->lesson['section_id'] ? TrainingSection::getSection($this->lesson['section_id']) : null;
            $user_groups = $this->user_id ? User::getGroupByUser($this->user_id) : false;
            $user_planes = $this->user_id ? Member::getPlanesByUser($this->user_id, 1, true) : false;
            $access = Training::getAccessData($user_groups, $user_planes, $this->training, $this->section, $this->lesson);
            if (!Training::checkUserAccess($access)) {
                header("Content-type: application/json; charset=utf-8");
                exit(json_encode($this->resp));
            }
        }

        $this->task = TrainingLesson::getTask2Lesson($this->lesson['lesson_id']);
        $this->test = $this->task ? TrainingTest::getTestByTaskID($this->task['task_id']) : null;
        $this->count_questions = $this->test ? TrainingTest::getCountQuestions2Test($this->test['test_id']) : 0;
        $this->show_questions_count = $this->test['show_questions_count'] < $this->count_questions ? $this->test['show_questions_count'] : $this->count_questions;
    
        $this->time = time();
        if ($this->homework  && $this->homework['test_start']) {
            $this->test_finish = $this->homework['test_start'] + ($this->test['test_time'] > 0 ? $this->test['test_time'] * 60 : 259200); // если время на тест не указано, даем 3 суток
            $this->test_expired = $this->time > $this->test_finish ? true : false;
        } else {
            $this->test_expired = false;
        }
        
    }


    /**
     * ЗАПУСК ТЕСТА
     */
    public function actionStart() {
        $write = TrainingTest::writeStartTest($this->task['task_id'], $this->lesson['lesson_id'], $this->user_id, 0, 0,
            0, 0, 0, 0, null, $this->time
        );

        if ($write) {
            $this->reSetParameters();
            $questions_ids = TrainingTest::getQuestionsIdsByTestId($this->test['test_id']);
            $question_id = $questions_ids[rand(0, $this->count_questions - 1)];
            $_SESSION['test_questions'] = [
                'questions' => [$question_id],
                'curr_index' => 0,
            ];
            $this->actionTest();
        }
    }


    /**
     * ТЕСТ
     */
    public function actionTest() {
        if ($this->test && $this->task['task_type'] > 1 && $this->count_questions > 0) {
            if (!$this->homework['test_start'] || (!isset($_SESSION['test_questions']) && !$this->homework['test'])) {
                require_once (ROOT . "/extensions/training/views/frontend/lesson/tests/start.php");
            } else {
                if (!$this->test_expired && !$this->homework['test']) {
                    $curr_index = $_SESSION['test_questions']['curr_index'];
                    $question_id = $_SESSION['test_questions']['questions'][$curr_index];
                    $question = TrainingTest::getQuestion($question_id);
                    $answers = TrainingLesson::getAnswers2Lesson($this->lesson['lesson_id'], $this->user_id, $this->homework['homework_id']);
                    $number_question = $curr_index + 1;

                    require_once (ROOT . "/extensions/training/views/frontend/lesson/tests/question.php");
                } else {
                    $test_result = TrainingTest::getTestResultData($this->test['test_id'], $this->user_id, $this->test['finish']);
                    require_once (ROOT . "/extensions/training/views/frontend/lesson/tests/result.php");
                }
            }
        }
    }


    /**
     * ПЕРЕЙТИ К ПРЕДЫДУЩЕМУ ОТВЕТУ
     */
    public function actionPrevQuestion() {
        if (isset($_SESSION['test_questions']) && $_SESSION['test_questions']['curr_index'] > 0) {
            $this->saveAnswer();
            $_SESSION['test_questions']['curr_index']--;
            $this->actionTest();
        }
    }


    /**
     * ПЕРЕЙТИ К СЛЕДУЮЩЕМУ ОТВЕТУ
     */
    public function actionNextQuestion() {
        if (isset($_SESSION['test_questions']) && $_SESSION['test_questions']['curr_index'] < $this->show_questions_count - 1) {
            $this->saveAnswer();

            if (count($_SESSION['test_questions']['questions']) == $_SESSION['test_questions']['curr_index'] + 1) {
                $questions_ids = TrainingTest::getQuestionsIdsByTestId($this->test['test_id']);
                $available_ids = array_values(array_diff($questions_ids, $_SESSION['test_questions']['questions']));

                if ($available_ids) { // если еще есть вопросы
                    $question_id = $available_ids[rand(0, count($available_ids) - 1)];
                    $_SESSION['test_questions']['questions'][] = $question_id;
                    $_SESSION['test_questions']['curr_index']++;
                    $this->actionTest();
                }
            } else {
                $_SESSION['test_questions']['curr_index']++;
                $this->actionTest();
            }
        }
    }


    /**
     * СОХРАНИТЬ ОТВЕТ ПОЛЬЗОВАТЕЛЯ
     */
    public function saveAnswer() {
        if (isset($_POST['question_id']) && isset($_POST['answers']) && !empty($_POST['answers'])) {
            $question_id = (int)$_POST['question_id'];
            $_SESSION['test_questions']['answers'][$question_id] = $_POST['answers'];
        }
    }


    /**
     * ЗАВЕРШЕНИЕ ТЕСТА
     */
    public function actionComplete() {
        $last_question_id = (int)$_POST['question_id'];
        $questions_id = isset($_SESSION['test_questions']['questions']) ? $_SESSION['test_questions']['questions'] : [];
        $questions[] = $last_question_id;

        $last_answers = isset($_POST['answers']) ? $_POST['answers'] : [];
        $answers = isset($_SESSION['test_questions']['answers']) ? $_SESSION['test_questions']['answers']: [];
        $answers[$last_question_id] = $last_answers;

        foreach ($questions_id as $question_id) {
            $is_valid = true;
            $right_answers = 0;
            $points = 0;
            $options = TrainingTest::getOptionsByQuest($question_id);
            $answer_titles = '';

            if ($options && isset($answers[$question_id])) {
                foreach ($options as $option) {
                    if ($option['valid']) {
                        $right_answers++;
                    }

                    if (in_array($option['option_id'], $answers[$question_id])) {
                        $points += $option['points'];
                        if (!$option['valid']) {
                            $is_valid = false;
                        }
                        $answer_titles .= ($answer_titles ? ',' : '') . $option['title'];
                    }
                }

                $is_valid = $is_valid && $right_answers == count($answers[$question_id]) ? true : false;

                // Пишем результат по каждому вопросу в test_results + $is_valid правильный ли ответ + начисленые баллы
                TrainingTest::addResultTest($this->test['test_id'], $this->lesson_id, $this->user_id, $question_id,
                    $answer_titles, $points, $is_valid, $this->time
                );
            }
        }

        $test_result = TrainingTest::getTestResultData($this->test['test_id'], $this->user_id, $this->test['finish']);
        $this->homework['test'] = $test_result['success'] ? 1 : 2;
        $is_send_homework = TrainingLesson::getAnswer($this->homework['homework_id']);
        $status_hw = null;
        if ($is_send_homework && $this->task['access_type'] == 1 && $this->homework['test'] == 1) {
            $status_hw = 1;
        }
        if ($this->task['task_type'] == 3 && $this->task['access_type'] == 1 && $this->homework['test'] == 2){
            $status_hw = 4;
        }
        TrainingLesson::updHomeworkData($this->homework['homework_id'], $this->user_id, $status_hw, $this->homework['test']);

        $map_status = TrainingTest::getStatus2UserMap($this->task['task_type'], $this->task['access_type'],
            $this->homework['test'], $this->homework['homework_id'], $is_send_homework
        );
        if ($map_status) {
            TrainingLesson::updLessonCompleteStatus($this->lesson['lesson_id'], $this->user_id, $map_status);
        }

        require_once (ROOT . "/extensions/training/views/frontend/lesson/tests/result.php");
    }
    
    public function reSetParameters() {
        $this->homework = TrainingLesson::getHomeWork($this->user_id, $this->lesson['lesson_id']);
        $this->test_finish = $this->homework['test_start'] + ($this->test['test_time'] > 0 ? $this->test['test_time'] * 60 : 259200); // если время на тест не указано, даем 3 суток
        $this->test_expired = $this->time > $this->test_finish ? true : false;
    }
}
