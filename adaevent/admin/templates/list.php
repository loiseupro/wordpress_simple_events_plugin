<?php
if (!current_user_can('manage_options')) {
    wp_die(__('You do not have sufficient permissions to access this page.'));
}

$adaevent = new adaevent();
$events = $adaevent->getEvents();
$nonce_update_name = 'adaevent_update_nonce';
$nonce_delete_name = 'adaevent_delete_nonce';

?>


<div class="wrap adaevent-wrap">
    <h1><?php echo __('Events list', 'adaevent'); ?></h1>
    <?php
    if (count($events) > 0) {
    ?>
        <p><b><?php echo __('All', 'adaevent'); ?></b> <?php echo count($events); ?></p>
        <table class="wp-list-table widefat fixed striped table-view-list">
            <thead>
                <tr>
                    <th><?php echo __("Id", 'adaevent'); ?></th>
                    <th><?php echo __("Name", 'adaevent'); ?></th>
                    <th><?php echo __("Place", 'adaevent'); ?></th>
                    <th><?php echo __("Start", 'adaevent'); ?></th>
                    <th><?php echo __("End", 'adaevent'); ?></th>
                    <th><?php echo __("Add date", 'adaevent'); ?></th>
                    <th><?php echo __("User add", 'adaevent'); ?></th>
                    <th><?php echo __("Update date", 'adaevent'); ?></th>
                    <th><?php echo __("User update", 'adaevent'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($events as $event) {
                    $user_add = get_userdata($event->user_add);
                    $user_update = get_userdata($event->user_update);

                    $update_url = "admin.php?page=adaevent%2Fadmin%2Ftemplates%2Fcreate.php&action=update_adaevent&id=" . $event->id;
                    $update_url = wp_nonce_url($update_url, $nonce_update_name, 'ae_update_nonce');

                    $delete_url = "admin.php?page=adaevent%2Fadmin%2Ftemplates%2Fremove.php&action=delete_adaevent&id=" . $event->id;
                    $delete_url = wp_nonce_url($delete_url, $nonce_delete_name, 'ae_delete_nonce');

                ?>
                    <tr>
                        <td><?php echo $event->id; ?></td>
                        <td>
                            <b>
                                <a href="<?php echo $update_url; ?>">
                                    <?php echo $event->name; ?>
                                </a>
                            </b>
                            <br/>
                            <span class="edit">
                                <a href="<?php echo $update_url; ?>">
                                    <?php echo __("Edit", 'adaevent'); ?>
                                </a> | 
                            </span>                     
                            <span class="trash">
                                <a href="<?php echo $delete_url; ?>" class="submitdelete">
                                    <?php echo __("Delete", 'adaevent'); ?>
                                </a> 
                            </span>                  
                        </td>
                        <td><?php echo $event->place; ?></td>
                        <td><?php echo $event->start; ?></td>
                        <td><?php echo $event->end; ?></td>
                        <td><?php echo $event->timestamp_add; ?></td>
                        <td><?php echo $user_add->user_nicename; ?></td>
                        <td><?php echo $event->timestamp_update; ?></td>
                        <td><?php echo $user_update->user_nicename; ?></td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    <?php
    } else {
    ?>
        <div class="notice notice-warning inline">
            <p><?php echo __("You haven't created any event yet.", 'adaevent'); ?></p>
        </div>
    <?php
    }
    ?>
</div>