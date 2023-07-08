<?php
/*
 * Plugin Name: Woocommerce Order Ship to Pathao
 * Description: Syncing all order data from woocommerce to pathao.
 * Version: 1.0
 * Author: Tanvirul karim
 * Author URI: bongotheme.com
 */

 include('ajax-actions.php');


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


  add_submenu_page(
    'wc-order-data-table',
    'Settings',
    'Settings',
    'manage_options',
    'pathao-shipment-settings',
    'pathao_shipment_settings',
  );

}
add_action( 'admin_menu', 'wc_order_data_table_page' );



function pathao_shipment_settings(){
   include 'settings.php' ; 
}

function wc_order_data_table_content() {

  wp_head(); 

  if ( ! current_user_can( 'manage_options' ) ) {
    return;
  }
  
  $paged  =  $_GET['paged'] ? $_GET['paged'] : 1 ; 
  $order_status = $_GET['order_status'] ;  


  if($order_status){

    $args = array(
      'post_type' => 'shop_order',
      'post_status' => $order_status,
      'posts_per_page' => 20,
      'paged' =>  $paged ,
  );
  }else{

    $args = array(
      'post_type' => 'shop_order',
      'post_status' => array_keys( wc_get_order_statuses() ),
      'posts_per_page' => 20,
      'paged' =>  $paged ,
    );

  }

  
// Get all the orders
$orders = new WP_Query( $args );

echo '<div class="wrap">';
echo '<h1>Order Data Table</h1>';
?>


<select name="order_status" class="select_order_status">
  <?php
  $order_statuses = wc_get_order_statuses();
  echo '<option value="#"> Select Status </option>';
  foreach ($order_statuses as $status => $label) { ?> 
    <option value="<?php echo esc_attr($status) ; ?>" <?php if($_GET['order_status']== $status){ echo 'selected' ; }?>> <?php echo esc_html($label);  ?> </option>
    <?php
    
  }

  $dynamic_url = add_query_arg( 'page', 'wc-order-data-table', admin_url( 'admin.php' ) );
  ?>
</select> 
<a  href="<?php echo $dynamic_url; ?>" class="reset_button"> Reset </a>




<?php
echo '<table class="wp-list-table widefat fixed striped posts WP_List_Table show_preloader">';
echo '<thead>';
echo '<tr>';
echo '<th scope="col">Order ID</th>';
echo '<th scope="col">Customer name</th>';
echo '<th scope="col">Customer Address</th>';
echo '<th scope="col">Select City/Zone/Area</th>';
echo '<th scope="col">Select Store</th>';
echo '<th scope="col">Customer Phone</th>';
echo '<th scope="col">Customer Cod</th>';
echo '<th scope="col">Order Status</th>';
echo '<th scope="col"> Consignment Id </th>';
echo '<th scope="col">Select</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Loop through each order and get the order ID
while ( $orders->have_posts() ) {
    $orders->the_post();

    // Do something with each post
    $order_id = get_the_ID();
    $order = wc_get_order( $order_id );

    // Get the email address associated with the order
    $email = $order->get_billing_email();

    // Get the total order amount
    $order_total = (int) $order->get_total();
    $order_status =  $order->get_status();
    global $wpdb;
    $table_name = $wpdb->prefix . 'pathao_shipping';
    $pathao_data = $wpdb->get_row("SELECT * FROM $table_name WHERE  order_id=$order_id");
    $pathao_delivery_status = $pathao_data->consignment_id ; 

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
    echo '<td class="get_city">';
    ?> 

    
<form action="#" id="select_city"  class="select_city_dropdown">
<label for="get_district">Choose a city:</label> 
<select id="get_district" class="get_district_class" name="district">
  <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'pathao_city_list';
    $results = $wpdb->get_results("SELECT city_id, city_name FROM $table_name"); 
    // Loop through the results and generate options
    echo "<option value=''>Select a District</option>"; 
    foreach ($results as $row) {
        $city_id = $row->city_id;
        $city_name = $row->city_name;
        echo "<option value='$city_id'>$city_name</option>";
    }
    ?>
</select>

<label for="get_zone">Choose a zone:</label> 
<select id="get_zone" class="get_zone_class" name="zone">
 <option value=''>Select a Zone</option>
</select> 
</form>
<?php

    echo '</td>';
    echo '<td class="get_store">' 
    ?> 
    
    <form action="#" id="select_store"  class="select_store_dropdown">
    <label for="get_store">Choose a store:</label> 
    <select id="get_store" class="get_store_class" name="store">
    <?php
    global $wpdb;
    $table_name = $wpdb->prefix . 'pathao_store_list';
    $results = $wpdb->get_results("SELECT store_id, store_name FROM $table_name"); 
    // Loop through the results and generate options
    echo "<option value=''>Select a store</option>"; 
    foreach ($results as $row) {
        $store_id = $row->store_id;
        $store_name = $row->store_name;
        echo "<option value='$store_id'>$store_name</option>";
    }
    ?>
    </select>
    </form>
    <?php
    echo '</td>';

    echo '<td class="get_phone" get_phone="'.$billing_phone.'">' . $billing_phone.  '</td>';
    echo '<td class="get_total" get_total="'.$order_total.'" >' . $order_total . '</td>';
    echo '<td class="get_status" get_status="'.$order_status.'" >' . $order_status . '</td>';
    echo '<td class="get_pathao_status" get_pathao_status="'.$pathao_delivery_status.'" ><p class="get_pathao_consingment_id">' . $pathao_delivery_status . '</p></td>';
    

    echo '<td class="push_checkbox">';
    global $wpdb;
    $table_name = $wpdb->prefix . 'pathao_shipping';
    $order_ids = $wpdb->get_col("SELECT order_id FROM $table_name");

    if (in_array($order_id, $order_ids)) {
      echo '<a  class="push_order_synced" > Order Synced <a>';
    } else {
        echo '<a  class="push_order push_order_class"> Push order <a>';
        echo '<a  class="push_order_success" style="display:none"> Order Synced <a>';
    }
    echo '</td>';
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';

// Pagination
echo '<div class="pagination">'; 


// global $orders;

$pagination_args = array(
    'base' => add_query_arg('paged', '%#%'),
    'format' => '',
    'current' => max(1, $_GET['paged']),
    'total' => $orders->max_num_pages,
    'add_args' => array(
        'order_status' => isset($_GET['order_status']) ? $_GET['order_status'] : ''
    )
);

echo paginate_links($pagination_args);



// echo paginate_links(array( 


//     'base' => str_replace(999999999, '%#%', esc_url(get_pagenum_link(999999999))),
//     'format' => '?paged=',
//     'current' => max(1, $_GET['paged']),
//     'total' => $orders->max_num_pages,
//     'add_args' => array(
//       'order_status' => isset($_GET['order_status']) ? $_GET['order_status'] : ''
//      ) 
// )); 


echo '</div>';
wp_reset_postdata();

// loop end  

next_posts_link( 'Older Entries' );
previous_posts_link( 'Next Entries &raquo;' ); 

echo '</tbody>';
echo '</table>';
echo '</div>';

wp_footer(); 

}





// Create the database table on plugin activation
register_activation_hook(__FILE__, 'create_five_fields_table');
function create_five_fields_table() {
  global $wpdb;

  $table_name = $wpdb->prefix . 'pathao_shipping';
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    order_id bigint(20) NOT NULL,
    consignment_id varchar(255) NOT NULL,
    delivery_fee varchar(255) NOT NULL,
    PRIMARY KEY  (id)
  ) $charset_collate;";


  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);

}



// Create the database table on plugin activation 
register_activation_hook(__FILE__, 'save_setttings');
function save_setttings() {
  global $wpdb;

  $table_name = $wpdb->prefix . 'pathao_settings';
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    api_client_id varchar(255) NOT NULL,
    api_client_secret varchar(255) NOT NULL,
    user_name varchar(255) NOT NULL,
    user_password varchar(255) NOT NULL,
    item_descripton  varchar(255) NOT NULL,
    special_information  varchar(255) NOT NULL, 
    PRIMARY KEY  (id)

  ) $charset_collate;";
  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);

}


// Create the database table on plugin activation 
register_activation_hook(__FILE__, 'save_city_list');
function save_city_list() {
  global $wpdb;

  $table_name = $wpdb->prefix . 'pathao_city_list';
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    city_id bigint(20) NOT NULL,
    city_name varchar(255) NOT NULL,
    PRIMARY KEY  (id)
  ) $charset_collate;";


  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);

}


// Create the database table on plugin activation 
register_activation_hook(__FILE__, 'create_db_for_stores');
function create_db_for_stores() {
  global $wpdb;

  $table_name = $wpdb->prefix . 'pathao_store_list';
  $charset_collate = $wpdb->get_charset_collate();

  $sql = "CREATE TABLE $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    store_id bigint(20) NOT NULL,
    city_id bigint(20) NOT NULL,
    zone_id bigint(20) NOT NULL,
    hub_id bigint(20) NOT NULL,
    is_active bigint(20) NOT NULL,
    store_name varchar(255) NOT NULL,
    store_address varchar(255) NOT NULL,

    PRIMARY KEY  (id)
  ) $charset_collate;";


  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
  dbDelta($sql);

}


function enqueue_select2_jquery() {
  wp_register_style( 'select2css', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.css', false, '1.0', 'all' );
  wp_register_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.js', array( 'jquery' ), '1.0', true );
  wp_enqueue_style( 'select2css' );
  wp_enqueue_script( 'select2' );
}
add_action( 'admin_enqueue_scripts', 'enqueue_select2_jquery' );





 ?> 






