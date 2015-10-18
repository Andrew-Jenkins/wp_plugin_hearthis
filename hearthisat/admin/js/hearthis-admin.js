(function( $ ) {
	'use strict';

	/**
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this function.
	 *
	 * From here, you're able to define handlers for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 */


		var defaults = {
			height : null,
			height_list : null,
			background : null,
			waveform : null,
			init_height : 130,
			init_height_list : 420,
		};


		// selectorsColor
		// 		color: "#333333",
		// 	      color = hex;

		// hightlightsColor
		// 		color: "#d81900",
		// 		 highlight = hex;

		// $('#hearthis_color').val('#333333');
		// $('#hearthis_hcolor').val('#d81900');

		$("#hearthis_style").change(function () {
			if ($(this).val() == 2) {
				$("#style-tpl").slideDown(500);
			} else {
				$("#style-tpl").slideUp(500);
			}
		});

		$("#hearthis_background").change(function () {
			if (this.checked) {
				defaults.background = 1;
				defaults.waveform = 0;
				this.value = 1;
				defaults.height = 170;
				$("#hearthis_waveform").prop("disabled" , "disabled").val('0');
			} else {
				defaults.background = 0;
				this.value = 0;
				this.checked = false;
				if(defaults.height != '' && defaults.height >= 130)
						defaults.height = defaults.init_height;
				$("#hearthis_waveform").prop("disabled", false).val(defaults.waveform);
			}
			change_height_field();
		});


		$("#hearthis_waveform").change(function () {
			if (this.checked) {
				defaults.waveform = 1;
				defaults.background = 0;
				this.value = 1;
				defaults.height = 95;
				$("#hearthis_background").prop("disabled" , "disabled" ).val('0');
			} else {
				defaults.waveform = 0;
				this.value = 0;
				this.checked = false;
				if(defaults.height != '' && ( defaults.height >= 130 || defaults.height <= 95))
						defaults.height = defaults.init_height;
				$("#hearthis_background").prop("disabled", false).val(defaults.background);
			}
			change_height_field();
		});


		$("#hearthis_autoplay").change(function () {
			if (this.checked) {
				this.value = 1;
			} else {
				this.value = 0;
			}
		});

		$("#hearthis_cover").change(function () {
			if (this.checked) {
				this.value = 1;
			} else {
				this.value = 0;
			}
		});

		$("#hearthis_css").keyup(function () {
			var css = $(this).val();
			var css2 = css.replace(/(<([^>]+)>)/ig, "");
			var css3 = css2.replace(" ", "+");
			this.value = css3;
		});

	

		function change_height_field() 
		{
			$("#hearthis_height").val(defaults.height);
		}


		$(function () {
			$('#hearthis_color,#hearthis_hcolor').wpColorPicker();
		});



		$(window).on('load', function() 
		{
			if($("#hearthis_style").val() == 2)
			{
				$("#style-tpl").show();
			}	
			if($("#hearthis_waveform").val() == 1)
			{
				$("#hearthis_waveform").prop('checked','checked');
				$("#hearthis_background").prop('checked',false);
			}	
			if($("#hearthis_background").val() == 1)
			{
				$("#hearthis_background").prop('checked','checked');
				$("#hearthis_waveform").prop('checked',false);
			}	
			
	
		});
			// chgFct('hearthis_background',"hearthis_waveform", true);
			// chgFct('hearthis_waveform',"hearthis_background", true);

	
})( jQuery );
