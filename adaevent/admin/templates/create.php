<?php
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

// Declarations
$invalid_nonce = false;
$nonce_add_name = 'adaevent_add_nonce';
$nonce_update_name = 'adaevent_update_nonce';
$adaevent = new adaevent();

// Add o update
if (isset($_REQUEST["action"]) and $_REQUEST["action"] == "add_adaevent" and isset($_REQUEST['ae_add_nonce'])) {

    if (!wp_verify_nonce($_REQUEST['ae_add_nonce'], $nonce_add_name)) {
        $invalid_nonce = true;
    } else {
        $event_to_save = array();
        if (isset($_REQUEST["ae_name"]) and trim($_REQUEST["ae_name"]) != "") {

            $event_to_save["name"] = $_REQUEST["ae_name"];

            if (isset($_REQUEST["ae_id"]) and trim($_REQUEST["ae_id"]) != "") {
                $event_to_save["id"] = $_REQUEST["ae_id"];
            }

            if (isset($_REQUEST["ae_place"])) {
                $event_to_save["place"] = $_REQUEST["ae_place"];
            }

            if (isset($_REQUEST["ae_start"])) {
                $event_to_save["start"] = $_REQUEST["ae_start"];
            }

            if (isset($_REQUEST["ae_end"])) {
                $event_to_save["end"] = $_REQUEST["ae_end"];
            }

            // Check dates
            $error_process = false;
            if (isset($event_to_save["start"]) and isset($event_to_save["end"])) {
                if (date_create($event_to_save["start"]) > date_create($event_to_save["end"])) {
                    $error_process = true;
                    $event_to_update = $event_to_save;
                }
            }

            if (!$error_process) {
                $saved = $adaevent->setEvent($event_to_save);
                if ($saved !== false) {
                    $event_to_update = $adaevent->getEventById($saved);
                }
            }
        }
    }
}

// Load to update
if (isset($_REQUEST["action"]) and $_REQUEST["action"] == "update_adaevent" and isset($_REQUEST['ae_update_nonce'])) {
    if (!wp_verify_nonce($_REQUEST['ae_update_nonce'], $nonce_update_name)) {
        $invalid_nonce = true;
    } else {
        if (isset($_REQUEST["id"]) and $adaevent->existsEvent($_REQUEST["id"])) {
            $event_to_update = $adaevent->getEventById($_REQUEST["id"]);
        }
    }
}

?>

<div class="wrap adaevent-wrap">
    <h1><?php echo __('Create Event', 'adaevent'); ?></h1>

    <?php
    if ($invalid_nonce) {
    ?>
        <div class="error notice-error inline inline">
            <p><b><?php echo __("Invalid nonce!", 'adaevent'); ?></b></p>
            <p><?php echo __("Unable to process changes to this event", 'adaevent'); ?></p>
        </div>
    <?php
    } else {
    ?>

        <?php
        if (isset($saved)) {
            if (!$saved) {
        ?>
                <div class="error notice-error inline inline">
                    <p><b><?php echo __("Invalid nonce!", 'adaevent'); ?></b></p>
                    <p><?php echo __("The data could not be processed", 'adaevent'); ?></p>
                </div>
            <?php
            } else {
            ?>
                <div class="notice notice-success inline">
                    <p><b><?php echo __("The data has been processed successfully", 'adaevent'); ?></b></p>
                </div>
            <?php
            }
        }

        if ($error_process) {
            ?>
            <div class="error notice-error inline inline">
                <p><b><?php echo __("The end date cannot be less than the start date", 'adaevent'); ?></b></p>
            </div>
        <?php
        }
        ?>

        <form action="" method="post" id="nds_add_user_meta_form">

            <input type="hidden" name="action" value="add_adaevent">
            <input type="hidden" name="ae_add_nonce" value="<?php echo wp_create_nonce($nonce_add_name); ?>" />

            <?php if (isset($event_to_update)) { ?>
                <input type="hidden" name="ae_id" value="<?php echo $event_to_update["id"]; ?>">
            <?php } ?>

            <div>
                <p><label for="ae_name"> <?php echo __("Name", 'adaevent'); ?> </label></p>
                <input required id="ae_name" type="text" name="ae_name" value="<?php if (isset($event_to_update)) { echo $event_to_update["name"]; } ?>" placeholder="<?php echo __("Name", 'adaevent'); ?>" />
            </div>

            <div>
                <p><label for="ae_place"> <?php echo __("Place", 'adaevent'); ?> </label></p>
                <input required id="ae_place" type="text" name="ae_place" value="<?php if (isset($event_to_update)) { echo $event_to_update["place"]; } ?>" placeholder="<?php echo __("Place", 'adaevent'); ?>" />
            </div>

            <div>
                <p><label for="ae_start"> <?php echo __("Start date", 'adaevent'); ?> </label></p>
                <input required id="ae_start" type="date" name="ae_start" value="<?php if (isset($event_to_update)) { echo $event_to_update["start"]; } ?>" placeholder="<?php echo __("Start date", 'adaevent'); ?>" />
            </div>

            <div>
                <p><label for="ae_end"> <?php echo __("End date", 'adaevent'); ?> </label></p>
                <input required id="ae_end" type="date" name="ae_end" value="<?php if (isset($event_to_update)) { echo $event_to_update["end"]; } ?>" placeholder="<?php echo __("End date", 'adaevent'); ?>" />
            </div>

            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save', 'adaevent'); ?>">
            </p>
        </form>
    <?php
    }
    ?>
</div>