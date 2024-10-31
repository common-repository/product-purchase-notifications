<?php

class NotifyMeSetting
{
    /**
     * constructor
     */
    function __construct()
    {
        $this->setup();
    }
    /**
     * Actions.
     * enqueue script and style sheet for admin.
     * add manu page on admin dashboard.
     */
    function setup()
    {
        add_action("admin_menu", array($this, "add_page"));
        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_script('handle', plugin_dir_url(__FILE__) . '/js/script.js');
            wp_enqueue_style('admin-notify-syling', plugin_dir_url(__FILE__) . '/style/style.css');
        });
    }
    /**
     * add submenu page under setting page.
     */
    function add_page()
    {

        add_options_page("Notify Setting Page", "Notify Me Settings", "manage_options", "notify_me", array($this, "NotifysettingPageHtml"));
        //setting registered
        register_setting('notify_me_group', "notify_time");
        register_setting('notify_me_group', "notify_toggle_img");
        register_setting('notify_me_group', "notify_showOrHideName");
        register_setting('notify_me_group', "notify_positionOfTheBanner");
        register_setting('notify_me_group', "notify_color_title");
        register_setting('notify_me_group', "notify_color_text");
        register_setting('notify_me_group', "notify_color_background");
        register_setting('notify_me_group', "notify_color_shadow");
        //section created and fields.
        //first
        add_settings_section("notify_me_Section_time", "Notify Me Settings", [$this, "Notify_me_section"], "notify_me");
        add_settings_field("notify_me_field_time", "Select Time (Interval Between Notification)", [$this, "notify_me_field_time_cb"], "notify_me", "notify_me_Section_time");
        add_settings_field("notify_me_field_toggle_img", "Show/Hide Image of Product", [$this, "notify_me_field_toggle_img_cb"], "notify_me", "notify_me_Section_time");
        add_settings_field("notify_me_field_showOrHideName", "Show Buyer Name with Notification", [$this, "notify_me_field_showOrHideName_cb"], "notify_me", "notify_me_Section_time");
        add_settings_field("notify_me_field_positionOfTheBanner", "Show Notification at Bottom-left/Bottom-right", [$this, "notify_me_field_positionOfTheBanner_cb"], "notify_me", "notify_me_Section_time");
        add_settings_field("notify_me_field_color_title", "Color of Product Name (Title)", [$this, "notify_me_field_color_title_cb"], "notify_me", "notify_me_Section_time");
        add_settings_field("notify_me_field_color_text", "Color of other text (Time & Buyer)", [$this, "notify_me_field_color_text_cb"], "notify_me", "notify_me_Section_time");
        add_settings_field("notify_me_field_color_background", "Background Color", [$this, "notify_me_field_color_background_cb"], "notify_me", "notify_me_Section_time");
        add_settings_field("notify_me_field_color_shadow", "Background Shadow Color", [$this, "notify_me_field_color_shadow_cb"], "notify_me", "notify_me_Section_time");

    }
    /**
     * generate html for notifyme settings page.
     */
    function NotifysettingPageHtml()
    {
?>
        <div class="notiyme-container">
        <form action="options.php" method="post">
        <?php
        settings_fields("notify_me_group");
        do_settings_sections("notify_me");
        submit_button(); ?>
        
        </form>
        </div>
        <?php


    }
    /**
     * section callback
     */
    function Notify_me_section()
    {
    // echo "<h5 class='wraper'>customize your banner</h5>";
    }
    //field callback
    /**
     * 
     */
    function notify_me_field_time_cb()
    {
        $notify_time = get_option("notify_time");
        echo "<input type='range' min='5' max='20' placeholder='set time' name='notify_time' id='timee' value='".esc_html($notify_time)."'><label for='timee' id='timeeLabel'>".esc_html($notify_time)."</label><span> s</span>";
    }
    function notify_me_field_toggle_img_cb()
    {
        $notify_toggle_img = get_option("notify_toggle_img");
        if ($notify_toggle_img == 1) {
            echo "<label for='show' style=' margin-right:12px;'>Show</label>       <input type='radio'  name='notify_toggle_img' id='show' value='1' style=' margin-right:12px' checked>";
            echo "<label for='hide' style=' margin-right:12px'>Hide</label>         <input type='radio'  name='notify_toggle_img' id='hide' value='0' >";
        }
        elseif ($notify_toggle_img == 0) {
            echo "<label for='show' style=' margin-right:12px ;'>Show</label>       <input type='radio'  name='notify_toggle_img' id='show' value='1' style=' margin-right:12px' >";
            echo "<label for='hide' style=' margin-right:12px'>Hide</label>         <input type='radio'  name='notify_toggle_img' id='hide' value='0' checked>";

        }
    }
    function notify_me_field_showOrHideName_cb()
    {
        $notify_showOrHideName = get_option("notify_showOrHideName");
        if ($notify_showOrHideName == 1) {
            echo "<label for='showName' style=' margin-right:12px'>Show</label>       <input type='radio'  name='notify_showOrHideName' id='showName' value='1' style=' margin-right:12px' checked>";
            echo "<label for='hideName' style=' margin-right:12px'>Hide</label>         <input type='radio'  name='notify_showOrHideName' id='hideName' value='0' >";
        }
        elseif ($notify_showOrHideName == 0) {
            echo "<label for='showName' style=' margin-right:12px'>Show</label>       <input type='radio'  name='notify_showOrHideName' id='showName' value='1' style=' margin-right:12px' >";
            echo "<label for='hideName' style=' margin-right:12px'>Hide</label>         <input type='radio'  name='notify_showOrHideName' id='hideName' value='0' checked>";

        }

    }
    function notify_me_field_positionOfTheBanner_cb()
    {
        $notify_positionOfTheBanner = get_option("notify_positionOfTheBanner");

        if ($notify_positionOfTheBanner == 1) {
            echo "<label for='left' style=' margin-right:12px'>Left</label>       <input type='radio'  name='notify_positionOfTheBanner' id='left' value='1' style=' margin-right:12px' checked>";
            echo "<label for='right' style=' margin-right:12px'>Right</label>         <input type='radio'  name='notify_positionOfTheBanner' id='right' value='0' >";
        }
        elseif ($notify_positionOfTheBanner == 0) {
            echo "<label for='left' style=' margin-right:12px'>Left</label>       <input type='radio'  name='notify_positionOfTheBanner' id='left' value='1' style=' margin-right:12px' >";
            echo "<label for='right' style=' margin-right:12px'>Right</label>         <input type='radio'  name='notify_positionOfTheBanner' id='right' value='0' checked>";
        }
    }
    function notify_me_field_color_title_cb()
    {
        $notify_color_title = get_option("notify_color_title");
        $notify_color_title = $notify_color_title ?? '#797c7e';

        echo "<input type='color' id='notify_color_title' name='notify_color_title' value='".esc_html($notify_color_title)."'> ";
    }
    function notify_me_field_color_text_cb()
    {
        $notify_color_text = get_option("notify_color_text");
        $notify_color_text = $notify_color_text ?? '#797c7e';

        echo "<input type='color' id='notify_color_text' name='notify_color_text' value='".esc_html($notify_color_text)."'> ";
    }
    function notify_me_field_color_background_cb()
    {
        $notify_color_background = get_option("notify_color_background");
        $notify_color_background = $notify_color_background ?? '#eee';

        echo "<input type='color' id='notify_color_background' name='notify_color_background' value='".esc_html($notify_color_background)."'> ";
    }
    function notify_me_field_color_shadow_cb()
    {
        $notify_color_shadow = get_option("notify_color_shadow");
        $notify_color_shadow = $notify_color_shadow ?? '#727171';

        echo "<input type='color' id='notify_color_shadow' name='notify_color_shadow' value='".esc_html($notify_color_shadow)."'> ";
    }
}
?>
