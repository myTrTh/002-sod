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

$(document).ready(function(){
	vote_widget();
})

/* vote widget */
function vote_widget() {
	$.ajaxSetup({cache: false});
	$.ajax({
		url: "/ajax/vote/show",
// 		// type: "POST",
		dataType: "json",
		success: function (data) {
			var header = $('#vote-header');
			header.html(data.vote.title);
		}
	});
};

/* send vote */
// $(document).ready(function(){
// 	$(document).on('submit', '#vote_send', function(e){
// 		e.preventDefault();
// 		var form = $('#vote_send');
// 		$.ajaxSetup({cache: false});
// 		$.ajax({
// 			url: '/ajax/vote/send',
// 			type: form.attr('method'),
// 			data: form.serialize(),
// 			// dataType: 'json',
// 			success: function() {
// 				vote_widget();
// 			}
// 		});
// 	});
// });
