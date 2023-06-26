<?php
/*
 * Plugin Name: Woocommerce Order Ship to Pathao
 * Description: Displays all the order data for Woocommerce orders in the form of a table
 * Version: 1.0
 * Author: OpenAI
 * Author URI: https://openai.com
 */


/* Include CSS and Script */
add_action('wp_enqueue_scripts','plugin_css_jsscripts');
function plugin_css_jsscripts() {
    // CSS
    wp_enqueue_style( 'style-css', plugins_url( '/style.css', __FILE__ ));

    // JavaScript
    wp_enqueue_script( 'script-js', plugins_url( '/script.js', __FILE__ ),array('jquery'));

    // Pass ajax_url to script.js
    wp_localize_script( 'script-js', 'plugin_ajax_object',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}


function wc_order_data_table_page() {
  add_menu_page(
    'Order Data Table',
    'Ship to Pathao',
    'manage_options',
    'wc-order-data-table',
    'wc_order_data_table_content',
    'dashicons-admin-generic',
    60
  );
}
add_action( 'admin_menu', 'wc_order_data_table_page' );

function wc_order_data_table_content() {

  wp_head(); 

  if ( ! current_user_can( 'manage_options' ) ) {
    return;
  }
  

$args = array(
    'post_type' => 'shop_order',
    'post_status' => array_keys( wc_get_order_statuses() ),
    'posts_per_page' => -1,
  
);


// Get all the orders
$orders = get_posts( $args );

  echo '<div class="wrap">';
  echo '<h1>Order Data Table</h1>';
  echo '<table class="wp-list-table widefat fixed striped posts WP_List_Table">';
  echo '<thead>';
  echo '<tr>';
  echo '<th scope="col">Order ID</th>';
  echo '<th scope="col">Customer name</th>';
  echo '<th scope="col">Customer Address</th>';
  echo '<th scope="col">Customer Phone</th>';
  echo '<th scope="col">Customer Email</th>';
  echo '<th scope="col">Customer Cod </th>';
  echo '<th scope="col"> Select </th>';
  echo '</tr>';
  echo '</thead>';
  echo '<tbody>';

// Loop through each order and get the order ID
foreach ( $orders as $order_get_id ) {

  // foreach ( $orders as $order_get_id ) :
    // Do something with each post


    $order_id = $order_get_id->ID;

// Get the order object for a given order ID
$order = wc_get_order( $order_id );

// Get the email address associated with the order
$email = $order->get_billing_email();

// Get the total order amount
$order_total = $order->get_total();

// Get the billing address
$billing_first_name = $order->get_billing_first_name();
$billing_last_name = $order->get_billing_last_name();
$billing_address_1 = $order->get_billing_address_1();
$billing_address_2 = $order->get_billing_address_2();
$billing_city = $order->get_billing_city();
$billing_state = $order->get_billing_state();
$billing_postcode = $order->get_billing_postcode();
$billing_country = $order->get_billing_country();

// Get the shipping address
$shipping_first_name = $order->get_shipping_first_name();
$shipping_last_name = $order->get_shipping_last_name();
$shipping_address_1 = $order->get_shipping_address_1();
$shipping_address_2 = $order->get_shipping_address_2();
$shipping_city = $order->get_shipping_city();
$shipping_state = $order->get_shipping_state();
$shipping_postcode = $order->get_shipping_postcode();
$shipping_country = $order->get_shipping_country();

// Get the customer phone number
$billing_phone = $order->get_billing_phone();

// Get the customer email address
$billing_email = $order->get_billing_email();

    echo '<tr>';
    echo '<td class="get_order_id" id="'.$order_id.'">' . $order_id. '</td>';
    echo '<td class="get_name" get_name="'.$billing_first_name.' '.$billing_last_name .'">' . $billing_first_name.' '.$billing_last_name . '</td>';
    echo '<td class="get_address" get_address="'.$billing_address_1.$billing_address_2.'">' . $billing_address_1.$billing_address_2. '</td>';
    echo '<td class="get_phone" get_phone="'.$billing_phone.'">' . $billing_phone.  '</td>';
    echo '<td class="get_email" get_email="'.$billing_email.'">' . $billing_email . '</td>';
  	echo '<td class="get_total" get_total="'.$order_total.'" >' . $order_total . '</td>';
  

    global $wpdb;
    $table_name = $wpdb->prefix . 'steadfast_shipping';
    $order_ids = $wpdb->get_col("SELECT order_id FROM $table_name");
  
    if (in_array($order_id, $order_ids)) {
      echo '<td class="push_checkbox"> <input type="checkbox"  disabled checked class="push_order"> </td>';
    } else {
      echo '<td class="push_checkbox"> <input type="checkbox" class="push_order" > </td>';
     }
	  echo '</tr>';

}


next_posts_link( 'Older Entries' );
previous_posts_link( 'Next Entries &raquo;' ); 




echo '</tbody>';
echo '</table>';
echo '</div>';

wp_footer(); 

}



add_action( 'wp_ajax_pushapi_to_steadfast', 'pushapi_to_steadfast' );
add_action( 'wp_ajax_nopriv_pushapi_to_steadfast', 'pushapi_to_steadfast' );
function pushapi_to_steadfast() {

    $formdata = [];
    wp_parse_str($_POST['formData'], $formdata);
    
    // print_r($formdata) ; 
    global $wpdb;
    $table_name = $wpdb->prefix . 'steadfast_shipping';
    $data = array(
      'order_id' => $formdata['get_order_id'],
     
  );
  $wpdb->insert( $table_name, $data );


  $api_key = '8itkm2wuwftjvpmwdggps7bspg5zrife' ; 
  $secret_key = 'dpgvyrbhgjr8uks8f3lraxp6' ; 

  

$headers = array(
    "Content-Type" => "application/json",
    "Api-Key" => $api_key,
    "Secret-Key" => $secret_key,
);

$data = array(
  'invoice' => $formdata['get_order_id'],
  'recipient_name' => $formdata['get_name'],
  'recipient_phone' => $formdata['get_phone'],
  'recipient_address' => $formdata['get_address'],
  'cod_amount' => $formdata['get_total'],
  'note' => 'note something',
);

$url = "https://portal.steadfast.com.bd/api/v1/create_order";  //uncomment later

$response = wp_remote_post( $url, array(
    'method' => 'POST',
    'timeout' => 45,
    'redirection' => 5,
    'httpversion' => '1.0',
    'blocking' => true,
    'headers' => $headers,
    'body' => json_encode($data),
    'cookies' => array()
));

if ( is_wp_error( $response ) ) {
    
} else {
    // $status_code = wp_remote_retrieve_response_code( $response );
    // print_r( $status_code) ; 
    $status = json_decode($response['body'])->status; 
    if ($status == 200) {
      return wp_send_json_success($status); 
    } else {
      return wp_send_json_success($status); 
    }
}


}


// Create the database table on plugin activation
register_activation_hook(__FILE__, 'create_five_fields_table');
function create_five_fields_table() {
  global $wpdb;

  $table_name = $wpdb->prefix . 'steadfast_shipping';
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    order_id bigint(20) NOT NULL,
    PRIMARY KEY  (id)
  ) $charset_collate;";


  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);

}


 ?> 





