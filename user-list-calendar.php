<?php
/**
 * Plugin Name: User List Calendar
 * Plugin URI:
 * Description:
 * Version: 1.0.0
 * Author: DEV Aecoded
 * Author URI:
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: aecoded
 */

// Prevent direct access to the plugin file
defined('ABSPATH') || exit;


/**
 * Require All Admin CSS, JS Files Here
 */
function admin_enqueue_scripts() {
    
    // css file 
    wp_enqueue_style('evo_calendar_css', plugin_dir_url(__FILE__) . '/assets/css/evo-calendar.min.css', '', '1.0.0', '');
    wp_enqueue_style('custom_admin_css', plugin_dir_url(__FILE__) . '/assets/css/custom-admin.css', '', '1.0.0', '');

    // js file 
    wp_enqueue_script('chart_script', plugin_dir_url(__FILE__) . 'assets/js/chart.js', array('jquery'), '1.0.0', false);
    wp_enqueue_script('evo_calendar_script', plugin_dir_url(__FILE__) . 'assets/js/evo-calendar.min.js', array('jquery'), '1.0.0', true);
    wp_enqueue_script('custom_admin_script', plugin_dir_url(__FILE__) . 'assets/js/custom-admin.js', array('jquery'), '1.0.0', true);

    // Ajax Request URL
    wp_localize_script('custom_admin_script', 'formAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'admin_enqueue_scripts');


/**
 * Require All Include Files Here
 */
require_once plugin_dir_path(__FILE__) . 'admin-menu.php';


/**
 * filter user with ajax handler
 */
add_action('wp_ajax_user_filter_calendar', 'user_filter_calendar_callback');
add_action('wp_ajax_nopriv_user_filter_calendar', 'user_filter_calendar_callback');

function user_filter_calendar_callback() { 

    if(isset($_POST['get_date'])) { 
        $get_date = $_POST['get_date'];

        $date_components = date_parse_from_format('m/d/Y', $get_date);
        $year = $date_components['year'];
        $month = $date_components['month'];
        $day = $date_components['day'];

        $args = array(
            'date_query' => array(
                array(
                    'year' => $year,
                    'month' => $month,
                    'day' => $day,
                ),
            ),
        );
        $users = get_users($args);

        $response = array();

        if(!empty($users)) {
            foreach ($users as $user) {
                // Get user data
                $username = $user->user_login;
                $email = $user->user_email;
                $registered_date = date('Y-m-d', strtotime($user->user_registered));

                $first_name = get_user_meta($user->ID, 'first_name', true);
                $last_name = get_user_meta($user->ID, 'last_name', true);
                $billing_phone = get_user_meta($user->ID, 'billing_phone', true);

                $response[] = array(
                    'username' => $username,
                    'email' => $email,
                    'name' => $first_name . ' ' . $last_name,
                    'phone' => $billing_phone,
                    'registered_date' => $registered_date,
                );
            }
            echo json_encode($response);

        } else {
            $response = array(
                'not_found' => 'No User is found in this date',
            );

            echo json_encode($response);
        }
    }

    wp_die();

}