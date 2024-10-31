var wpversion;
jQuery(document).ready(function($) {
	//alert('got');
	var wpversion = $('meta[name=wpversion]').attr("content");
	//alert($('meta[name=wpversion]').attr("content"));
	$(".postbox h3.tzpost-expand,.postbox .handlediv").click(function(e){
		if($(this).hasClass('tzpost-highlight')){
			$(this).removeClass('tzpost-highlight')
		}
		if($(this).parent().hasClass('closed')){
		$(this).parent().removeClass('closed');
		$(this).parent().find(".handlediv").removeClass('down');
		$(this).parent().find(".handlediv").addClass('up');
		}
		else
		{
		$(this).parent().addClass('closed');
		$(this).parent().find(".handlediv").removeClass('up');
		$(this).parent().find(".handlediv").addClass('down');
		}
	});
	
	if(wpversion >= '3.5'){
	 	$(".tzpost-color-picker").wpColorPicker();
	}
	else{
		 jQuery('.tzpostfarb').hide();
		 $('.tzpostfarb').each(function() {
			 //alert();
			 var sell = $(this).parent().find('.tzpost-color-picker').attr('id');
			 //alert(sell);
			 jQuery(this).farbtastic("#"+sell);
		 });
		 
		 $('.tzpost-color-picker').click(function() {
        	$(this).parent().find('.tzpostfarb').fadeIn();
		});
	
		$(document).mousedown(function() {
			$('.tzpostfarb').each(function() {
				var display = $(this).css('display');
				if ( display == 'block' )
					$(this).fadeOut();
			});
		});
	 }//
});

function onlyNum(evt)
{
    var e = window.event || evt;
    var charCode = e.which || e.keyCode;

    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

    return true;

}
function NumNdNeg(evt)
{
    var e = window.event || evt;
    var charCode = e.which || e.keyCode;

    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 45)
        return false;

    return true;

}

function advpsCheckCat(p,n){
	var fldSel = 'tzpost-cat-field'+n;
	if(p != "page"){
		jQuery.ajax({
			  type : "post",
			  context: this,
			  dataType : "html",
			  url : tzpostajx.ajaxurl,
			  data : {action: "tzpostchkCategory",post_type:p,checkReq:tzpostajx.tzpostAjaxReqChck},
			  success: function(response) {
				 //alert(response);return;
					jQuery('#'+fldSel).html(response);
				},
				complete : function(){
					
				}
		});///
	}
	else
	{
		jQuery('#'+fldSel).html('');
	}
}

function tzpostUpdateLabel(f,v,id){
	jQuery('#tzpostbox'+id).css('display','inline');
	jQuery.ajax({
		  type : "post",
		  context: this,
		  dataType : "html",
		  url : tzpostajx.ajaxurl,
		  data : {action: "tzpostUpdateLabel",f_name:f,f_value:v,checkReq:tzpostajx.tzpostAjaxReqChck},
		  success: function(response) {
			 jQuery('#tzposttxt'+id).html(v);
			jQuery('#tzpostbox'+id).css('display','none');
			},
			complete : function(){
				
			}
	});///
}
function tzpostupdateOptionSet(id){
	var optdata = jQuery('#'+id).serialize();

	jQuery('.ajx-sts').html('');
	jQuery('#'+id).find('.ajx-loader').css('display','inline');
	
	jQuery.ajax({
		  type : "post",
		  context: this,
		  dataType : "html",
		  url : tzpostajx.ajaxurl,
		  data : {action: "tzpostUpdateOpt",optdata:optdata,checkReq:tzpostajx.tzpostAjaxReqChck},
		  success: function(response) {
			 
			jQuery('#'+id).find('.ajx-loader').css('display','none');
			jQuery('#'+id).find('.ajx-sts').html(response);
			setTimeout('clearText()',4000);
			},
			complete : function(){
				
			}
	});///
}
function listPost(n){
	var fldSel = 'tzpost-plist-field'+n;
	
	var ptype = jQuery("#plist"+n+" select[name=tzpost_post_stypes]").val();
	var pmax = jQuery("#plist"+n+" input[name=tzpost_plistmax]").val();
	var porderBy = jQuery("#plist"+n+" select[name=tzpost_plistorder_by]").val();
	var porder = jQuery("#plist"+n+" select[name=tzpost_plistorder]").val();
	
	jQuery('#plist'+n).find('.ajx-loaderp').css('display','inline');
	
	jQuery.ajax({
		  type : "post",
		  context: this,
		  dataType : "html",
		  url : tzpostajx.ajaxurl,
		  data : {action: "tzpostListPost",ptype:ptype,pmax:pmax,porderBy:porderBy,porder:porder,checkReq:tzpostajx.tzpostAjaxReqChck},
		  success: function(response) {
			 jQuery('#'+fldSel).html(response);
			 jQuery('#plist'+n).find('.ajx-loaderp').css('display','none');
			
		  },
		  complete : function(){
			  
		  }
	});///
}
function tzupdateSm(elem,id){
	jQuery('#smudtsts'+id).css('display','inline');
	
	var selval = jQuery(elem).val();
	var selnam = jQuery(elem).attr('name');
	
	if(selval == 'query'){
		jQuery("#plist"+id+" table").addClass("tzpost-hide");
		jQuery("#query"+id+" table").removeClass("tzpost-hide");
	}
	else
	{
		jQuery("#query"+id+" table").addClass("tzpost-hide");
		jQuery("#plist"+id+" table").removeClass("tzpost-hide");
	}
	
	jQuery.ajax({
		  type : "post",
		  context: this,
		  dataType : "html",
		  url : tzpostajx.ajaxurl,
		  data : {action: "tzpostupdateSmethod",selnam:selnam,selval:selval,checkReq:tzpostajx.tzpostAjaxReqChck},
		  success: function(response) {	
		  	jQuery('#smudtsts'+id).css('display','none');
		  },
		  complete : function(){
			  
		  }
	});///
}

function tzpostdeleteTzPost(id){
	var rsp = confirm("Do you really want to delete this layout?");
	if(rsp){
		jQuery("#frmOptDel"+id).removeAttr("onsubmit");
		jQuery("#frmOptDel"+id).submit();
	}
}
function pagerAttr(v){
	alert(v);
}
function clearText(){
	jQuery('.ajx-sts').html('');
}
function sliderType(v,id){
	if(v != 'standard'){
		jQuery("#tzpost-pthumb-lvl"+id).addClass('tzpost-fade');
		jQuery("#tzpost-pthumb"+id).attr('disabled','disabled');
	}
	else
	{
		jQuery("#tzpost-pthumb-lvl"+id).removeClass('tzpost-fade');
		jQuery("#tzpost-pthumb"+id).removeAttr('disabled');
	}
}