<?php defined('BILLINGMASTER') or die;

class adminSiteController extends AdminBase {


    // СПИСОК ФОРМ
    public function actionFeedbackforms()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_feedback'])) {
            header("Location: /admin");
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();

        $forms = System::getFeedBackFormList();

        require_once (ROOT . '/template/admin/views/feedback/forms.php');
    }



    // СОЗДАТЬ ФОРМУ
    public function actionAddfeedbackform()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_feedback'])) {
            System::redirectUrl('/admin');
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();

        if (isset($_POST['addform']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_feedback'])) {
                System::redirectUrl('/admin');
            }

            $name = htmlentities($_POST['name']);
            $form_desc = htmlentities($_POST['form_desc']);
            $status = intval($_POST['status']);
            $default_form = intval($_POST['default_form']);
            $params = base64_encode(serialize($_POST['form']));

            $add = System::AddForm($name, $form_desc, $status, $default_form, $params);
            System::redirectUrl('/admin/feedback/forms', $add);
        }

        require_once (ROOT . '/template/admin/views/feedback/add_form.php');
    }


    // ИЗМЕНИТЬ ФОРМУ
    public function actionEditfeedbackform($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_feedback'])) {
            System::redirectUrl('/admin');
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $id = intval($id);

        if (isset($_POST['editform']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_feedback'])) {
                System::redirectUrl('/admin');
            }

            $name = htmlentities($_POST['name']);
            $form_desc = htmlentities($_POST['form_desc']);
            $status = intval($_POST['status']);
            $default_form = intval($_POST['default_form']);
            $params = base64_encode(serialize($_POST['form']));

            $edit = System::editForm($id, $name, $form_desc, $status, $default_form, $params);
            if ($edit) {
                header("Location: /admin/feedback/editform/$id?success");
            }
        }

        $form = System::getFormDataByID($id);
        $params = unserialize(base64_decode($form['params']));

        require_once (ROOT . '/template/admin/views/feedback/edit_form.php');
    }


    // УДАЛИТЬ ФОРМУ
    public function actionDelfeedbackform($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_feedback'])) {
            header("Location: /admin/feedback");
            exit;
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();
        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = System::deleteFeedbackForm($id);

            if ($del) header("Location: ".$setting['script_url']."/admin/feedback/forms?success");
            else header("Location: ".$setting['script_url']."/admin/feedback/forms?fail");
        }

    }


    // СПИСОК СООБЩЕНИЙ
    public function actionFeedback()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_feedback'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();

        $messages = System::getFeedBackList();

        require_once (ROOT . '/template/admin/views/feedback/index.php');
    }



    // ПРОСМОТР СООБЩЕНИЯ
    public function actionViewmessage($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_feedback'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();

        $id = intval($id);
        $message = System::getFeedbackMessage($id);

        if (isset($_POST['save']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_feedback'])) {
                System::redirectUrl('/admin');
            }
            $status = intval($_POST['status']);
            $comment = $_POST['comment'];

            $save = System::saveMessage($id, $status, $comment);
            if ($save) header("Location: /admin/feedback");
        }

        require_once (ROOT . '/template/admin/views/feedback/view.php');
    }



    // УДАЛИТЬ СООБЩЕНИЕ
    public function actionDelfeedback($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_feedback'])) {
            header("Location: /admin/feedback");
            exit;
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();
        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = System::deleteFeedback($id);

            if ($del) header("Location: ".$setting['script_url']."/admin/feedback");
        }
    }



    // СТАТИЧНЫЕ СТРАНИЦЫ
    public function actionPages()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_pages'])) header("Location: /admin");
        $name = $_SESSION['admin_name'];

        $pages = System::getStaticPages();

        require_once (ROOT . '/template/admin/views/pages/index.php');
    }



    // СОЗДАТЬ СТАТИЧНУЮ СТРАНИЦУ
    public function actionAddpage()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_pages'])) {
            System::redirectUrl('/admin');
        }
        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();

        if (isset($_POST['addpage']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {

            if (!isset($acl['change_pages'])) {
                header("Location: /admin");
                exit;
            }
            $name = htmlentities($_POST['name']);
            $status = intval($_POST['status']);
            if (empty($_POST['alias'])) $alias = System::Translit($_POST['name']);
            else $alias = $_POST['alias'];

            if (empty($_POST['title'])) $title = $_POST['name'];
            else $title = $_POST['title'];

            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);

            $curl = htmlentities($_POST['curl']);

            $content = $_POST['content'];
            $in_head = $_POST['in_head'];
            $in_body = $_POST['in_body'];
            $tmpl = intval($_POST['tmpl']);

            if (isset($_POST['custom_code'])) $custom_code = $_POST['custom_code'];
            else $custom_code = null;

            $add = System::addStaticPage($name, $status, $alias, $title, $meta_desc, $meta_keys, $content, $tmpl, $in_head, $in_body,
                $custom_code, $curl);
            if ($add) header("Location: ".$setting['script_url']."/admin/statpages?success");
        }

        require_once (ROOT . '/template/admin/views/pages/add.php');
    }



    // ИЗМЕНИТЬ СТРАНИЦУ
    public function actionEditpage($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_pages'])) {
            System::redirectUrl('/admin');
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();

        if (isset($_POST['editpage']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_pages'])) {
                System::redirectUrl('/admin');
            }
            $name = htmlentities($_POST['name']);
            $status = intval($_POST['status']);
            if (empty($_POST['alias'])) $alias = System::Translit($_POST['name']);
            else $alias = $_POST['alias'];

            if (empty($_POST['title'])) $title = $_POST['name'];
            else $title = htmlentities($_POST['title']);

            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);

            $curl = htmlentities($_POST['curl']);

            $content = $_POST['content'];
            $in_head = $_POST['in_head'];
            $in_body = $_POST['in_body'];
            $tmpl = intval($_POST['tmpl']);
            $custom_code = $_POST['custom_code'];

            $edit = System::editPage($id, $name, $status, $alias, $title, $meta_desc, $meta_keys, $content, $tmpl, $in_head,
                $in_body, $custom_code, $curl);
            if ($edit) header("Location: ".$setting['script_url']."/admin/statpages/edit/$id?success");
        }

        $page = System::getPageData($id);

        require_once (ROOT . '/template/admin/views/pages/edit.php');
    }



    // УДАЛИТЬ СТАТИЧЕСКУЮ СТРАНИЦУ
    public function actionDelpage($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_pages'])) {
            System::redirectUrl('/admin');
        }
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();
        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = System::deletePage($id);

            if ($del) header("Location: ".$setting['script_url']."/admin/statpages?success");
        }
    }


    /**
     *  ВИДЖЕТЫ
     */



    public function actionWidgets()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_widgets'])) {
            System::redirectUrl('/admin');
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $widgets = Widgets::getAllWidgets();

        require_once (ROOT . '/template/admin/views/widgets/index.php');
    }


    public function actionAddwidget()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_widgets'])) {
            System::redirectUrl('/admin');
        }

        $name = $_SESSION['admin_name'];
        $setting = System::getSetting();
        $type = isset($_GET['type']) ? htmlentities($_GET['type']) : 'html';

        if (isset($_POST['addwidget']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_widgets'])) {
                System::redirectUrl('/admin');
            }

            $type = $_POST['type'];
            $title = htmlentities($_POST['title']);
            $position = htmlentities(($_POST['position']));
            $page = $_POST['page'];
            $desc = htmlentities($_POST['desc']);
            $affix = 0;
            $params = isset($_POST['widget']) ? serialize($_POST['widget']) : null;
            $sort = intval($_POST['sort']);
            $status = intval($_POST['status']);
            $date = time();
            $private = $_POST['private'];

            $show_header = intval($_POST['show_header']);
            $header = htmlentities($_POST['header']);
            $show_subheader = intval($_POST['show_subheader']);
            $subheader = htmlentities($_POST['subheader']);
            $show_right_button = intval($_POST['show_right_button']);
            $right_button_name = htmlentities($_POST['right_button_name']);
            $right_button_link = htmlentities($_POST['right_button_link']);

            $suffix = htmlentities($_POST['suffix']);
            $show_for_course = isset($_POST['show_for_course']) ? base64_encode(serialize($_POST['show_for_course'])) : null;
            $show_for_training = isset($_POST['show_for_training']) ? json_encode($_POST['show_for_training']) : null;

            $add = Widgets::addWidget($type, $title, $position, $page, $desc, $affix, $params, $sort, $status, $date, $private,
                $show_header, $header, $show_subheader, $subheader, $show_right_button, $right_button_name, $right_button_link,
                $suffix, $show_for_course, $show_for_training);

            if ($add) {
                header("Location: ".$setting['script_url']."/admin/widgets");
            }
        }

        require_once (ROOT . '/template/admin/views/widgets/add.php');
    }



    public function actionEditwidget($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_widgets'])) {
            System::redirectUrl('/admin');
        }

        $name = $_SESSION['admin_name'];
        $id = intval($id);
        $setting = System::getSetting();

        if (isset($_POST['editwidget']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            if (!isset($acl['change_widgets'])) {
                System::redirectUrl('/admin');
            }

            $title = htmlentities($_POST['title']);
            $position = htmlentities(($_POST['position']));
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $desc = htmlentities($_POST['desc']);
            $affix = 0;
            $suffix = htmlentities($_POST['suffix']);
            $sort = intval($_POST['sort']);
            $status = intval($_POST['status']);
            $show_header = intval($_POST['show_header']);
            $header = htmlentities($_POST['header']);
            $show_subheader = intval($_POST['show_subheader']);
            $subheader = htmlentities($_POST['subheader']);
            $show_right_button = intval($_POST['show_right_button']);
            $right_button_name = htmlentities($_POST['right_button_name']);
            $right_button_link = htmlentities($_POST['right_button_link']);

            $params = isset($_POST['widget']) ? serialize($_POST['widget']) : null;
            $private = $_POST['private'];
            $show_for_course = isset($_POST['show_for_course']) ? base64_encode(serialize($_POST['show_for_course'])) : null;
            $show_for_training = isset($_POST['show_for_training']) ? json_encode($_POST['show_for_training']) : null;

            $add = Widgets::editWidget($id, $title, $position, $page, $desc, $affix, $params, $sort, $status, $private,
                $show_header, $header, $show_subheader, $subheader, $show_right_button, $right_button_name, $right_button_link,
                $suffix, $show_for_course, $show_for_training
            );
            if ($add) {
                header("Location: ".$setting['script_url']."/admin/widgets/edit/$id?success");
            }
        }

        $widget = Widgets::getWidgetData($id);
        $pages = Widgets::getWidgetsPage($id);
        $params = unserialize($widget['params']);
        $show = $widget['show_for_course'] != null ? unserialize(base64_decode($widget['show_for_course'])) : [];
        $show_training = $widget['show_for_training'] != null ? json_decode($widget['show_for_training'], true) : [];


        require_once (ROOT . '/template/admin/views/widgets/edit.php');
    }


    public function actionDelwidget($id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_widgets'])) {
            System::redirectUrl('/admin');
        }
        $setting = System::getSetting();
        $name = $_SESSION['admin_name'];
        $id = intval($id);
        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = Widgets::deleteWidget($id);

            if ($del) {
                header("Location: /admin/widgets");
            }
        }
    }
}