jQuery(document).ready(function(){
	
	// registration form
	jQuery("#checkout_login_register input[name=login_register]").click(function(){
		var val = jQuery(this).val();
		if(val == 'guest')
			jQuery("#checkout_login_register #password").closest("div").hide();
		else{
			jQuery("#checkout_login_register #password").closest("div").show();
			if(val == 'login'){
				jQuery('#register_password_label').hide();
				jQuery('#login_password_label, .onepageForgotPassword').show();
			}
			else if(val == 'register'){
				jQuery('#register_password_label').show();
				jQuery('#login_password_label, .onepageForgotPassword').hide();
			}
		}
		if(val == 'login')
			jQuery("#checkout_login_register #confirm_email").closest("div").hide();
		else{
			jQuery("#checkout_login_register #confirm_email").closest("div").show();
		}
	});		
	
	// watch loaded step to allow stepping back
	jQuery("#onepage-checkout .backable:not(.active)").live("click", function(){				
		// we load the step content if it's not there yet
		var step = jQuery(this);				
		
		if(step.find(".sub-content:first").html() == ""){			
			jQuery.ajax({
				timeout: 30000,
				dataType: "json",
				url: step.data("url"),
				beforeSend: function(){
					jQuery.blockUI({message: '<h1 class="popupXcheckout">preparing to ride // please wait...</h1>', css: { 'background': 'none', 'border': 'none' }});
				},
				success: function(responseText, statusText, xhr, form){
					updateStep(responseText);
					setupForm();
				},
		   		complete: function(){
		   			jQuery.unblockUI();
		   		},
				error: function(){
					jQuery.unblockUI();
				}
			});
		}
		else{
			jQuery("#onepage-checkout .step.active").removeClass("active");	
			step.addClass("active");
		}			
		//setupForm();
	});		
	
	// watch the address toggle buttons
	jQuery("#onepage-checkout .toggle").live("click", function(e){		
		var visible_elements = jQuery(jQuery(this).data("target")+".show");
		var invisible_elements = jQuery(jQuery(this).data("target")+".hide");
		
		visible_elements.removeClass("show").addClass("hide").find(":input").attr("disabled", true);			
		invisible_elements.removeClass("hide").addClass("show").find(":input").attr("disabled", false);

		e.preventDefault();
	});
	
	// watch step-edit-link
	jQuery(".step-edit-link").live("click", function(){
		jQuery("#"+jQuery(this).data("step")).trigger("click");
	});
	
	// watch the checkbox that allow same shipping address
	jQuery("#same_shipping_billing_address").live("click", function(){
		var payment_container = jQuery("#onepage-checkout-payment-address-container");
		if(jQuery(this).is(':checked')){			
			payment_container.find(":input").attr("disabled", true);			
			jQuery("#onepage-checkout-payment-address-container").hide();
		}
		else{
			payment_container.find(":input").attr("disabled", false);			
			jQuery("#onepage-checkout-payment-address-container").show();
		}
	});
	
	// watch the address form (perhaps we dont need to use live?)
	jQuery('#shippingCountryId').live("change", function(){
		updateState("shipping");
	});
	jQuery('#billingCountryId').live("change", function(){
		updateState("billing");
	});
	setupForm();
});

function setupForm(){
	// all ajax form
	var $form = jQuery("#onepage-checkout .step.active form.ajax");

	// we need to disable hidden inputs
	$form.find(".hide :input").attr("disabled", true);
	
	var validation_key = $form.data('validation');

	if(validation_key !== undefined && validation_key != ''){
		var validator = $form.validate({rules:validation[validation_key]["rules"],
					  //messages:validation[validation_key]["messages"],
					  submitHandler: function(form) {						   
						   $form.ajaxSubmit({
								dataType: "json",								
								timeout: 30000,
								beforeSend: function(){
									jQuery.blockUI({message: '<h1 class="popupXcheckout">preparing to ride // please wait...</h1>', css: { 'background': 'none', 'border': 'none' }});
								},
								success: function(responseText, statusText, xhr, form){
									updateStep(responseText);
									setupForm();
								},
						   		complete: function(){
						   			jQuery.unblockUI();
						   		},
								error: function(){
									jQuery.unblockUI();
								}
							});
						 }
					  });
	}
	else{		
		$form.ajaxForm({
			dataType: "json",
			timeout: 30000,
			beforeSend: function(){
				jQuery.blockUI({message: '<h1 class="popupXcheckout">preparing to ride // please wait...</h1>', css: { 'background': 'none', 'border': 'none' }});
			},
			success: function(responseText, statusText, xhr, form){
				updateStep(responseText);
				setupForm();
			},
	   		complete: function(){
	   			jQuery.unblockUI();
	   		},		
			error: function(){
				jQuery.unblockUI();
			}
		});
	}	
}

function updateStep(responseText){
	// redirection? handle it with javascript
	if(responseText.redirect !== undefined && responseText.redirect != ""){
		window.location.href = responseText.redirect;
	};			

	var current_active = old_active = jQuery("#onepage-checkout .step.active");	
	// update new content
	if(responseText.steps !== undefined && responseText.steps.length != 0){		
		jQuery.each(responseText.steps, function(index, step){
			if(step.content != ""){
				jQuery("#"+index+" .sub-content:first").html(step.content);
			}
			
			if(step.current) {
				if(current_active.attr("id") != index){
					current_active = jQuery("#"+index).addClass('active loaded').next(".loaded").removeClass("loaded");
					old_active.removeClass("active").find(".message:first").html("");					
				}
			}			
		});
	}	
	
	// update new messages
	if(responseText.messages !== undefined && responseText.messages.length != 0){
		var all_messages = ""; 
		jQuery.each(responseText.messages, function(index, type){
			jQuery.each(type, function(index2, ref){
				jQuery.each(ref, function(index3, message){
					all_messages += '<div>'+message+'</div>';
				});
			});
		});
		
		current_active.find(".message:first").html(all_messages).attr("tabindex", -1).focus();
	}
	else
		current_active.find(".message:first").html("");
	
	// update progress
	if(responseText.progress !== undefined && responseText.progress != ""){
		jQuery("#checkout-progress").html(responseText.progress);
	}
}

function updateState(address_type) {
    // show timer
    var zoneId = jQuery('#'+address_type+'ZoneId');
    var state = jQuery('#'+address_type+'State');
    var countryId = jQuery('#'+address_type+'CountryId').val();
    
    var sz = 0 < zoneId.size() ? zoneId : state;
    if (all_zones[countryId]) {
        var state_value = state.val();
        var state_select = '<select id="'+address_type+'ZoneId" name="'+address_type+'ZoneId">';
        state_select += '<option value=""><?php _vzm("-- Please select a state --") ?></option>';
        for (var zoneId in all_zones[countryId]) {
            var name = all_zones[countryId][zoneId];
            var selected = state_value == name ? ' selected="selected"' : '';
            state_select += '<option value="'+zoneId+'"'+selected+'>'+name+'</option>';
        }
        state_select += '</select>';

        // replace with dropdown
        sz.after(state_select).remove();
    } else {
        // free text
       sz.after('<input type="text" id="'+address_type+'State" name="'+address_type+'State" value="">').remove();
    }
};

var selected;
var submitter = null;
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=320,screenX=150,screenY=150,top=150,left=150')
}
function couponpopupWindow(url) {
  window.open(url,'couponpopupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=320,screenX=150,screenY=150,top=150,left=150')
}
function submitFunction($gv,$total) {
  if ($gv >=$total) {
    submitter = 1;	
  }
}

function methodSelect(theMethod) {
  if (document.getElementById(theMethod)) {
    document.getElementById(theMethod).checked = 'checked';
  }
}