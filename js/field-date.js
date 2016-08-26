jQuery(document).ready(function(){

			var datepickeroptions = {
					appendText: '(dd-mm-yyyy)',
					dateFormat : 'dd-mm-yy',
					showButtonPanel: true,
					changeMonth: true,
					changeYear: true,
					}
	        jQuery('.datepicker').each(function(){
	        jQuery(this).datepicker(datepickeroptions);

	    });
	   });