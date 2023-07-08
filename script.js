jQuery(document).ready(function($){

    // select2 function 
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();y
    });

//  import cities 
 $('#shipping_cities').on('click', function() {
          var ajax_url = plugin_ajax_object.ajax_url;
          var data = {
              'action': 'save_shipping_cities',
              'formData': 1
          };
         
          $.ajax({
              url: ajax_url,
              type: 'post',
              data: data,
              success: function(response){
                  alert(response.data) ;

              }
          });

 });
 


 
 //  import stores 
 $('#get_stores').on('click', function() {
  var ajax_url = plugin_ajax_object.ajax_url;
  var data = {
      'action': 'save_stores',
      'formData': 1
  };
 
  $.ajax({
      url: ajax_url,
      type: 'post',
      data: data,
      success: function(response){
          alert(response.data) ;

      }
  });


});



 //  save settings  
 $('.save_pathao_settings').on('click', function() { 



  get_client_id = $('#get_client_id').val() ;
  get_client_secret = $('#get_client_secret').val() ;
  get_item_desc = $('#get_item_desc').val() ;
  get_special_info = $('#get_special_info').val() ;
  get_user_password = $('#get_user_password').val() ;
  get_user_name = $('#get_user_name').val() ;
  get_special_info = $('#get_special_info').val() ;


  var formData = 
  'get_client_id=' + get_client_id+
  '&get_client_secret=' + get_client_secret+
  '&get_user_name=' + get_user_name+
  '&get_user_password=' + get_user_password+
  '&get_item_desc=' + get_item_desc+
  '&get_special_info=' + get_special_info;



  alert(get_client_id) ; 

  var ajax_url = plugin_ajax_object.ajax_url;
  var data = {
      'action': 'save_settings',
      'formData': formData,
  };
 
  $.ajax({
      url: ajax_url,
      type: 'post',
      data: data,
      success: function(response){
          alert(response.data) ;

      }
  });


});




 $(document).on('change', '.get_district_class', function() {
    var selectedCity = $(this).val();
    alert("Selected option: " + selectedCity);
  
    var ajax_url = plugin_ajax_object.ajax_url;
    var data = {
      'action': 'get_zones_by_city',
      'formData': selectedCity
    };
  
    var selectElement = $(this).closest('tr').find('.get_zone_class'); // Find the select element within the same table row
  
    $.ajax({
      url: ajax_url,
      type: 'post',
      data: data,
      success: function(response) {
        console.log(response);
        var get_zones = response;
  
        // Clear any existing options
        selectElement.empty();
  
        $.each(get_zones, function(index, zone) {
          // Create a new option element
          var option = $('<option>');
  
          // Set the value and text of the option
          option.val(zone.zone_id);
          option.text(zone.zone_name);
  
          // Append the option to the select element
          selectElement.append(option);
        });
      },
      error: function(xhr, textStatus, error) {
        console.log(xhr.statusText);
        console.log(textStatus);
        console.log(error);
      }
    });
  });






// order sorting by order status  
 $(document).on('change', '.select_order_status', function() {
    var selectedStatus = $(this).val();
    alert("Selected order: " + selectedStatus);
    var currentURL = window.location.href; // Get the current URL
    // Remove any existing order_status parameter from the URL
    var updatedURL = currentURL.replace(/([&?])order_status=[^&]+/i, '$1');
    // Append the selected order_status parameter to the URL
    updatedURL += (updatedURL.indexOf('?') >= 0 ? '&' : '?') + 'order_status=' + selectedStatus;
    // Redirect to the updated URL
    window.location.href = updatedURL;

  });
  
 

  
//push order 
$('.push_order').click(function (event) {
    event.preventDefault();
  
    var get_order_id = $(this).closest("tr").find(".get_order_id").attr("id");
    var get_name = $(this).closest("tr").find(".get_name").attr("get_name");
    var get_address = $(this).closest("tr").find(".get_address").attr("get_address");
    var get_phone = $(this).closest("tr").find(".get_phone").attr("get_phone");
    var get_email = $(this).closest("tr").find(".get_email").attr("get_email");
    var get_total = $(this).closest("tr").find(".get_total").attr("get_total");
    var get_city = $(this).closest("tr").find(".get_district_class").val();
    var get_zone = $(this).closest("tr").find(".get_zone_class").val();
    var get_store = $(this).closest("tr").find(".get_store_class").val();


    if (get_city === '') {
      alert('Please select a district/city');
    }else if(get_zone === ''){
      alert('Please select a zone');
    }else if(get_store === ''){
      alert('Please select a store');
    }else if (get_address.length < 10) {
      alert('The address should be at least 10 characters long.Please edit your address.');
    }
    
    //show preloader 
    if (get_city && get_zone && get_store ) {
      $('.show_preloader').addClass('processing-loader');
    } 

    var formData = 
                'get_order_id=' + get_order_id+
                '&get_name=' + get_name+
                '&get_address=' + get_address+
                '&get_phone=' + get_phone+
                '&get_email=' + get_email+
                '&get_total=' + get_total+
                '&get_city=' + get_city+
                '&get_zone=' + get_zone+
                '&get_store=' + get_store;

    var ajax_url = plugin_ajax_object.ajax_url;
    var data = {
        'action': 'pushapi_to_pathao',
        'formData': formData
  
    };
    var $tr = $(this).closest("tr");
    var $pushOrderClassElements = $tr.find(".push_order_class");
    var $pushOrderSuccess = $tr.find(".push_order_success");

    $.ajax({
        url: ajax_url,
        type: 'post',
        data: data,
        success: function(response){
        
         if(response.status==200){
            alert(response.message) ;
            $pushOrderClassElements.css("display", "none"); 
            $pushOrderSuccess.css("display", "block"); 
            $('.show_preloader').removeClass('processing-loader'); //remove preloader 
         }else if(response.status==422){
            //  alert(response.message) ;
         }


        }
    });
  
  });


});
    


  