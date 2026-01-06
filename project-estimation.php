<?php

/**
 * Plugin Name: Project Estimation
 * Description: A project estimation plugin for WordPress.
 * Author: Hiren Gediya
 * Domain : project-estimation
 */

include plugin_dir_path(__FILE__) . 'includes/functions.php';

register_activation_hook(__FILE__, 'project_estimation_activate');
register_uninstall_hook(__FILE__, 'project_estimation_uninstall');

function project_estimation_activate()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'project_estimation';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        number varchar(20) NOT NULL,
        email varchar(100) NOT NULL,
        company_name varchar(255),
        site_url varchar(255),
        new_project_url varchar(255),
        project_name varchar(255),
        project_type varchar(255),
        project_brief text,
        estimation_amount varchar(50) DEFAULT '0.00',
        extra_amount varchar(50) DEFAULT '0.00',
        estimation_date datetime DEFAULT '0000-00-00 00:00:00',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function project_estimation_uninstall()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'project_estimation';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}
