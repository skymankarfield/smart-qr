function bindInputBoxStyling () {
   $(".replaceInput").each(function(){
		inputVars[$(this).attr("id")]=$(this).attr("value");
		$(this)
		.focus(function () {
			$(this)
				.addClass("selected")
				.attr("value", "")
		})
		.blur(function () {
		    if ($(this).attr("value")=="") {
				$(this)
					.attr("value", inputVars[$(this).attr("id")])
					.removeClass("selected");
			}
		})
   })
}

var inputVars = {};

$(document).ready(function(){
bindInputBoxStyling ();


 });