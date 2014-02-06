(function($){
	
	// Placeholder
	$(' [placeholder] ').defaultValue();
	
	$('.tooltip').tipsy({gravity: 's'}); // nw | n | ne | w | e | sw | s | se
	
	$('#tag-input').tag({separator: ','});
	
    $("textarea, select, input:checkbox, input:radio, input:file").uniform();
    
	jQuery('.progress.animate span[rel]').css('width', 0).each(function(){
		jQuery(this).animate({
			width: jQuery(this).attr('rel') + '%'
		}, 1000);
	});
	
	// Delete confirmation
	jQuery('a.delete').click(function(){
		var message = jQuery(this).attr('rel');
		if (message=='') message = 'Are you sure?';
		if (confirm( message )) {
			return true;
		}
		return false;
	});
	
	// Tabs 
	jQuery('.tab-nav ul li a').click(function() {
		var href = jQuery(this).attr("href");
		jQuery('.tab').hide();
		jQuery('.tab-nav ul li').removeClass('active');
		jQuery(this).parent().addClass('active');
		jQuery(href).show();
		jQuery.cookie('open_tab', href);
		return false;
	});
	if (jQuery('.tab-nav ul li.active').size() > 0) {
		jQuery('.tab-nav ul li.active a').click();
	} else {
		jQuery('.tab-nav ul li:first a').click();
	}
	
	// Lightbox
	
	$("a[href$='.jpg'], a[href$='.jpeg'], a[href$='.gif'], a[href$='.png']").fancybox({
		'overlayShow'	: true,
	});
	$("a[href$='.pdf'], a[href$='.css'], a[href$='.txt'], a[href$='.mov'], a[href$='.doc'], a[href$='.xls'], a[href$='.swf']").fancybox({
		'width'				: '75%',
		'height'			: '75%',
		'autoScale'			: false,
		'type'				: 'iframe'
	});
	
	// Ajax Login
	jQuery('form#supportpress_login').submit(function(){
		
		var thisform = this;
		var fields = jQuery('p.inputs', thisform);
		
		jQuery(fields).block({ message: null, overlayCSS: { 
	        backgroundColor: '#fff', 
	        opacity:         0.6 
	    } });
	    
	    var data = {
			action: 		'woo_supportpress_ajax_login_process',
			security: 		jQuery('input[name=\"login_nonce\"]', thisform).val(),
			log: 			jQuery('input[name=\"log\"]', thisform).val(),
			pwd: 			jQuery('input[name=\"pwd\"]', thisform).val(),
			redirect_to:	jQuery('input[name=\"redirect_to\"]', thisform).val()
		};
	
		// Ajax action
		jQuery.post( jQuery('input[name=\"ajax_url\"]', thisform).val(), data, function(response) {
			
			jQuery('span.error').remove();
			jQuery('input.input-error').removeClass('error');
			
			result = jQuery.parseJSON( response );
			
			if (result.success==1) {
				window.location = result.redirect;
			} else if (result.error) {
				jQuery(thisform).append('<span class="error">' + result.error + '</span>');
				jQuery('.input-text', fields).addClass('input-error');
				jQuery(fields).unblock();
			} else {
				return true;
			}
		});
		
		return false;
	});
	
	/*-----------------------------------------------------------------------------------*/
	/* Responsive menus */
	/*-----------------------------------------------------------------------------------*/
	(function(a){var b=0;a.fn.mobileMenu=function(c){function m(a){if(f()&&!g(a)){l(a)}else if(f()&&g(a)){j(a)}else if(!f()&&g(a)){k(a)}}function l(b){if(e(b)){var c='<select id="mobileMenu_'+b.attr("id")+'" class="mobileMenu">';c+='<option value="">'+d.topOptionText+"</option>";b.find("li").each(function(){var b="";var e=a(this).parents("ul, ol").length;for(i=1;i<e;i++){b+=d.indentString}var f=a(this).find("a:first-child").attr("href");var g=b+a(this).clone().children("ul, ol").remove().end().text();c+='<option value="'+f+'">'+g+"</option>"});c+="</select>";b.parent().append(c);a("#mobileMenu_"+b.attr("id")).change(function(){h(a(this))});j(b)}else{alert("mobileMenu will only work with UL or OL elements!")}}function k(b){b.css("display","");a("#mobileMenu_"+b.attr("id")).hide()}function j(b){b.hide("display","none");a("#mobileMenu_"+b.attr("id")).show()}function h(a){if(a.val()!==null){document.location.href=a.val()}}function g(c){if(c.attr("id")){return a("#mobileMenu_"+c.attr("id")).length>0}else{b++;c.attr("id","mm"+b);return a("#mobileMenu_mm"+b).length>0}}function f(){return a(window).width()<d.switchWidth}function e(a){return a.is("ul, ol")}var d={switchWidth:768,topOptionText:"Select a page",indentString:"   "};return this.each(function(){if(c){a.extend(d,c)}var b=a(this);a(window).resize(function(){m(b)});m(b)})}})(jQuery);

	
	// Responsive Navigation (switch top drop down for select)
	jQuery('#main-nav ul').mobileMenu({
		switchWidth: 767,                   //width (in px to switch at)
		topOptionText: 'Select a page',     //first option text
		indentString: '&nbsp;&nbsp;&nbsp;'  //string for indenting nested items
	});
		
})(window.jQuery);