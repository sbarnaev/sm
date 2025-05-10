<?php defined('BILLINGMASTER') or die;

require_once(__DIR__ . '/add_media.php');
require_once(__DIR__ . '/add_playlist.php');
require_once(__DIR__ . '/add_text.php');
require_once(__DIR__ . '/add_attach.php');
require_once(__DIR__ . '/add_html.php');
if (System::CheckExtensension('polls', 1)) {
    require_once(__DIR__ . '/add_poll.php');
}
if (System::CheckExtensension('forum2', 1)) {
    require_once(__DIR__ . '/add_forum.php');
}?>

<div class="modal-elements">
    <div id="modal_edit_element" class="uk-modal">
        <div class="uk-modal-dialog uk-modal-add-elem">
            <div class="userbox modal-userbox-3"></div>
        </div>
    </div>
</div>