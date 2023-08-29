<?php
function get_events_json(){
    global $wpdb;
    $table = $wpdb->prefix.'pfhb_events';
    $events =$wpdb->get_results("select id, start_date, end_date, title AS text, description, outer_url 
    
    from $table 
    
    order by id ASC");
    // return $events;
    return json_encode($events);
}
function get_events_xml() {
    global $wpdb;
    $table = $wpdb->prefix . 'pfhb_events';
    $events = $wpdb->get_results("SELECT id, start_date, end_date, title, description, outer_url
                                  FROM $table
                                  ORDER BY id ASC");

    // Create XML string
    $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><events></events>');

    foreach ($events as $event) {
        $eventElement = $xml->addChild('event');
        $eventElement->addChild('id', $event->id);
        $eventElement->addChild('start_date', $event->start_date);
        $eventElement->addChild('end_date', $event->end_date);
        $eventElement->addChild('title', $event->title);
        $eventElement->addChild('description', $event->description);
        $eventElement->addChild('outer_url', $event->outer_url);
    }
    
    $response = new WP_REST_Response($xml->asXML(), 200);
    $response->header('Content-Type', 'application/xml');
    return $response;;
}
function register_custom_api_endpoint() {
    register_rest_route('pfhb_calendar/v1', 'events', array(
        'methods' => 'GET',
        'callback' => 'get_events_xml',
        // 'callback' => 'get_events_json_callback',
        'permission_callback' => '__return_true',
    ));
}
// function register_custom_api_endpoint() {
//     register_rest_route('my/v1', 'events', array(
//         'methods' => 'GET',
//         'callback' => 'get_events_json_callback',
//         'permission_callback' => '__return_true', // Allow access to all users
//     ));
// }
add_action('rest_api_init', 'register_custom_api_endpoint');
// function get_events_json_callback($request) {
//     $events_json = get_events_json();
//     return new WP_REST_Response($events_json, 200, array('Content-Type' => 'application/json'));
//     // return new WP_REST_Response($xml, 200, array('Content-Type' => 'application/xml'));
// }
// function get_events_json_callback($request) {
//     $xml = get_events_xml();
//     // return new WP_REST_Response($events_json, 200, array('Content-Type' => 'application/json'));
//     return new WP_REST_Response($xml, 200, array('Content-Type' => 'application/xml'));
// }

function get_events(){
    global $wpdb;
    $table = $wpdb->prefix.'pfhb_events';
    $events =$wpdb->get_results("select *
    
    from $table 
    
    order by id ASC");
    return $events;
}
function get_event($event_id){
    global $wpdb;
    $table = $wpdb->prefix.'pfhb_events';
   
    
    $event =$wpdb->get_row("select * from $table where id=".$event_id." ");
    return $event;
}
function insert_event($start_date,$end_date,$title,$description,$outer_url){
    global $wpdb;
    $format = array('%s');
    $table = $wpdb->prefix.'pfhb_events';
    // echo "w funkcji insert end_date ".$end_date;
    $data = array('start_date'=> $start_date,
    'end_date'=>$end_date,
                'title'=>$title, 
                'description'=>$description,
                'outer_url' => $outer_url
        );
        //  var_dump($data);
    $last_insert= $wpdb->insert($table,$data,$format);
    if(!$last_insert){
        return false;
    }
    else{
        return $wpdb->insert_id;
    }
}

function remove_event($event_id){
    global $wpdb;
    $table = $wpdb->prefix.'pfhb_events';
    
    return $wpdb->delete(
        $table,      // table name with dynamic prefix
         array( 'id' => $event_id )                            // make sure the id format
     );
   
}

function update_event($event_id,$start_date,$end_date,$title,$description,$outer_url){
    global $wpdb;
    $table = $wpdb->prefix.'pfhb_events';
    // $format = array('%s','%d');
    $where=['id'=>$event_id];
    $data = array('start_date'=> $start_date,
        'end_date'=>$end_date,
        'title'=>$title, 
        'description'=>$description,
        'outer_url' => $outer_url
    );
    $last_insert= $wpdb->update($table,$data,$where);
    if(!$last_insert){
        return false;
    }
    return $event_id;
}

function get__show_events(){
    global $wpdb;
    $table = $wpdb->prefix.'pfhb_events';
    $query ="select *
    
    from $table 
    
    ";
    $total_query = "SELECT COUNT(1) FROM (${query}) AS combined_table";
    $total = $wpdb->get_var( $total_query );
    $items_per_page = 10;
    $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;
    $offset = ( $page * $items_per_page ) - $items_per_page;
    $asc_desc =(empty($_GET['asc_desc'])) ? " ASC " : $_GET['asc_desc'];
    $order =(empty($_GET['order'])) ? "$table.id ASC" : "$table.$order $asc_desc";
    $all_events = $wpdb->get_results( $query . " ORDER BY $order LIMIT ${offset}, ${items_per_page}" );

    if(!empty($all_events)):
        echo "<h2>Lista wydarzeń</h2>";
        echo '<a href="admin.php?page=wp_pfhb_scheduler&act=add_event">Dodaj wydarzenie</a>';
        echo "<ul class='data-list events-list'>";
        echo "<li>";
        echo "<div>Tytuł</div>";
        echo "<div>Czas wydarzenia</div>";
        echo "<div>Odnośnik</div>";
        echo "<div>Opis</div>";
        
        echo "<div></div>";
        echo "</li>";
        foreach($all_events as $item):
            echo "<li >";
                echo "<div>".$item->title."</div>";
                echo "<div>".$item->start_date." - ".$item->end_date."</div>";
                echo "<div>".$item->outer_url."</div>";  
                echo "<div>".$item->description."</div>";  
                echo "<div>";
                echo "<a href='admin.php?page=wp_pfhb_scheduler&act=edit_event&event_id=".$item->id."#edit-form'>Edytuj</a>";
                
                echo "<a href='admin.php?page=wp_pfhb_scheduler&act=remove_event&event_id=".$item->id."'>USUŃ Wydarzenie</a>";
                echo "</div>";
            echo "</li>";
        endforeach;
        echo "<ul>";
        echo paginate_links( array(
            'base' => add_query_arg( 'cpage', '%#%' ),
            'format' => '',
            'prev_text' => __('&laquo;'),
            'next_text' => __('&raquo;'),
            'total' => ceil($total / $items_per_page),
            'current' => $page
        ));
    else:
        echo '<a href="admin.php?page=wp_pfhb_scheduler&act=add_event">Dodaj wydarzenie</a>';
    endif;
}


function validate_date_range($start_date, $end_date) {
    return strtotime($start_date) <= strtotime($end_date);
}