<script>
// Since using enqueue script to load jquery in no-conflict mode, must declare this to allow $ as shortcut for jQuery
jQuery(document).ready(function($){

  // Hover Transition links
  $.each($('#nue_events .nue_hovertrans'),function(){
	$(this).parent('div,li').css('position','relative');
	$(this).css('height',$(this).parent('div,li').css('height'));
	$(this).css('width',$(this).parent('div,li').css('width'));
	$(this).css('lineHeight',$(this).parent('div,li').css('lineHeight'));
  });
  $('#nue_events .nue_hovertrans').parent('div,li').hover(
	function () {
		$(this).children('.nue_hovertrans').stop(true, true).fadeIn(200);
	},
	function () {
		$(this).children('.nue_hovertrans').stop(true, true).fadeOut(200);
	}
  );

// Ends allowance of jQuery to $ shortcut
});
</script>
