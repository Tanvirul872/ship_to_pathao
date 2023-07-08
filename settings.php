<?php
/*
Template Name: Settings
*/

wp_head(); 

?>



<div class="form_box">
 <label for="myCheckbox">Import Cities</label>
 <!-- <input type="checkbox" id="shipping_cities" name="shipping_cities"> -->
 <a href="#" id="shipping_cities" name="shipping_cities"> Click </a>
</div>


<div class="form_box">
 <label for="myCheckbox">Import Stores</label>
 <a href="#" id="get_stores" name="get_stores"> Click </a>
 <!-- <input type="checkbox" id="get_stores" name="get_stores"> -->
</div>



<form action="#">


<?php 
 
 global $wpdb;
$table_name = $wpdb->prefix . 'pathao_settings';

// Retrieve data from the database
$data = $wpdb->get_row("SELECT * FROM $table_name");


if ($data) {
    $api_client_id = $data->api_client_id;
    $api_client_secret = $data->api_client_secret;
    $user_name = $data->user_name;
    $user_password = $data->user_password;
    $item_description = $data->item_descripton;
    $special_information = $data->special_information;
} else {
    // Set default values if no data found
    $api_client_id = '';
    $api_client_secret = '';
    $item_description = '';
    $special_information = '';
    $user_name = '';
    $user_password = '';
}
?>

<div class="form_box">
<label for="myCheckbox">Api Client Id </label>
<input type="text" id="get_client_id" name="get_client_id" value="<?php echo esc_attr($api_client_id); ?>">

</div>

<div class="form_box">
<label for="myCheckbox">Api Client Secret </label>
<input type="text" id="get_client_secret" name="get_client_secret" value="<?php echo esc_attr($api_client_secret); ?>"> 
</div>

<div class="form_box">
<label for="myCheckbox"> User Name </label>
<input type="text" id="get_user_name" name="get_user_name" value="<?php echo esc_attr($user_name); ?>"> 
</div>

<div class="form_box">
<label for="myCheckbox"> User Passw6rd </label>
<input type="text" id="get_user_password" name="get_user_password" value="<?php echo esc_attr($user_password); ?>"> 
</div>

<div class="form_box">
<label for="myCheckbox"> Item Descripton </label>
<input type="text" id="get_item_desc" name="get_item_desc" value="<?php echo esc_attr($item_description); ?>">
</div>

<div class="form_box">
<label for="myCheckbox"> Special Information </label>
<input type="text" id="get_special_info" name="get_special_info" value="<?php echo esc_attr($special_information); ?>">
</div>



<div class="form_box">
<a class="save_pathao_settings"> save </a>
</div>


</form> 





<?php wp_footer(); ?>