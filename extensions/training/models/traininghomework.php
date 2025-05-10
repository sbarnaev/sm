<?php defined('BILLINGMASTER') or die;


class TrainingHomeWork {

    use ResultMessage;

    private $training;
    private $lesson;
    private $task;
    private $homework;
    private $homework_is_public;
    private $user_id;

    public function __construct($training, $lesson, $task, $homework_is_public, $user_id) {
        $this->training = $training;
        $this->lesson = $lesson;
        $this->user_id = $user_id;
        $this->task = $task;
        $this->homework = $task ? TrainingLesson::getHomeWork($user_id, $lesson['lesson_id']) : false;
        $this->homework_is_public = $homework_is_public;
    }

    public function answerSave($lesson_complete_status, $task_check_type, $levelAccessTypeHomeWork, $answer_list, $answer_type) {
        $attach = null;
        if (isset($_FILES['lesson_attach']) && $_FILES['lesson_attach']['size'][0] > 0) {
            $attach = TrainingLesson::uploadAttach2Answer($_FILES, $this->lesson['lesson_id'], Training::USER_TYPE_USER);
        }
        $answer = isset($_POST['answer']) && trim(strip_tags(str_replace('&nbsp;', '', $_POST['answer']), '<img>')) ? base64_encode(trim($_POST['answer'])) : null;

        if ($answer_type == TrainingLesson::MSG_TYPE_ANSWER) {
            $work_link = isset($_POST['work_link']) && $_POST['work_link'] ? trim($_POST['work_link']) : null;
            if (isset($_POST['answer']) && !$answer && (!$work_link && !$attach)) {
                return false;
            }

            $answer = $answer ?: base64_encode('Работа сделана, пожалуйста, проверьте. Спасибо!');
            $public = $this->training['on_public_homework'] && isset($_POST['homework_is_public']) && !$this->homework_is_public ? 2 : 0;
            //TrainingLesson::addPublicHomework($this->lesson['lesson_id'], $this->user_id);   : 0; // TODO здесь что-то с публичными ДЗ скорее всего надо проверить, может и так работает.

            if ($task_check_type == 0) { // Если самостоятельная проверка то в базу не пишем, а просто обновляем статус в юзер_мап ниже
                $answer = base64_encode('Самостоятельная проверка');
                $status = in_array($this->task["access_type"], [1,3]) && $this->homework['test'] == 2 ? 4 : 1;
            } else { // тут если у урока стоит тип Автопроверка(1), то юзер_мап пишется статус 4 иначе во всех остальных случаях 1
                $status = $task_check_type == 1 && in_array($this->task["access_type"], [1,3]) && $this->homework['test'] == 2 ? 4
                    : $task_check_type == TrainingLesson::HOME_WORK_ACCEPTED ? TrainingLesson::HOME_WORK_ACCEPTED : TrainingLesson::HOME_WORK_SEND;
            }

            $write = TrainingLesson::writeAnswer($this->task['task_id'], $this->lesson['lesson_id'], $this->user_id,
                0, $status, $public, $answer, $attach, $work_link
            );

            if ($write) { // TODO вот тут нужно будет добавить проверки по типам доступа к ДЗ у юзера
                if ($task_check_type == 0) {
                    $lesson_complete_status = in_array($this->task["access_type"], [1,3]) && $this->homework['test'] == 2 ? 2 : 3;
                } else { // тут если у Урока стоит тип Автопроверка(1), то юзер_мап пишется статус 4 иначе во всех остальных случаях 1
                    $lesson_complete_status = $task_check_type == 1 && in_array($this->task["access_type"], [1,3]) && $this->homework['test'] == 2 ? 2
                        : $task_check_type == 1 ? TrainingLesson::HOMEWORK_AUTOCHECK : TrainingLesson::HOMEWORK_SUBMITTED;
                }

                TrainingLesson::updLessonCompleteStatus($this->lesson['lesson_id'], $this->user_id, $lesson_complete_status);

                if ($this->training['send_email_to_curator'] == 1) {
                    Email::SendAnswerFromUserToCurator($this->user_id, $answer_list[0]['homework_id'], $answer, $this->lesson, $this->training);
                }
            }
        } elseif ($answer_type == TrainingLesson::MSG_TYPE_COMMENT && $answer) {
            TrainingLesson::writeComment($answer_list[0]['homework_id'], $this->user_id, 0, $answer, 0, $attach);

            if ($this->training['send_email_to_curator'] == 1) {
                Email::SendAnswerFromUserToCurator($this->user_id, $answer_list[0]['homework_id'], $answer, $this->lesson, $this->training);
            }
        }
    }
}