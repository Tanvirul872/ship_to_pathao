<?php 




add_action( 'wp_ajax_save_shipping_cities', 'save_shipping_cities' );
add_action( 'wp_ajax_nopriv_save_shipping_cities', 'save_shipping_cities' );
function save_shipping_cities() {


  global $wpdb;
  $table_name = $wpdb->prefix . 'pathao_settings';
  // Retrieve data from the database
  $data = $wpdb->get_row("SELECT * FROM $table_name");


  $client_id = $data->api_client_id;
  $client_secret = $data->api_client_secret;
  $username = $data->user_name;
  $password = $data->user_password;
  $grant_type = $data->user_password;

  // api call for access token start
  // $client_id = 'WZdPN5zaKg' ; 
  // $client_secret = 'GuxjOKMRSNoj1AkMexLY6PMOR5jNEKkZxm04b9SQ' ; 
  // $username = 'bm175581@gmail.com' ; 
  // $password = 'anmtanvir872' ; 
  // $grant_type = 'anmtanvir872' ; 
  $headers = array(
      "accept" => "application/json",
      "content-Type" => "application/json",
  );

$data = array( 

    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "username" => $username,
    "password" => $password,
    "grant_type" => $grant_type, 

);


$url_for_token = "https://api-hermes.pathao.com/aladdin/api/v1/issue-token";  //uncomment later
$response = wp_remote_post( $url_for_token, array( 
    'method' => 'POST',
    'headers' => $headers,
    'body' => json_encode($data),
));

if ( is_wp_error( $response ) ) {
    

} else {
    $response_for_token = json_decode($response['body']); 
    $access_token = $response_for_token->access_token ;   
}

  // api call for access token end 

// api call for save cities start  
$authorization = "Bearer ".$access_token ; 
$headers_for_get_cities = array(
    "authorization" => $authorization,
    "content-Type" => "application/json",
    "accept" => "application/json", 
);

$url_for_get_cities = "https://api-hermes.pathao.com/aladdin/api/v1/countries/1/city-list";  //uncomment later
$response_for_get_cities= wp_remote_post( $url_for_get_cities, array( 
  'method' => 'GET',
  'timeout' => 45,
  'redirection' => 5,
  'httpversion' => '1.0',
  'blocking' => true,
  'headers' => $headers_for_get_cities,
  'cookies' => array()
));


if ( is_wp_error( $response_for_get_cities ) ) {
  
} else {

  $response_body_get_cities = json_decode($response_for_get_cities['body']);  
  $get_cities = $response_body_get_cities->data->data ; 

  global $wpdb;
  $table_name = $wpdb->prefix . 'pathao_city_list';
  $wpdb->query("TRUNCATE TABLE $table_name");

    // Insert data into the table
    foreach ($get_cities as $item) {
        $wpdb->insert($table_name, [
            'city_id' => $item->city_id,
            'city_name' => $item->city_name,
        ]);
    } 

    return wp_send_json_success('cities inserted successfully'); 

}

// api call for save cities  end 

wp_die() ;

}








// get stores 
add_action( 'wp_ajax_save_stores', 'save_stores' );
add_action( 'wp_ajax_nopriv_save_stores', 'save_stores' );
function save_stores() {

  // api call for access token start
  // $client_id = 'WZdPN5zaKg' ; 
  // $client_secret = 'GuxjOKMRSNoj1AkMexLY6PMOR5jNEKkZxm04b9SQ' ; 
  // $username = 'bm175581@gmail.com' ; 
  // $password = 'anmtanvir872' ; 
  // $grant_type = 'anmtanvir872' ; 
  
  global $wpdb;
  $table_name = $wpdb->prefix . 'pathao_settings';
  // Retrieve data from the database
  $data = $wpdb->get_row("SELECT * FROM $table_name");

  $client_id = $data->api_client_id;
  $client_secret = $data->api_client_secret;
  $username = $data->user_name;
  $password = $data->user_password;
  $grant_type = $data->user_password;

  $headers = array(
      "accept" => "application/json",
      "content-Type" => "application/json",
  );

$data = array( 

    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "username" => $username,
    "password" => $password,
    "grant_type" => $grant_type, 

);


$url_for_token = "https://api-hermes.pathao.com/aladdin/api/v1/issue-token";  //uncomment later
$response = wp_remote_post( $url_for_token, array( 
    'method' => 'POST',
    'headers' => $headers,
    'body' => json_encode($data),
));

if ( is_wp_error( $response ) ) {
    

} else {
    $response_for_token = json_decode($response['body']); 
    $access_token = $response_for_token->access_token ;   
}

  // api call for access token end 

// api call for save stores start  
$authorization = "Bearer ".$access_token ; 
$headers_for_get_stores = array(
    "authorization" => $authorization,
    "content-Type" => "application/json",
    "accept" => "application/json", 
);

$url_for_get_stores = "https://api-hermes.pathao.com/aladdin/api/v1/stores";  //uncomment later
$response_for_get_stores= wp_remote_post( $url_for_get_stores, array( 
  'method' => 'GET',
  'timeout' => 45,
  'redirection' => 5,
  'httpversion' => '1.0',
  'blocking' => true,
  'headers' => $headers_for_get_stores,
  'cookies' => array()
));


if ( is_wp_error( $response_for_get_stores ) ) {
  
} else {

  $response_body_get_stores = json_decode($response_for_get_stores['body']);  
  


  $get_stores= $response_body_get_stores->data->data ; 
  // print_r($get_stores) ; 

  global $wpdb;
  $table_name = $wpdb->prefix . 'pathao_store_list';
  $wpdb->query("TRUNCATE TABLE $table_name");



    // Insert data into the table
    foreach ($get_stores as $item) {
      $wpdb->insert($table_name, [
        'store_id' => $item->store_id,
        'city_id' => $item->city_id,
        'zone_id' => $item->zone_id,
        'hub_id' => $item->hub_id, 
        'is_active' => $item->is_active, 
        'store_name' => $item->store_name,
        'store_address' => $item->store_address,
    ]);

    } 
    return wp_send_json_success('Stores inserted successfully'); 
}



// api call for save stores  end 

wp_die() ;

}






add_action( 'wp_ajax_get_zones_by_city', 'get_zones_by_city' );
add_action( 'wp_ajax_nopriv_get_zones_by_city', 'get_zones_by_city' );
function get_zones_by_city() {

    $city_id = $_POST['formData'] ; 

  // api call for access token start
  // $client_id = 'WZdPN5zaKg' ; 
  // $client_secret = 'GuxjOKMRSNoj1AkMexLY6PMOR5jNEKkZxm04b9SQ' ; 
  // $username = 'bm175581@gmail.com' ; 
  // $password = 'anmtanvir872' ; 
  // $grant_type = 'anmtanvir872' ; 

  global $wpdb;
  $table_name = $wpdb->prefix . 'pathao_settings';
  // Retrieve data from the database
  $data = $wpdb->get_row("SELECT * FROM $table_name");

  $client_id = $data->api_client_id;
  $client_secret = $data->api_client_secret;
  $username = $data->user_name;
  $password = $data->user_password;
  $grant_type = $data->user_password;

  $headers = array(
      "accept" => "application/json",
      "content-Type" => "application/json",
  );

$data = array( 

    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "username" => $username,
    "password" => $password,
    "grant_type" => $grant_type, 

);


$url_for_token = "https://api-hermes.pathao.com/aladdin/api/v1/issue-token";  //uncomment later
$response = wp_remote_post( $url_for_token, array( 
    'method' => 'POST',
    'headers' => $headers,
    'body' => json_encode($data),
));

if ( is_wp_error( $response ) ) {
    

} else {
    $response_for_token = json_decode($response['body']); 
    $access_token = $response_for_token->access_token ;   
}

  // api call for access token end 

// api call for save cities start  
$authorization = "Bearer ".$access_token ; 
$headers_for_get_zones = array(
    "authorization" => $authorization,
    "content-Type" => "application/json",
    "accept" => "application/json", 
);

$url_for_get_zones = "https://api-hermes.pathao.com/aladdin/api/v1/cities/".$city_id."/zone-list";  //uncomment later
$response_for_get_zones= wp_remote_post( $url_for_get_zones, array( 
  'method' => 'GET',
  'timeout' => 45,
  'redirection' => 5,
  'httpversion' => '1.0',
  'blocking' => true,
  'headers' => $headers_for_get_zones,
  'cookies' => array()
));


if ( is_wp_error( $response_for_get_zones ) ) {
  
} else {

  $response_body_get_zones = json_decode($response_for_get_zones['body']);  
  $get_zones = $response_body_get_zones->data->data ; 

// print_r($get_zones) ; 

  
    return wp_send_json($get_zones); 

}

// api call for save cities  end 

wp_die() ;

}








// save settings   
add_action( 'wp_ajax_save_settings', 'save_settings' );
add_action( 'wp_ajax_nopriv_save_settings', 'save_settings' );
function save_settings() {

 

  $formdata = [];
  wp_parse_str($_POST['formData'], $formdata);
  
  global $wpdb;
  $table_name = $wpdb->prefix . 'pathao_settings';
  
  // Check if the data already exists in the table
  $data_exists = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
  
  if ($data_exists) {
      // Update the existing data
      $wpdb->update(
          $table_name,
          [
              'api_client_id' => $formdata['get_client_id'],
              'api_client_secret' => $formdata['get_client_secret'],
              'user_name' => $formdata['get_user_name'],
              'user_password' => $formdata['get_user_password'],
              'item_descripton' => $formdata['get_item_desc'],
              'special_information' => $formdata['get_special_info'],
          ],
          ['id' => 1] // Specify the condition to update the correct row(s)
      );
  } else {
      // Insert data into the table
      $wpdb->insert(
          $table_name,
          [
              'api_client_id' => $formdata['get_client_id'],
              'api_client_secret' => $formdata['get_client_secret'],
              'user_name' => $formdata['get_user_name'],
              'user_password' => $formdata['get_user_password'],
              'item_descripton' => $formdata['get_item_desc'],
              'special_information' => $formdata['get_special_info'],
          ]
      );
  }
  
  

    return wp_send_json_success('settings saved successfully'); 


wp_die() ;

}

?>