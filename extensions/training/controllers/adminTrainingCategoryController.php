<?php defined('BILLINGMASTER') or die;


class adminTrainingCategoryController extends AdminBase {

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
     * СПИСОК КАТЕГОРИЙ ТРЕНИНГОВ
     */
    public function actionCats()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        $cat_list = TrainingCategory::getCatList(false);

        require_once (ROOT . '/extensions/training/views/admin/category/index.php');
    }


    /**
     * ДОБАВИТЬ КАТЕГОРИЮ ТРЕНИНГА
     */
    public function actionAddCat()
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['addcat']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $name = htmlentities($_POST['name']);
            $status = intval($_POST['status']);

            $alias = $_POST['alias'] ?: System::Translit($name);
            if (System::searchDuplicateAliases($alias, 'training_cats')) {
                $alias .= '-1';
            }

            $title = !empty($_POST['title']) ? $_POST['title'] : $name;
            $cat_desc = htmlentities($_POST['cat_desc']);
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            $img_alt = htmlentities($_POST['img_alt']);
            $parent_cat = intval($_POST['parent_cat']);
            $sort = intval($_POST['sort']);

            if (isset($_FILES['cover'])) {
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке

                $folder = ROOT . '/images/training/category/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if (is_uploaded_file($tmp_name)) {
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $img = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                }
            }

            $add = TrainingCategory::addCategory($name, $status, $alias, $title, $cat_desc, $meta_desc, $meta_keys, $img, $img_alt, $parent_cat, $sort);
            System::redirectUrl("/admin/training/cats", $add);
        }

        require_once (ROOT . '/extensions/training/views/admin/category/add.php');
    }


    /**
     * ИЗМЕНИТЬ КАТЕГОРИЮ
     * @param $cat_id
     */
    public function actionEditCat($cat_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['show_courses'])) {
            System::redirectUrl('/admin');
        }

        if (isset($_POST['editcat']) && isset($_POST['token']) && $_POST['token'] == $_SESSION['admin_token']) {
            $name = htmlentities($_POST['name']);
            $status = intval($_POST['status']);

            $alias = $_POST['alias'] ?: System::Translit($name);
            if (System::searchDuplicateAliases($alias, 'training_cats', $cat_id, 'cat_id')) {
                $alias .= '-1';
            }

            $title = !empty($_POST['title']) ? $_POST['title'] : $name;
            $cat_desc = htmlentities($_POST['cat_desc']);
            $meta_desc = htmlentities($_POST['meta_desc']);
            $meta_keys = htmlentities($_POST['meta_keys']);
            $img_alt = htmlentities($_POST['img_alt']);
            $parent_cat = intval($_POST['parent_cat']);
            $sort = intval($_POST['sort']);

            if (isset($_FILES['cover']) && $_FILES["cover"]["size"] != 0) {
                $tmp_name = $_FILES["cover"]["tmp_name"]; // Временное имя картинки на сервере
                $img = $_FILES["cover"]["name"]; // Имя картинки при загрузке

                $folder = ROOT . '/images/training/category/'; // папка для сохранения
                $path = $folder . $img; // Полный путь с именем файла
                if (is_uploaded_file($tmp_name)) {
                    if (file_exists($path)) {
                        $pathinfoimage = pathinfo($path);
                        $newname = $pathinfoimage['filename'].'-copy.'.$pathinfoimage['extension'];
                        $img = $newname;
                        $path = $folder . $newname;
                    }
                    move_uploaded_file($tmp_name, $path);
                }
            } else {
                $img = $_POST['current_img'];
            }

            $edit = TrainingCategory::editCategory($cat_id, $name, $status, $alias, $title, $cat_desc, $meta_desc,
                $meta_keys, $img, $img_alt, $parent_cat, $sort
            );
            System::redirectUrl("/admin/training/editcat/$cat_id", $edit);
        }

        $cat = TrainingCategory::getCategory($cat_id);
        require_once (ROOT . '/extensions/training/views/admin/category/edit.php');
    }


    /**
     * УДАЛИТЬ КАТЕГОРИЮ
     * @param $cat_id
     */
    public function actionDelCat($cat_id)
    {
        $acl = self::checkAdmin();
        if (!isset($acl['del_courses'])) {
            System::redirectUrl('/admin');
        }

        $cat_id = intval($cat_id);
        if (isset($_GET['token']) && $_GET['token'] == $_SESSION['admin_token']) {
            $del = Trainingcategory::DelCat($cat_id);
            System::redirectUrl("/admin/training/cats", $del);
        }
    }
}
