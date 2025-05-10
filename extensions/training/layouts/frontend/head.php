<?php defined('BILLINGMASTER') or die;

$is_page = !isset($is_page) ? $this->is_page : $is_page;
$title = !isset($title) ? $this->title : $title;
$meta_desc = !isset($meta_desc) ? $this->meta_desc : $meta_desc;
$meta_keys = !isset($meta_keys) ? $this->meta_keys = $this->meta_keys : $meta_keys;
$setting = !isset($setting) ? $this->setting : $setting;
$use_css = !isset($use_css) ? $this->use_css : $use_css;
require_once (ROOT . "/template/{$this->setting['template']}/layouts/head.php");