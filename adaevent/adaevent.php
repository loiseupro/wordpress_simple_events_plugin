<?php

/**
 * @package Adaevent
 */
/*
Plugin Name: Adaevent
Plugin URI: https://adaweb.es/
Description: Simple events plugin
Version: 1.0
Author: Lois
Author URI: https://adaweb.es/
License: GPLv2 or later
Text Domain: event
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

// Make sure we don't expose any info if called directly
if (!function_exists('add_action') or !defined('ABSPATH')) {
    wp_die("Sorry you can't call me directly");
}

define('ADAEVENT_TABLE_NAME', 'adaevent');

require_once plugin_dir_path(__FILE__) . "includes/adaeventClass.php";


/**
 * Install plugin
 * 
 * @return bool
 */
function ae_install_plugin() {
    global $wpdb;

    $sql = 'CREATE TABLE IF NOT EXISTS `' . $wpdb->prefix . ADAEVENT_TABLE_NAME . '` ( 
        `id` INT NOT NULL AUTO_INCREMENT, 
        `name` TEXT NOT NULL , 
        `place` VARCHAR(25) NOT NULL , 
        `start` VARCHAR(25) NOT NULL , 
        `end` VARCHAR(25) NOT NULL ,         
        `timestamp_add` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `timestamp_update` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `user_add` INT(3) NOT NULL , 
        `user_update` INT(3) NOT NULL ,        
        PRIMARY KEY (`id`)) 
        DEFAULT CHARSET=utf8;';

    if (!$wpdb->query($sql)) {
        wp_die("The sql could not be executed: [" . $sql . "]");
    }

    return true;
}

register_activation_hook(__FILE__, 'ae_install_plugin');


/**
 * Deactivate plugin
 * 
 * @return bool
 */
function ae_deactivate_plugin() {
    return true;
}

register_deactivation_hook(__FILE__, 'ae_deactivate_plugin');


/**
 * Add front CSS and SCRIPTS
 * 
 */
function ae_public_style_and_script() {
    wp_enqueue_style('adaevent-style',  plugin_dir_url(__FILE__) . '/public/css/ae_styles.css');
    wp_enqueue_script('adaevent-script', plugin_dir_url(__FILE__) . '/public/scripts/ae_script.js', array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'ae_public_style_and_script');


/**
 * Add backend CSS and SCRIPTS
 * 
 */
function ae_admin_style_and_script() {
    wp_enqueue_style('adaevent-style',  plugin_dir_url(__FILE__) . '/admin/css/ae_styles.css');
    wp_enqueue_script('adaevent-script', plugin_dir_url(__FILE__) . '/admin/scripts/ae_script.js', array(), '1.0.0', true);
}
add_action('admin_enqueue_scripts', 'ae_admin_style_and_script');


/**
 * Menú
 * 
 */
function ae_menu() {

    $path_info = plugin_dir_path(__FILE__) . 'admin/templates/info.php';
    $path_list = plugin_dir_path(__FILE__) . 'admin/templates/list.php';
    $path_create = plugin_dir_path(__FILE__) . 'admin/templates/create.php';
    $path_delete = plugin_dir_path(__FILE__) . 'admin/templates/remove.php';

    add_menu_page('AdaEvent', 'Ada Events', 'manage_options', $path_info, '', 'dashicons-cloud');
    add_submenu_page($path_info, 'List events', __("List events", "adaevent"), 'manage_options', $path_list);
    add_submenu_page($path_info, 'Create event', __("Create event", "adaevent"), 'manage_options', $path_create);
    add_submenu_page("", 'Delete event', __("Delete event", "adaevent"), 'manage_options', $path_delete);
}
add_action('admin_menu', 'ae_menu');


/**
 * SHORTCODE list all events
 * Example of use: [adaevents only_future=true]
 * 
 */
function shortcode_adaevents($atts) {
    $attrs = shortcode_atts(['only_future' => true,], $atts);
    $adaevent = new adaevent();
    $events = $adaevent->getEvents();
    return $adaevent->buildEvenListHtml($events, $attrs['only_future']);
}
add_shortcode('adaevents', 'shortcode_adaevents');


/**
 * SHORTCODE list event by id
 * Example of use: [adaeventbyid id=1]
 * 
 */
function shortcode_adaeventById($atts) {
    $attrs = shortcode_atts(['id' => false,], $atts);
    $adaevent = new adaevent();
    $events = $adaevent->getEventById($attrs['id']);
    if (isset($events["id"])) {
        $events_arr[] = $events;
        return $adaevent->buildEvenListHtml($events_arr, false);
    } else {
        return '';
    }
}
add_shortcode('adaeventbyid', 'shortcode_adaeventById');
