<?php
/*
Plugin Name: PFHB - Events Calendarium
Description: Wtyczka obsługująca kalendarium wydarzeń
Version: 0.1
Author: Piotr Kowalski
Author URI: https://piotrkowalski.pw
*/
add_action('admin_menu', 'wp_pfhb_scheduler_setup_menu');
define( 'MY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
function wp_pfhb_scheduler_setup_menu(){
    add_menu_page( 'Kalendarium wydarzeń', 'Kalendarium', 'manage_options', 'wp_pfhb_scheduler', 'main_admin_page_callback' );

}

function main_admin_page_callback(){
    include(MY_PLUGIN_PATH . "views/admin/main.php");

}

register_activation_hook( __FILE__, 'pfhb_scheduler_create_db' );
function pfhb_scheduler_create_db() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
    $sql = array();

	$table_name = $wpdb->prefix . 'pfhb_events';
    if($wpdb->get_var( "show tables like '$table_name'" ) != $table_name){

   
        $sql[] = "CREATE TABLE $table_name (
            id int(11) AUTO_INCREMENT,
            start_date datetime NOT NULL,
            end_date datetime NOT NULL,
            title varchar(255) DEFAULT NULL,
            description varchar(255) DEFAULT NULL,
            outer_url varchar(255) DEFAULT NULL,
            PRIMARY KEY (id)
            
            
        ) $charset_collate;";

    }

    if ( !empty($sql) ) {

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        dbDelta($sql);
        add_option("wnm_db_version", $wnm_db_version);
        
    }

}

add_action('admin_enqueue_scripts', 'callback_for_pfhb_cal_admin_plugin');
function callback_for_pfhb_cal_admin_plugin() {
    wp_register_style( 'a2a2_admin_styles', plugins_url('assets/admin/css/main.css', __FILE__), false, null, 'all' );
    wp_enqueue_style( 'a2a2_admin_styles' );
    
    // wp_register_style( 'a2a2_admin_styles', 'http://locationofcss.com/mycss.css' );
    // wp_enqueue_style( 'namespace' );
    // wp_enqueue_script( 'namespaceformyscript', 'http://locationofscript.com/myscript.js', array( 'jquery' ) );
}

add_action('wp_enqueue_scripts', 'callback_for_pfhb_cal_admin_plugin');
add_action('wp_enqueue_scripts', 'callback_for_pfhb_cal_front_plugin');
function callback_for_pfhb_cal_front_plugin() {
    // wp_register_style( 'a2a2_styles', MY_PLUGIN_PATH . 'assets/main.css' );
    // wp_enqueue_style( 'a2a2_styles' );
    wp_register_script( 'custom-script', plugins_url('wp_pfhb_scheduler/assets/js/main.js'), array('jquery'), false, true );
 
    // Localize the script with new data
    $script_data_array = array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'security' => wp_create_nonce( 'load_more_posts' ),
    );
    wp_localize_script( 'custom-script', 'a2a2', $script_data_array );
 
    // Enqueued script with localized data.
    wp_enqueue_script( 'custom-script' );
    
    // wp_enqueue_script( 'namespaceformyscript', 'http://locationofscript.com/myscript.js', array( 'jquery' ) );
}

include(MY_PLUGIN_PATH . "inc/plugin_functions.php");

add_shortcode('show_pfhb_calendar', 'pfhb_calendar');
function pfhb_calendar() {
    ob_start();
    ?>

            


            <div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:100vh;'>
        <div class="dhx_cal_navline">
            <div class="dhx_cal_prev_button">&nbsp;</div>
            <div class="dhx_cal_next_button">&nbsp;</div>
            <div class="dhx_cal_today_button"></div>
            <div class="dhx_cal_date"></div>
            <div class="dhx_cal_tab" data-tab="day"></div>
            <div class="dhx_cal_tab" data-tab="week" ></div>
            <div class="dhx_cal_tab" data-tab="month"></div>
        </div>
        <div class="dhx_cal_header"></div>
        <div class="dhx_cal_data"></div>       
   </div>

   <?php
    // return ob_get_clean();

    $content = ob_get_clean();

    // Output the content where the shortcode is placed
    echo $content;

    // Output the script just before the closing </body> tag
    add_action('wp_footer', function() {
        ?>
        <script src="https://cdn.dhtmlx.com/scheduler/edge/dhtmlxscheduler.js"></script>
        <link href="https://cdn.dhtmlx.com/scheduler/edge/dhtmlxscheduler_material.css" rel="stylesheet" type="text/css" charset="utf-8">
        <script type="text/javascript">
            document.addEventListener("DOMContentLoaded", function() {
                scheduler.config.active_link_view = "week";
           
                scheduler.attachEvent("onTemplatesReady", function(){
                    scheduler.templates.event_text=function(start,end,event){
                        return "<a href='"+event.outer_url+"'><b>" + event.text + "</b></a><br>";
                    }
                }); 
                scheduler.init("scheduler_here",new Date(),"month");
                scheduler.attachEvent("onClick", function (id, e){
                    alert('buu');
                    //any custom logic here
                    return true;
                });
                // scheduler.load("/wp-json/pfhb_calendar/v1/events");
                scheduler.parse(<?php echo get_events_json(); ?>, "json");
                scheduler.templates.event_text = function(start,end,ev){   return 'Subject: ' + ev.text + '';};
                scheduler.renderEvent = function(container, ev) {
                    var container_width = container.style.width;
                    var html = "<div class='dhx_event_move my_event_move' style='width:" +
                    + container_width + "'>asdasdadsas</div>";
                    
                    container.innerHTML = html;
                    return true; 
                }
                // scheduler.showLightbox(8);
            });
        </script>
        <?php
    },300);
}