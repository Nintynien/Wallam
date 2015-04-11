$(document).ready(function(){
	
	$(".movie-image").hover(function(){
		$(this).find(".info").show();

	},
	function()
	{
		$(this).find(".info").hide();
	});


	$(".blink").focus(function() {
            if(this.title==this.value) {
                this.value = '';
            }
        })
        .blur(function(){
            if(this.value=='') {
                this.value = this.title;                    
			}
		});
		
	$('#shown').change(
	function ()
	{
		if ($("#shown").is(':checked'))
		{
			$(".showing").each(function() {
				if ($(this).text() == "1") {
					//$(this).parent().parent().fadeIn(00);
				} else {
					$(this).parent().parent().fadeOut(00); //Only fade out if not in theaters currently. (Want to only filter current search results)
				}
			})
		} else {
			$(".showing").each(function() {
				$(this).parent().parent().fadeIn(0);
			})
			search();
		}
	});
	$('#shown').trigger('click');
});

function search() {
	if ($("#shown").is(':checked')) {
		$('#shown').prop('checked', false);
	}
	$(".title").each(function() {
		if (($(this).text().toLowerCase().indexOf($("#search").val().toLowerCase()) >= 0) || ($("#search").val() == "")) {
			$(this).parent().fadeIn(00);
		} else {
			$(this).parent().fadeOut(00);
		}
	})
}
