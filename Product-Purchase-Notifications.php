<?php

/**
 *
 * Plugin Name: Product-Purchase-Notifications
 * Description: Generate Notifications for latest order Placed on the site
 * Author: Bitcraftx Team
 * Version: 1.0
 * Author URI: https://bitcraftx.com/
 */

use function PHPSTORM_META\type;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
require_once(ABSPATH . 'wp-admin/includes/plugin.php');

/**
 * check for WooCommerce activation.
 */
if (!(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))) {
    deactivate_plugins(plugin_basename(__FILE__));
    if (isset($_GET['activate'])) {
        unset($_GET['activate']);
    }
    function sample_admin_notice__error()
    {
        $class = 'notice notice-error is-dismissible text-danger';
        $message = __('woocommerce is required For to activate Product-Purchase-Notifications plugin.', 'sample-text-domain');
        printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
    add_action('admin_notices', 'sample_admin_notice__error');

}
class NotifyMe
{
    /**
     * constructor
     */
    function __construct()
    {
        $this->setup();
    }
    function setup()
    {
        register_activation_hook(__FILE__, array($this, 'nm_activate'));
        add_action("wp_enqueue_scripts", array($this, 'addScriptsAndStyleSheet'));

        add_filter('plugin_action_links', array($this, 'settingPagelink'), 10, 2); //setting option in plugin with active/deactivate.

        add_action('wp_ajax_generatepopup', array($this, 'generatepopup'));

        add_action('wp_ajax_nopriv_generatepopup', array($this, 'generatepopup'));
    }
    /**
     * activate plugin
     */
    function nm_activate()
    {
        update_option("notify_time", 5);
        update_option("notify_toggle_img", 1);
        update_option("notify_showOrHideName", 1);
        update_option("notify_positionOfTheBanner", 1);
        update_option("notify_color_title", '#050505');
        update_option("notify_color_text", "#b1afaf");
        update_option("notify_color_background", "#ffffff");
        update_option("notify_color_shadow", "#a1a1a1");
    }
    /**
     * enqueue script and styles sheet
     */
    function addScriptsAndStyleSheet()
    {
        wp_enqueue_script('custom', plugin_dir_url(__FILE__) . 'public/js/script.js', ['jquery'], '1.0');
        wp_enqueue_style('bannerStyle', plugin_dir_url(__FILE__) . 'public/css/banner-style.css', null, '1.0');
        $this->notifyMe_nonce = wp_create_nonce("notifyMe_nonce");
        wp_localize_script("custom", "localizedData", ["url" => admin_url("admin-ajax.php"), 'timeDisapear' => 5000, 'nonce' => $this->notifyMe_nonce, 'action' => "generatepopup"]);
    }
    /**
     * callback for ajax request it fetch data from the database about recent order placed across the site.
     */
    function generatepopup()
    {
        
        $shownIds = sanitize_text_field($_REQUEST['ids']);
        $nonce = $_REQUEST['nonce'];
        if(!is_array($shownIds)){
            wp_send_json('');
        }
        if (wp_verify_nonce($nonce, 'notifyMe_nonce')) {
            $resp = [];
            $resp["items"] = [];
            $oneProduct = [];
            $counter = 0;
            $TotalLatestOrder = 5; //how many record to show
            // wp_send_json('');die;
            $totalOrders = wc_get_orders(array('limit' => -1, 'return' => 'ids'));
            $totalOrders = sizeof($totalOrders);
            $last_order_id = wc_get_orders(array('limit' => $TotalLatestOrder, 'exclude' => $shownIds, 'orderby' => 'date', 'order' => 'DESC', 'return' => 'ids'));
            if (sizeof($last_order_id) < $TotalLatestOrder) {
                $TotalLatestOrder = sizeof($last_order_id);
            }
            $randomNumber = random_int(0, $TotalLatestOrder - 1); //counter to fetch random record

            if (!empty($last_order_id)) {
                //meta fetching
                $notify_time = get_option("notify_time");
                $notify_toggle_img = get_option("notify_toggle_img");
                $notify_showOrHideName = get_option("notify_showOrHideName");
                $notify_positionOfTheBanner = get_option("notify_positionOfTheBanner");
                $notify_color_title = get_option("notify_color_title");
                $notify_color_text = get_option("notify_color_text");
                $notify_color_background = get_option("notify_color_background");
                $notify_color_shadow = get_option("notify_color_shadow");
                //Fetch Order's detail

                $order = wc_get_order($last_order_id[$randomNumber]);

                if ($order) {
                    $id = $order->get_id();
                    $sub = $order->get_total();
                    if ($order->get_total() >= 0) {

                        $firstName = $order->get_billing_first_name();
                        $lastName = $order->get_billing_last_name();
                        if ($firstName && $lastName) {
                            $buyerName = $firstName . ' ' . $lastName;
                        }
                        else {
                            $user = $order->get_user();
                            if ($user) {
                                $buyerName = $user->user_nicename;
                            }
                            else {
                                $buyerName = "SomeOne";
                            }
                        }
                        $PlacedAt = $order->get_date_created();
                        $get_billing_city = $order->get_billing_city();
                        if ($order->get_items()) {
                            foreach ($order->get_items() as $item_id => $item) { //fetch each product
                                $oneProduct[$counter]['product_name'] = $item->get_name();
                                $oneProduct[$counter]['quantity'] = $item->get_quantity();
                                $oneProduct[$counter]['product_type'] = $item->get_type();
                                $oneProduct[$counter]['product_id'] = $product_id = $item->get_product_id();
                                $oneProduct[$counter]['Permalink'] = get_the_permalink($product_id);
                                $oneProduct[$counter]['imgurl'] = wp_get_attachment_image_src(get_post_thumbnail_id($product_id), 'single-post-thumbnail');
                                $counter++;
                            }
                        }
                    }
                    //Generate time String to show on banner.
                    if ($PlacedAt) {
                        $interval = $this->makeTimeStirngMsg($PlacedAt);
                    }

                    //check how much items are in one order.
                    if ($oneProduct) {
                        $TotelProductInOneOrder = sizeof($oneProduct);
                        $randomProductinOrder = random_int(0, $TotelProductInOneOrder - 1);
                    }

                    //Add data to array
                    //OrderDetails
                    $resp += ["id" => $id];
                    $resp += ["totalOrders" => $totalOrders];
                    $resp += ["shownIDs" => sizeof($shownIds)];
                    $resp += ["buyer" => $buyerName];
                    $resp += ["get_billing_city" => $get_billing_city];
                    $resp += ["PlacedAt" => $interval];
                    //Styling,times and position to show the Notification
                    $resp += ["notify_time" => $notify_time];
                    $resp += ["notify_toggle_img" => $notify_toggle_img];
                    $resp += ["notify_showOrHideName" => $notify_showOrHideName];
                    $resp += ["notify_positionOfTheBanner" => $notify_positionOfTheBanner];
                    $resp += ["notify_color_title" => $notify_color_title];
                    $resp += ["notify_color_text" => $notify_color_text];
                    $resp += ["notify_color_background" => $notify_color_background];
                    $resp += ["notify_color_shadow" => $notify_color_shadow];
                    //Product Details
                    $resp["items"] += ["product_name" => $oneProduct[$randomProductinOrder]['product_name']];
                    $resp["items"] += ["quantity" => $oneProduct[$randomProductinOrder]['quantity']];
                    $resp["items"] += ["product_type" => $oneProduct[$randomProductinOrder]['product_type']];
                    if ($notify_toggle_img == 1) {
                        $resp["items"] += ["imgurl" => $oneProduct[$randomProductinOrder]['imgurl']];
                    }
                    $resp["items"] += ["Permalink" => $oneProduct[$randomProductinOrder]['Permalink']];

                    wp_send_json($resp);
                }
            }
        }
        else {
            wp_send_json('');
        }

        die();
    }
    /**
     * make a string to display on front-end with notification.
     */
    function makeTimeStirngMsg($DateObj)
    {

        $currentTime = new DateTime();

        $timeInterval = date_diff($DateObj, $currentTime);
        $months = (int)$timeInterval->format('%m');
        $days = (int)$timeInterval->format('%d');
        $hours = (int)$timeInterval->format('%h');
        $minutes = (int)$timeInterval->format('%i');
        $seconds = (int)$timeInterval->format('%s');

        //month
        $timeArray = [];
        if ($months != '0') {
            if ($months == '1') {
                $months = "$months Month";
            }
            else {
                $months = "$months Months";
            }
            array_push($timeArray, $months);
        }
        //days
        if ($days != '0') {
            if ($days == '1') {
                $days = "$days Day";
            }
            else {
                $days = "$days Days";
            }
            array_push($timeArray, $days);
        }
        //hours
        if ($hours != '0') {
            if ($hours == '1') {
                $hours = "$hours Hour";
            }
            else {
                $hours = "$hours Hours";
            }
            array_push($timeArray, $hours);
        }
        //minutes 
        if ($minutes != '0') {
            if ($minutes == '1') {
                $minutes = "$minutes Minute";
            }
            else {
                $minutes = "$minutes Minutes";
            }
            array_push($timeArray, $minutes);
        }
        // seconds
        if ($seconds != '0') {
            if ($seconds >= '15' || $timeArray != []) {
                $seconds = "$seconds Seconds";
            }
            else if ($seconds <= '15' && $timeArray == []) {
                $seconds = "Just Now";
            }
            array_push($timeArray, $seconds);
        }
        if (sizeof($timeArray) > 3) {
            $timeArray = array_slice($timeArray, 0, 3);
        }
        $timeString = '';
        $timeString .= $timeArray[0] . ", ";
        $timeString .= $timeArray[1] . ", And ";
        $timeString .= $timeArray[2] . " Ago";
        return $timeString;
    }
    /**
     * Display a setting button along with the a Activate/Deactivate Plugin button on plugin Menu-page.
     */
    function settingPagelink($links, $file)
    {
        if ($file == plugin_basename(dirname(__FILE__) . '/notifyme.php')) {
            /*
             * Insert the link at the beginning.
             */
            $in = '<a href="options-general.php?page=notify_me">' . __('Settings', 'mtt') . '</a>';
            array_unshift($links, $in);
        }
        return $links;
    }
}

include_once plugin_dir_path(__FILE__) . 'admin/option_page.php';
new NotifyMe();
new NotifyMeSetting();
?>
