
// jQuery(document).ready(function($){


    $(document).ready(function() {
        alert('hello') ;
        $('.js-example-basic-multiple').select2();
    });
    
    
    
    $('.sync_order_api').click(function (event) {
    event.preventDefault();
    
    alert('hello') ;
    
    var ajax_url = plugin_ajax_object.ajax_url;
    
    var data = {
        'action': 'contactMailList',
        'formData': form
    
    };
    
    $.ajax({
        url: ajax_url,
        type: 'post',
        data: data,
        success: function(response){
           
            alert('success') ;
        }
    });
    
    })
    
    
    // });
    


