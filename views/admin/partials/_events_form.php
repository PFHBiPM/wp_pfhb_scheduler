<div id="event-form">
    <h2>Dodaj/Edytuj wydarzenie</h2>
    <a href="admin.php?page=wp_pfhb_scheduler">Wszystkie wydarzenia</a>
<form method="post" action="<?php echo get_site_url() . "/wp-admin/admin.php?page=wp_pfhb_scheduler"; ?>">

<input type="hidden" name="event_id" value="<?php echo $event_id; ?>" />
<input type="hidden" name="form" value="event_form" />
<label>Początek wydarzenia: 
<input type="datetime-local" name="start_date" value="<?php echo $start_date; ?>" required="required">
</label>
<label>Koniec wydarzenia: 
<input type="datetime-local" name="end_date" value="<?php echo $end_date; ?>" required="required">
</label>
<label>Tytuł: 
<input type="text" name="title"  value="<?php echo $title; ?>" required="required">
</label>
<label>Opis: 
<textarea  name="description"><?php echo $description; ?></textarea>
</label>
<label>Adres odnośnika: 
<input placeholder="https://example.com" pattern="https://.*" name="outer_url"  value="<?php echo $outer_url; ?>">
</label>
<input type="submit" value="Dodaj/aktualizuj wydarzenie">
</form>
</div>