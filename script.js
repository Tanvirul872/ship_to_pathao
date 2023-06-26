jQuery(document).ready(function($){

    
$('.push_order').change(function (event) {
    event.preventDefault();
  
    alert('push_order') ;


 
    
   


    var get_order_id = $(this).closest("tr").find(".get_order_id").attr("id");
    var get_name = $(this).closest("tr").find(".get_name").attr("get_name");
    var get_address = $(this).closest("tr").find(".get_address").attr("get_address");
    var get_phone = $(this).closest("tr").find(".get_phone").attr("get_phone");
    var get_email = $(this).closest("tr").find(".get_email").attr("get_email");
    var get_total = $(this).closest("tr").find(".get_total").attr("get_total");

    var formData = 
                'get_order_id=' + get_order_id+
                '&get_name=' + get_name+
                '&get_address=' + get_address+
                '&get_phone=' + get_phone+
                '&get_email=' + get_email+
                '&get_total=' + get_total;

    var ajax_url = plugin_ajax_object.ajax_url;
    var data = {
        'action': 'pushapi_to_steadfast',
        'formData': formData
  
    };
   
  
    $.ajax({
        url: ajax_url,
        type: 'post',
        data: data,
        success: function(response){
            
            if(response.data==400){
                alert('Already added to steadfast dashboard before') ; 
               
                $(this).closest("tr").find(".push_checkbox").addClass("d-none")
             }else if(response.data==200){
                alert('Added to steadfast dashboard') ; 
                $(this).closest("tr").find(".push_order").addClass("d-none")
             }

           
          // console.log(coupon_code);
        
            // alert('successfully store data') ;
        }
    });
  
  });


});
    


  