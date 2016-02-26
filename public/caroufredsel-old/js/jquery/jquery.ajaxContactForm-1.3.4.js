/*	
 *	jQuery ajaxContactForm version 1.3.4
 *	www.frebsite.nl
 *	Copyright (c) 2010 Fred Heusschen
 *	Licensed under the MIT license.
 *	http://www.opensource.org/licenses/mit-license.php
 */


(function($) {
	$.fn.ajaxContactForm = function(act, opts) {

		return this.each(function() {
			var t = this,
				f = $(this);
				f.prepend('<input type="text" name="bottrap" value="" style="display: none;" />');
	
			var options 	= $.extend({}, $.fn.ajaxContactForm.defaults, opts),
				submit_btn	= ($(':submit', f).length) ? $(':submit', f) : $(options.submitButton, f),
				reset_btn	= ($(':reset',  f).length) ? $(':reset',  f) : $(options.resetButton, f),
				action		= (typeof(act) == 'undefined') ? '' : act;		


			$('input, textarea, select', f).each(function() {
				if ($.fn.ajaxContactForm.is_invoerveld($(this))) {
					$(this)
						.data("defaultvalue", $(this).val())
						.focus(function() {
							$(this).addClass('focussed');
						})
						.blur(function() {
							$(this).removeClass('focussed');
							if (!$.fn.ajaxContactForm.test_veld($(this), $(this).val())) {
								options.falseFieldFunc(f, $(this), options.language);
							}
						});

					if ($.fn.ajaxContactForm.is_default_value_veld($(this))) {
						$.fn.ajaxContactForm.inactivate($(this));

						if (this.tagName.toLowerCase() == 'select') {
							$.fn.ajaxContactForm.inactivate($(this).find("option:selected"));
							$(this)
								.change(function() {
									if ($(this).val() == $(this).data("defaultvalue")) {
											$.fn.ajaxContactForm.inactivate($(this));
									} else	$.fn.ajaxContactForm.activate($(this));
								});

						} else {
							$(this)
								.focus(function() {
									if ($(this).val() == $(this).data("defaultvalue")) {
										$(this).val("");
										$.fn.ajaxContactForm.activate($(this));
									}
								})
								.blur(function() {
									if ($(this).val() == '') {
										$(this).val($(this).data("defaultvalue"));
										$.fn.ajaxContactForm.inactivate($(this));
									}
								});
						}						
					}
				}
			}).filter(':checkbox, :radio').change(function() {
				$(this).trigger("blur");
			});


			submit_btn.click(function() {
				var miss_arr = new Array();
				var data_arr = new Array();
								
				$('input, textarea, select', f).each(function(i) {
					if ($.fn.ajaxContactForm.is_invoerveld($(this))) {		
						var veld = $(this),
							name = veld.attr('name'),
							valu = veld.val();
	
						if ($.fn.ajaxContactForm.is_default_value_veld($(this))) {
							if (valu == $(this).data("defaultvalue")) 						valu = '';
						}
						
						if ($.fn.ajaxContactForm.test_veld(veld, valu)) {
							if (veld.attr('type') == 'radio' 	&& !veld.is(':checked'))	valu = '';
							if (veld.attr('type') == 'checkbox'	&& !veld.is(':checked')) 	valu = '';

							if (valu.length > 0) {
								valu = valu.split('+').join('{PLUS}');
								data_arr.push(name+'='+escape(valu));
							}
						} else {							
							miss_arr.push($(this));
						}
					}
				});

			//	niet goedkeuren
				if (miss_arr.length > 0 || data_arr.length == 0) {
					options.falseMessageFunc(f, options.language);
					for (var z = 0; z < miss_arr.length; z++) {
						options.falseFieldFunc(f, miss_arr[z], options.language);
					}
					return false;
			
			//	goedkeuren en versturen
				} else {
					f.css('opacity', '0.5');

				//	via ajax versturen	
					if (action != '') {
							data_arr.push('language='+options.language);
						for (var i in options.extraValues) {
							data_arr.push(i+'='+options.extraValues[i]);
						}

						$.ajax({
							type: options.method,
							url: action,
							data: 'ajaxcontactform=ajaxcontactform&'+data_arr.join('&'),
							success: function(msg) {
								var msg = msg.split('___');
	
								if (msg[0].toUpperCase() != 'Y')	{
									options.errorMessageFunc(f, msg[1], options.language);
								} else {
									options.succesMessageFunc(f, msg[1], options.language);
								}
							},
							error: function() {
								options.errorMessageFunc(f, 'Error, file not found', options.language);
								f.css('opacity', '1');
							}
						});
						return false;
					
				//	via form versturen
					} else {
						if (t.tagName.toLowerCase() == 'form') {
							t.submit();
							return false;
						}
						return true;
					}
				}
			});
			reset_btn.click(function() {
				$.fn.ajaxContactForm.resetForm(f);				
				return false;
			});
		});
	};

	$.fn.ajaxContactForm.defaults = {
		submitButton:		'.submit',
		resetButton:		'.reset',
		method:				'POST',
		language:			'nl',
		extraValues:		{},
		falseFieldFunc:		function(form, veld, lang) {
			veld.effect("highlight", {color: '#dd0000'}, 1000);
		},
		falseMessageFunc:	function(form, lang) {
			switch (lang) {
				case 'en':
					msg = "Attention, not all the fields have been filled out correctly.";
					break;
				
				case 'de':
					msg = "Achtung, nicht alle Felder sind korrekt ausgefuellt.";
					break;

				default:
					msg = "Let op, niet alle velden zijn correct ingevuld.";
					break;
			}
			alert(msg);
		},
		errorMessageFunc:	function(form, msg, lang) { 
			alert(msg); 
		},
		succesMessageFunc:	function(form, msg, lang) { 
			$.fn.ajaxContactForm.resetForm(form);
			alert(msg);
		}
	};

	$.fn.ajaxContactForm.resetForm = function(form) {
		$('input, textarea, select', form).each(function() {
			
			var type = this.type.toLowerCase();
			if (type == 'checkbox' || type == 'radio') 	  this.checked = false;
			else 										$(this).val($(this).data("defaultvalue"));

			if ($.fn.ajaxContactForm.is_default_value_veld($(this))) {
				$.fn.ajaxContactForm.inactivate($(this));
			}
		});
	};

	$.fn.ajaxContactForm.test_v = function(name, valu, veld) {
		if (name.indexOf('__v') == -1) 	return true;
		if (valu.length < 1) 			return false;
		
		if ((veld.attr('type') == 'radio' || veld.attr('type') == 'checkbox')
	 	 && !veld.is(':checked'))		return false;
										return true;
	};
	$.fn.ajaxContactForm.test_n = function(name, valu) {
		if (name.indexOf('__n') == -1) 	return true;

		var vervangen = new Array(' ', '-', '+', '(', ')', '/', '\\');
		for (var i = 0; i < vervangen.length; i++)	{
			valu = valu.split(vervangen[i]).join('');
		}
		if (valu.length == 0)			return true;
		else if (isNaN(valu))			return false;
										return true;
	};
	$.fn.ajaxContactForm.test_e = function(name, valu) {
		if (name.indexOf('__e') == -1)	return true;
		if (valu.length < 1)			return true;
		if (valu.indexOf("@") != -1 &&
			valu.indexOf(".") != -1 &&
			valu.length > 4
		) {
				return true;
		} else	return false;
	};
	$.fn.ajaxContactForm.test_d = function(valu, veld) {
		if (!$.fn.ajaxContactForm.is_default_value_veld(veld))	return true;
		if (veld[0].tagName.toLowerCase() == 'select') 			return true;
		if (valu == veld.data("defaultvalue"))					return false;
																return true;
	};
	$.fn.ajaxContactForm.test_veld = function(veld, valu) {
		var name = veld.attr('name');
		if ($.fn.ajaxContactForm.test_v(name, valu, veld) 	&&
			$.fn.ajaxContactForm.test_n(name, valu) 		&&
			$.fn.ajaxContactForm.test_e(name, valu)			&&
			$.fn.ajaxContactForm.test_d(valu, veld)
		) {
				return true;
		} else 	return false
	};
	$.fn.ajaxContactForm.is_invoerveld = function(veld) {
		if (veld.attr('type') == 'button') 	return false;
		if (veld.attr('type') == 'submit') 	return false;
		if (veld.attr('type') == 'reset') 	return false;
		if (veld.attr('name') == 'bottrap')	return false;
											return true;
	};
	$.fn.ajaxContactForm.is_default_value_veld = function(veld) {		
		return (veld.attr('name').indexOf('__d') == -1) ? false : true;
	};
	$.fn.ajaxContactForm.inactivate = function(veld) {
		veld.addClass("inactive");
	};
	$.fn.ajaxContactForm.activate = function(veld) {
		veld.removeClass("inactive");
	};


})(jQuery);