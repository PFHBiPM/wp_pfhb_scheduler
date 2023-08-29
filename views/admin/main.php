<?php

if (isset($_GET['error'])) {
    $error_message = urldecode($_GET['error']);
    echo '<p class="error-message">' . esc_html($error_message) . '</p>';
}

if(isset($_GET['act']) && $_GET['act']=='add_event'):
    $event_id ="";
    $start_date="";
    $end_date="";
    $title="";
    $description="";
    $outer_url="";
    include(MY_PLUGIN_PATH . "views/admin/partials/_events_form.php");
elseif(isset($_GET['act']) && $_GET['act']=='edit_event'):
    if(isset($_GET['event_id']) && !empty($_GET['event_id'])):
        $event_id = $_GET['event_id'];
        $event=get_event($event_id);
        if(!empty($event)):
            // $start_date=date('Y-m-d', strtotime($event->start_date));
            // $end_date= date('Y-m-d', strtotime($event->end_date));
            $start_date=$event->start_date;
            $end_date= $event->end_date;
            $title=$event->title;
            $description=$event->description;
            $outer_url=$event->outer_url;

            include(MY_PLUGIN_PATH . "views/admin/partials/_events_form.php");
        else:
            echo "Brak wydarzenia o id: ".$event_id;
        endif;
    endif;
elseif(isset($_GET['act']) && $_GET['act']=='remove_event'):
    if(isset($_GET['event_id'])):
        echo remove_event($_GET['event_id']);
    
        get__show_events();
    endif;
elseif(isset($_GET['act']) && $_GET['act']=='event_json'):
    echo get_events_json();
elseif(isset($_POST['form']) && $_POST['form']=="event_form"):
    $start_date=sanitize_text_field($_POST['start_date']);
    $end_date=sanitize_text_field($_POST['end_date']);
    if (validate_date_range($start_date, $end_date)):
        $title=sanitize_text_field($_POST['title']);
        $description=sanitize_text_field($_POST['description']);
        $outer_url=sanitize_text_field($_POST['outer_url']);
        
        if(isset($_POST['event_id']) && !empty($_POST['event_id'])):
            $event_id=update_event($_POST['event_id'],$start_date,$end_date,$title,$description,$outer_url);
            if($event_id):
                echo "Zmieniono wydarzenie nr: ".$event_id;
                include(MY_PLUGIN_PATH . "views/admin/partials/_show_event.php");
            else:
                echo "Błąd, spróbuj ponownie";
            endif;
        else:
            $event_id=insert_event($start_date,$end_date,$title,$description,$outer_url);
            if($event_id):
                echo "Dodano wydarzenie nr: ".$event_id;
                include(MY_PLUGIN_PATH . "views/admin/partials/_show_event.php");
            else:
                echo "Błąd, spróbuj ponownie";
            endif;
        endif;
    else:
        $error_message="Data końcowa wydarzenia nie może być wcześniejsza niż początkowa!!";
        wp_safe_redirect(wp_get_referer() . (strpos(wp_get_referer(), '?') !== false ? '&' : '?') . 'error=' . urlencode($error_message));
        
        exit;
    endif;
else:
    get__show_events();
endif;