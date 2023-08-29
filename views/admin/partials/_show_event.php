<?php

$event=get_event($event_id);

echo "<div class='show-zgloszenie'>";
    echo "<h1 class='center'>";
        echo "<strong>Kalendarium</strong></h1><h2 class='center'><br/>Wydarzenie NR: ".$event->id;
    echo "</h1>";


    
    echo "<div class='dane'>";
        echo "<h3>";
        echo $event->title;
        echo "</h3>";
        echo "<div>".$event->start_date." - ".$event->end_date."</div>";
    echo "</div>";

        
    echo "<div class=''>";
    echo $event->description;
    echo "</div>";
    echo "<div class=''>";
    echo $event->outer_url;
    echo "</div>";

echo "<a href='admin.php?page=wp_pfhb_scheduler'>Wydarzenia</a>";

echo "</div>";

