var ventas = function(){

	return {
		parse_query_string: function(query) {
		  var vars = query.split("&");
		  var query_string = {};
		  for (var i = 0; i < vars.length; i++) {
		    var pair = vars[i].split("=");
		    var key = decodeURIComponent(pair[0]);
		    var value = decodeURIComponent(pair[1]);
		    // If first entry with this name
		    if (typeof query_string[key] === "undefined") {
		      query_string[key] = decodeURIComponent(value);
		      // If second entry with this name
		    } else if (typeof query_string[key] === "string") {
		      var arr = [query_string[key], decodeURIComponent(value)];
		      query_string[key] = arr;
		      // If third or later entry with this name
		    } else {
		      query_string[key].push(decodeURIComponent(value));
		    }
		  }
		  return query_string;
		},
		init: function(){
			
			var query = window.location.search.substring(1);
			var qs = ventas.parse_query_string(query);
			
			if (typeof qs.mensaje !== 'undefined') {
				
				var y = $('#message-' + qs.mensaje).offset().top - $('#breadcrumb').height() -100;
				console.log($('#message-' + qs.mensaje).offset().top );
				console.log($('#breadcrumb').height());
				console.log(y);
				$('#wrapper-content').animate({
					scrollTop: y
				}, 1000, function(){

					$('#message-' + qs.mensaje).parents('div').eq(0).addClass('highlight');

					setTimeout(function(){
						$('#message-' + qs.mensaje).parents('div').eq(0).removeClass('highlight');
					}, 5000);

				});
			}

		}
	}

}();


$(document).ready(function () {

	ventas.init();
    
});