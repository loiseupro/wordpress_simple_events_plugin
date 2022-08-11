<?php
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

// Declarations
$invalid_nonce = false;
$nonce_delete_name = 'adaevent_delete_nonce';
$adaevent = new adaevent();

// Load to delete
if (isset($_REQUEST["action"]) and $_REQUEST["action"] == "delete_adaevent" and isset($_REQUEST['ae_delete_nonce'])) {
    if (!wp_verify_nonce($_REQUEST['ae_delete_nonce'], $nonce_delete_name)) {
        $invalid_nonce = true;
    } else {
        if (isset($_REQUEST["id"]) and $adaevent->existsEvent($_REQUEST["id"])) {
            $event_to_delete = $adaevent->getEventById($_REQUEST["id"]);
        }
    }
}

?>

<div class="wrap adaevent-wrap">
    <h1><?php echo __('Delete Event', 'adaevent'); ?></h1>

    <?php
    if ($invalid_nonce) {
    ?>
        <div class="error notice-error inline inline">
            <p><b><?php echo __("Invalid nonce!", 'adaevent'); ?></b></p>
            <p><?php echo __("Unable to process changes to this event", 'adaevent'); ?></p>
        </div>
        <?php
    } else {
        if (isset($event_to_delete)) {
            if ($adaevent->deleteEvent($_REQUEST["id"])) {
        ?>
                <div class="notice notice-success  inline">
                    <p><?php echo $event_to_delete->name; ?></p>
                    <p><b><?php echo __("The event has been deleted successfully", 'adaevent'); ?></b></p>
                </div>
            <?php
            } else {
            ?>
                <div class="error notice-error inline inline">
                    <p><b><?php echo __("An error occurred while trying to delete the event", 'adaevent'); ?></b></p>
                </div>
            <?php
            }
        } else {
            ?>
            <div class="error notice-error inline inline">
                <p><b><?php echo __("An error has occurred", 'adaevent'); ?></b></p>
            </div>
    <?php
        }
    }
    ?>
</div>