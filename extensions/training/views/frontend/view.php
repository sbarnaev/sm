<?php defined('BILLINGMASTER') or die;

$template_path = ROOT . "/template/{$this->setting['template']}/extensions/training/views/{$this->view_path}";
if (!file_exists($template_path)) {
    $template_path = ROOT . "/extensions/training/views/frontend/{$this->view_path}";
}
require_once ($template_path);