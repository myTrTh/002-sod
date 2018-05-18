// add vote options
$(function(){
	$('#add_option').on('click', function(){
		var count = $('.vote-options > input').length;
		var div = $('.vote-options');
		div.append('<input type="text" name="vote_options[]">');
	})
})

// delete vote options
$(function(){
	$('#remove_option').on('click', function(){
		var count = $('.vote-options > input').length;

		if(count > 2) {
			var last_input = $('.vote-options input:last');
			last_input.remove();
		} else {
			alert("Должно быть минимум два варианта ответа");
		}
	})
})