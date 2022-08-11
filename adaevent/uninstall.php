<?php
// if uninstall.php is not called by WordPress, die
if (!defined('WP_UNINSTALL_PLUGIN')) {
    wp_die("Sorry you can't call me directly");
}

define( 'ADAEVENT_TABLE_NAME', 'adaevent' );

// Uninstall plugin
global $wpdb;
$sql = 'DROP TABLE `' . $wpdb->prefix . ADAEVENT_TABLE_NAME . '`; ';
if (!$wpdb->query($sql)) {
    wp_die("The sql could not be executed: [" . $sql . "]");
}

