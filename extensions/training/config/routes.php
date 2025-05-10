<?php defined('BILLINGMASTER') or die;

return array (
    'admin/training' => 'extensions/training/adminTraining/index',
    'admin/training/del/([0-9]+)' => 'extensions/training/adminTraining/del/$1',
    'admin/training/delall/([0-9]+)' => 'extensions/training/adminTraining/delall/$1',
    'admin/training/edit/([0-9]+)' => 'extensions/training/adminTraining/edit/$1',
    'admin/training/add' => 'extensions/training/adminTraining/add',
    'admin/training/eventsfinish/add' => 'extensions/training/adminTraining/addEventsFinish',
    'admin/training/eventsfinish/edit/([0-9]+)' => 'extensions/training/adminTraining/editEventsFinish/$1',
    'admin/trainingsetting' => 'extensions/training/adminTraining/setting',
    'admin/training/structure/([0-9]+)' => 'extensions/training/adminTraining/structure/$1',
    'admin/training/statistics/([0-9]+)' => 'extensions/training/adminTraining/statistics/$1',
    'admin/training/previewcertificate/([0-9]+)' => 'extensions/training/adminTraining/previewCertificate/$1',

    'admin/training/test/question/add/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingTest/addQuest/$1/$2',
    'admin/training/test/question/edit/([0-9]+)/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingTest/editQuest/$1/$2/$3',
    'admin/training/test/question/del/([0-9]+)/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingTest/delQuest/$1/$2/$3',
    'admin/training/test/answer/add/([0-9]+)/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingTest/addAnswer/$1/$2/$3',
    'admin/training/test/answer/del/([0-9]+)/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingTest/delAnswer/$1/$2/$3',

    'admin/training/addlesson/([0-9]+)' => 'extensions/training/adminTrainingLesson/addLesson/$1',
    'admin/training/editlesson/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingLesson/editLesson/$1/$2',
    'admin/training/copylesson/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingLesson/copyLesson/$1/$2',
    'admin/training/copytransfer' => 'extensions/training/adminTrainingLesson/copytransfer',
    'admin/training/dellesson/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingLesson/delLesson/$1/$2',
    'admin/training/lessons/([0-9]+)' => 'extensions/training/adminTrainingLesson/lessons/$1',
    'admin/training/lessons/statistics/([0-9]+)' => 'extensions/training/adminTrainingLesson/statistics/$1',

    'admin/training/lessons/editoption/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingLesson/editOption/$1/$2',
    'admin/training/lessons/deloption/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingLesson/delOption/$1/$2',

    'admin/training/lessons/element/add' => 'extensions/training/adminTrainingLesson/addElement',
    'admin/training/lessons/element/edit/([0-9]+)' => 'extensions/training/adminTrainingLesson/editElement/$1',
    'admin/training/lessons/element/del/([0-9]+)' => 'extensions/training/adminTrainingLesson/delElement/$1',

    'admin/training/lessons/playlistitem/add' => 'extensions/training/adminTrainingLesson/addPlaylistItem',
    'admin/training/lessons/playlistitem/edit/([0-9]+)' => 'extensions/training/adminTrainingLesson/editPlaylistItem/$1',
    'admin/training/lessons/playlistitem/del/([0-9]+)' => 'extensions/training/adminTrainingLesson/delPlaylistItem/$1',

    'admin/training/delcat/([0-9]+)' => 'extensions/training/adminTrainingCategory/delCat/$1',
    'admin/training/editcat/([0-9]+)' => 'extensions/training/adminTrainingCategory/editCat/$1',
    'admin/training/addcat' => 'extensions/training/adminTrainingCategory/addCat',
    'admin/training/cats' => 'extensions/training/adminTrainingCategory/cats',

    'admin/training/delsection/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingSection/delSection/$1/$2',
    'admin/training/editsection/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingSection/editSection/$1/$2',
    'admin/training/addsection/([0-9]+)' => 'extensions/training/adminTrainingSection/addSection/$1',
    'admin/training/sections/([0-9]+)' => 'extensions/training/adminTrainingSection/sections/$1',

    'admin/training/addblock/([0-9]+)' => 'extensions/training/adminTrainingBlock/addBlock/$1',
    'admin/training/delblock/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingBlock/delBlock/$1/$2',
    'admin/training/editblock/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingBlock/editBlock/$1/$2',

    'admin/training/answers' => 'extensions/training/adminTrainingLesson/answers',
    'admin/training/answers/([0-9]+)/([0-9]+)' => 'extensions/training/adminTrainingLesson/answer/$1/$2',
    'admin/training/answers/del/([0-9]+)' => 'extensions/training/adminTrainingLesson/answer/del/$1',
    'admin/trainingajax/([a-zA-Z0-9_-]+)' => 'extensions/training/adminTrainingAjax/$1',

    'admin/training/import' => 'extensions/training/adminTrainingImport/index',


    'training/category/([a-zA-Z0-9_-]+)/([a-zA-Z0-9_-]+)' => 'extensions/training/trainingCategory/subcategory/$1/$2',// подкатегория
    'training/category/([a-zA-Z0-9_-]+)' => 'extensions/training/trainingCategory/category/$1', // категория тренинга
    'training/view/([a-zA-Z0-9_-]+)' => 'extensions/training/training/training/$1', // страница тренинга
    'training/view/([a-zA-Z0-9_-]+)/section/([a-zA-Z0-9_-]+)' => 'extensions/training/trainingSection/section/$1/$2', // радел тренинга
    'training/view/([a-zA-Z0-9_-]+)/lesson/([a-zA-Z0-9_-]+)' => 'extensions/training/trainingLesson/lesson/$1/$2', // урок тренинга

    'training' => 'extensions/training/training/index', // главная страница тренингов
    'training/options/([0-9]+)' => 'extensions/training/training/options/$1',
    'training/lesson/options/([0-9]+)' => 'extensions/training/trainingLesson/options/$1',
    'training/lesson/test/test' => 'extensions/training/trainingTest/test',
    'training/lesson/test/start' => 'extensions/training/trainingTest/start',
    'training/lesson/test/question/prev' => 'extensions/training/trainingTest/prevQuestion',
    'training/lesson/test/question/next' => 'extensions/training/trainingTest/nextQuestion',
    'training/lesson/test/complete' => 'extensions/training/trainingTest/complete',

    'training/section/options/([0-9]+)' => 'extensions/training/trainingSection/options/$1',
    'training/view/([a-zA-Z0-9_-]+)/lesson/([a-zA-Z0-9_-]+)/user/([0-9]+)' => 'extensions/training/trainingLesson/publicHomework/$1/$2/$3', // публичные ДЗ
    'training/lesson/attach/([0-9]+)' => 'extensions/training/trainingLesson/lessonAttach/$1', // вложение у урока
    'training/answer/edit' => 'extensions/training/training/editAnswer', // редактивароние ответа к ДЗ
    'training/comment/edit' => 'extensions/training/training/editComment', // редактивароние комментария к ДЗ
    'training/curator-comment/edit' => 'extensions/training/training/editCuratorComment',
    'training/showcertificate/([a-zA-Z0-9_-]+)' => 'extensions/training/training/showCertificate/$1',
    'trainingajax/([a-zA-Z0-9_-]+)' => 'extensions/training/trainingAjax/$1', // ajax

    'lk/curator' => 'extensions/training/training/curator', // Кабинет куратора
    // $1 - это homework_id $2 - это user_id $3 - это lesson_id
    'lk/curator/answers/([0-9]+)/([0-9]+)/([0-9]+)' => 'extensions/training/training/answer/$1/$2/$3', // Кабинет куратора
    'lk/curator/del-homework/([0-9]+)' => 'extensions/training/training/delHomework/$1', // удалить сообщение в диалоге
    'lk/curator/delmessage/([0-9]+)' => 'extensions/training/training/delmessage/$1', // удалить сообщение в диалоге
    'lk/mytrainings' => 'extensions/training/training/myTraining',
);