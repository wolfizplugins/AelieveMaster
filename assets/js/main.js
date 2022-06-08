jQuery(document).ready(function($) {
    $('.wlf_multi_select').select2();
});


// jQuery(document).ready(function($) {
//     $('.add_new').click(function(){
//         $('.latest').append('<br /><tr><td><p> Slogan Name </p></td></tr>');
//         return false;
//     });
// });

// jQuery(document).ready(function($) {
//     window.addEventListener('load',function($){
//         $('.wlf_cache_divs').each(function(){
//             // alert($(this).find('.type_select').val());
//             if($(this).find('.type_select').val()=='elementor_library'){
//                 $(this).find('.act_type_select').attr('disabled','disabled');
//                 var id = $(this).find('.act_wlf').attr('id');
//                 // console.log('#select2-'+id+'-results');
//                 // $('#select2-'+id+'-results').css('display','none'); 
//             }
//             // console.log("values");
//         });
//     });
// });


jQuery(document).ready(function($) {
    $(document).on('change','.wlf_multi_select',function(e){

        var val = $(this).attr('data-val');

        var id = $(this).attr('id');
        var selectedtrigger = $('.type_select :selected').val();
        var selectedtrigger2 = $(this).val();
        if(selectedtrigger == "elementor_library"){
            // $('#'+id).attr('multiple',false);
            // alert('#'+id);
            // $('#wlf_act_'+val).attr('disabled','disabled');
            // alert(selectedtrigger2);
            var dat = $('#'+id+' :selected').val();
            // alert($('#'+id+' :selected').val());
            $('#wlf_act_'+val)
            .val('page')
            .trigger('change');
            // $('#wlf_act_'+val).val('page');   
            console.log('#wlf_act_'+val);

            jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data:{
                    action: 'wlf_data_for_action',
                    selectedtrigger2: dat,
            },
            success: function (response) {
                console.log(response);
                       var resp = jQuery.parseJSON( response );
                       // $('#act_'+id).select2("val", ["198", "195", "196"]);
                       // console.log(resp);
                       if(resp.sel_data){
                            console.log(resp.sel_data);
                            // $('#act_'+id)    
                            // $('#act_'+id).attr('disabled','disabled');
                            $('#act_'+id).select2(); 
                            // alert(id); 
                            // console.log('#select2-act_'+id+'-results');
                            
                            $('#act_'+id).val(resp.sel_data).trigger('change');
                            // $('#select2-'+id+'-results').css('display','none'); 
                       }
                       
                    // var result = jQuery.parseJSON( response );
                    // $('#act_wlf_multi_select'+val).html(result.sel_data);
                    // $('#wlf_multi_select'+val).select2();
                    // $('#state').trigger('change.select2');

            }
            })

        }
        // alert($(this).val());//here
    });
});

jQuery(document).ready(function($) {
    $(document).on('click','.clear-logs',function(e){
        $('.loader-img').css('opacity','1');
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data:{
                    action: 'wlf_delete_log',
            },
            success: function (response) {
                    // if(response>0){
                    //     alert("Number of records deleted => "+response);
                    // }
                    $( ".af_sm_table" ).load(window.location.href + " .af_sm_table" );
                    console.log(response);
                    $('.loader-img').css('opacity','0');
            }
        })
    });
});

jQuery(document).ready(function($) {
    $(document).on('change','.type_select',function(e){
        // $(this).parent().next().find('div').css('display','none');
        // $(".wlf_multi_select").select2("val", "");
        // alert($(this).attr('data-id'));
        // alert($(this).attr('data-val'));
        var id = $(this).attr('data-id');
        var val = $(this).attr('data-val');
        $('#wlf_multi_select'+val).parent().css('display','none');
        $('#wlf_multi_select'+val).select2("val", "");

    	// alert("ok");
        var ajaxurl=wlf_ajax_var.admin_url;
    	var selectedval = $(this).val();
        // if(selectedval=="Select Post Type"){
        //     return;
        // }
    	jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data:{
                    action: 'wlf_select_data',
                    wlfselected: selectedval,
                    id:id,
                    val:val
            },
            success: function (response) {
                    // console.log(response);
                    var result = jQuery.parseJSON( response );
                    $('#wlf_multi_select'+val).html(result.sel_data);
                    // $('#wlf_multi_select'+val).select2();
                    // $('#state').trigger('change.select2');

            }
        })
        setTimeout(function(e) {
            $('#wlf_multi_select'+val).parent().css('display','block');
        },3000);
        

    });
});

jQuery(document).ready(function($) {
    $(document).on('click','.wlf_cloud_set',function(e){
        var token = $('#wf_meta_keyword_token').val();
        var zone = $('#wf_meta_keyword_zone').val();

        if(token==''){
            $('#wf_meta_keyword_token').parent().append('<p id="cldfr_error">* Token is required</p>');
        }
        if(zone==''){
            $('#wf_meta_keyword_zone').parent().append('<p id="cldfr_error1">* Zone is required</p>');
        }
        else{
            jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data:{
                    action: 'wlf_cloudflare_connection',
                    token: token,
                    zone:zone
            },
            success: function (response) {
                    console.log(response);
                    if(response=='error_6111'){
                         $('#wf_meta_keyword_token').parent().append('<p id="cldfr_error">* Invalid format for Authorization header</p>');
                    }
                    else{
                        $('.afacr_options_form').css('display','none');
                        $('.afacr_options_form').before('<br />'+response+'<br />');
                        // $('.afacr_options_form').html('<br />'+response+'<br />');
                    }
                    // if(response.type)
            }
        })
        }
        setTimeout(function(e) {
          $('#cldfr_error').remove();  
          $('#cldfr_error1').remove();  

      },3000);
        
        
    });
});


jQuery(document).ready(function($) {
    $(document).on('change','.act_type_select',function(e){
        // $(this).parent().next().find('div').css('display','none');
        // $(".wlf_multi_select").select2("val", "");
        // alert($(this).attr('data-id'));
        // alert($(this).attr('data-val'));
        var id = $(this).attr('data-id');
        var val = $(this).attr('data-val');
        $('#act_wlf_multi_select'+val).parent().css('display','none');
        $('#act_wlf_multi_select'+val).select2("val", "");

        // alert("ok");
        var ajaxurl=wlf_ajax_var.admin_url;
        var selectedval = $(this).val();
        // if(selectedval=="Select Post Type"){
        //     return;
        // }
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data:{
                    action: 'wlf_select_data',
                    wlfselected: selectedval,
                    id:id,
                    val:val
            },
            success: function (response) {
                    // console.log(response);
                    var result = jQuery.parseJSON( response );
                    $('#act_wlf_multi_select'+val).html(result.sel_data);
                    // $('#wlf_multi_select'+val).select2();
                    // $('#state').trigger('change.select2');

            }
        })
        setTimeout(function(e) {
            $('#act_wlf_multi_select'+val).parent().css('display','block');
        },3000);
        

    });
});


jQuery(document).ready(function($) {
    $('#selectall').change(function(){
        if($(this).prop('checked')) {
            $('.allchkbx').prop('checked');
        } else {
            // alert('is not checked');
        }
});

});
// jQuery(document).ready(function($) {
//     // alert($('.type_select').val());
//     // $('.type_select').change(function(){
//         var id = $('.type_select').attr('data-id');
//         var val = $('.type_select').attr('data-val');
//         // alert(val);
//         var ajaxurl=wlf_ajax_var.admin_url;
//         var selectedval = $('.type_select').val();
//         jQuery.ajax({
//             url: ajaxurl,
//             type: 'POST',
//             data:{
//                     action: 'wlf_select_data_initial',
//                     wlfselected: selectedval,
//                     id:id,
//                     val:val
//             },
//             success: function (response) {
//                     // console.log(response);
//                     $('.wlf_multi_select').html(response);
//             }
//         })
//     // });
// });
jQuery(document).ready(function($) {
    // $('.wlf_add').click(function(e){
    //     var newcreated = $('.wlf_main_div').append($('.wsl-row').html());
    //     newcreated.id = "kljlkfjl";
    //     // newcreated.find('.wlf_multi_select').select2();
    //     e.preventDefault();
    // });
});

jQuery(document).ready(function($) {
    $(document).on('click','.wlf_add',function(e){

        var postID = $(this).attr("data-theId");
        // return;
        // do_action('save_post', postID);
        let ajaxurl = wlf_ajax_var.admin_url;
        jQuery.ajax(
            {
                url:ajaxurl,
                type: 'POST',
                data:{
                    action: 'wlf_repeater_fields',
                    postID:postID,
                    // checkbox_status:checked,
                },
                success: function (response) {
                    // console.log(response);
                   // console.log(response);
                      // $('.wlf_main_div').empty();
                        $('.wlf_main_div').html(response);
                        // $('#wlf_main_div').load(location.href + " #wlf_main_div");
                      $('.wlf_multi_select').select2();
                    
                    // $('.wlf_multi_select').trigger('change');

                   // location.reload(true);
                }
            }
        )
    });
});

jQuery(document).ready(function($) {
    $(document).on('click','.wlf_del',function(e){
        var delID  = $(this).attr("data-delId");
        var postID = $(this).attr("data-postId");
        let ajaxurl = wlf_ajax_var.admin_url;
        jQuery.ajax(
            {
                url:ajaxurl,
                type: 'POST',
                data:{
                    action: 'wlf_repeater_fields_delete',
                    delID:delID,
                    postID:postID,
                    // checkbox_status:checked,
                },
                success: function (response) {
                   // console.log(response);   
                    // $('.wlf_main_div').load(window.location + " .wlf_main_div");
                    $('.wlf_main_div').html(response);
                     $('.wlf_multi_select').select2();
                    // $('.wlf_multi_select').select2();
                   // location.reload(true);
                }
            }
        )
    });
});

jQuery(document).ready(function($) {
    $(document).on('click','.wlf_selectweb_btn',function(e){
        var selweb = $('.selected_web').val().split(',')[0];
        var selzon = $('.selected_web').val().split(',')[1];
        var seltkn = $('#wf_meta_keyword_token').val();
        var selzzn = $('#wf_meta_keyword_zone').val();

        // alert($('.selected_web').val().split(',')[0]);
        jQuery.ajax(
            {
                url:ajaxurl,
                type: 'POST',
                data:{
                    action: 'save_tkn_web_sel',
                    selweb:selweb,
                    seltkn:seltkn,
                    selzon:selzon,
                    selzzn:selzzn
                },
                success: function (response) {
                    location.reload();
                }
            }
        )
    });
});

jQuery(document).ready(function($) {
    $(document).on('click','.wf_sync_data',function(e){

        jQuery.ajax(
            {
                url:ajaxurl,
                type: 'POST',
                data:{
                    action: 'wlf_cloud_disc',
                },
                success: function (response) {
                    location.reload();
                }
            }
        )
    });
});

jQuery(document).ready(function($) {
    $('.wf_del_data').closest('tr').find('span.view').remove();
    $(document).on('click','.wf_del_data',function(e){
        var location = $(this).closest('tr').find('span.trash a.submitdelete').attr('href');   
        window.location.href = location;
    });
});

jQuery(document).ready(function($) {
    $(document).on('click','.wlf_manual_cache',function(e){
        jQuery.ajax(
            {
                url:ajaxurl,
                type: 'POST',
                data:{
                    action: 'wlf_manual_cache',
                },
                success: function (response) {
                    location.reload();
                    console.log("cache cleaned => "+response);
                }
            }
        )
    });
});