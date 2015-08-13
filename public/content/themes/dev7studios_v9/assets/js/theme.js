;(function($) {
	"use strict";

	$(document).ready(function($) {

		$('.edd_download_columns_3 .edd_download').attr('style', '').addClass('col-sm-4');

		if($('#isa-edd-specs').length){ $('#isa-edd-specs').addClass('table table-striped table-bordered'); }
		if($('#edd_sl_license_keys').length){ $('#edd_sl_license_keys').addClass('table table-striped table-bordered'); }
		if($('#edd_sl_license_sites').length){ $('#edd_sl_license_sites').addClass('table table-striped table-bordered'); }

		if($('#edd_sl_license_keys').length || $('#edd_sl_license_sites').length){
			$('#account-tabs').hide();
		}

		if($('#affwp-affiliate-dashboard').length){
			$('#affwp-affiliate-dashboard').addClass('col-sm-12');
		}

		if($('#account-tabs').length){
			$('#account-tabs').tabs();
		}

	});

})(window.jQuery);