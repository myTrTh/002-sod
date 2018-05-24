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
		dataType: "json",
		success: function (data) {
			if (data.error != 1) {
				var header = $('#vote-header');
				header.html(data.vote.title);

				if (data.access == 'close') {

					$html1 = '<div id="error-vote-msg"></div><form id="widget-form" method="post">';
					$html2 = [];
					for (var i=0; i < data.vote.options.length; i++) {
						if (data.vote.type == 1) {
							$html2[i] = '<div class="checkbox-line"><input type="radio" name="vote_options" value="' + data.vote.options[i].id + '">' + data.vote.options[i].title + '</div>';
						} else {
							$html2[i] = '<div class="checkbox-line"><input type="checkbox" name="vote_options[]" value="' + data.vote.options[i].id + '">' + data.vote.options[i].title + '</div>';
						}
					}
					
					$html2 = $html2.join('');
					$html3 = '<input id="token-input" type="hidden" name="_csrf_token" value=""><input id="widget-vote-send" type="submit" value="Голосовать"></form>';

				} else if (data.access == 'open') {
					$html1 = '<div class="vote-result-bar"><div class="vote-result-name">ВСЕГО</div><div class="vote-result-info">' + data.count + '</div><div class="vote-result-percent"></div></div>';
					$html2 = [];
					var fullwidth = data.count;

					for (var i = 0; i < data.sort_options.length; i++) {
						if (fullwidth == 0) {
							var width = 0;
						} else {
							width = (data.sort_options[i].users.length * 100) / fullwidth;
						}

						if(data.count != 0)
							percent = (data.sort_options[i].users.length * 100) / data.count;
						else
							percent = 0;

						if (percent != 100)
							var formatnumber = percent.toFixed(1);
						else
							var formatnumber = percent;

						$html2[i] = '<div class="vote-option-result"><div class="vote-option-bar"><div class="vote-option-bar-name">' + data.sort_options[i].title + '</div><div class="vote-option-bar-info">' + data.sort_options[i].users.length + '</div><div class="vote-option-bar-percent">' + formatnumber + '%</div></div><div class="vote-progress-bar"><div class="progress-line" style="width: ' + width + '%"></div></div></div>';
					}
					$html2 = $html2.join('');
					$html3 = '';
				}
				var content = $('#vote-content');
				content.html($html1 + $html2 + $html3 + '<br>');
			}
		}
	});
};

/* send vote */
$(document).ready(function(){
	$(document).on('submit', '#widget-form', function(e){
		e.preventDefault();
		var token = $('#vote-content').attr('data-token');
		var token_input = $('#token-input');
		token_input.val(token);
		var form_data = $('#widget-form').serialize();
		$.ajaxSetup({cache: false});
		$.ajax({
			url: '/ajax/vote/send',
			type: "POST",
			data: form_data,
			dataType: "json",
			success: function (data) {
				if (data) {
					var error_field = $("#error-vote-msg");
					error_field.html(data);
				} else {
					vote_widget();
				}
			}
		});
	});
});
