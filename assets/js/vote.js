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


/* vote widget */
function vote_widget() {
	$.ajaxSetup({cache: false}); 
	$.ajax({
		url: "/ajax/vote/show",
		// type: "POST",
		dataType: "json",
		success: function(data){
			console.log(data);
			// var vote_widget = $(".block-vote");
			// var headtitle = '<h3>'+ data.headvote.title +'</h3>';
			// if(!data.status) {

			// 	var formstart = "<form method='post' id='vote_send'>";
			// 	var first = [];
			// 	for(var i=0;i<data.optionvote.length;i++)
			// 	{
			// 		first[first.length] = "<div class='ul-styles'><input required='required' type='radio' name='radio' value='" + data.optionvote[i].id + "'> " + data.optionvote[i].description + "</div>";
			// 	}
			// var submit = "<input type='hidden' name='vote_id' value='" + data.headvote.id + "'><input type='submit' id='vote_submit' class='btn btn-default' name='send_vote' value='Голосовать'></form><br>";
			// var firststr = first.join('');
			// var voteslink = "<a class='content' href='/votes'><h4>ВСЕ ГОЛОСОВАНИЯ</h4></a>";			
			// vote_widget.html(headtitle + formstart + firststr + submit + voteslink);

			// } else {

			// 	var headvote = "<table class='vote-table'><tr><td>ВСЕГО ГОЛОСОВ</td><td>" + data.count + "</td><td></td></tr>";
			// 	var fullwidth = data.optionvote[0].cnt;

			// 	var first = [];

			// 	for(var i=0;i<data.optionvote.length;i++)
			// 	{
			// 		if(fullwidth != 0)
			// 			width = (data.optionvote[i].cnt * 100) / fullwidth;
			// 		else
			// 			width = 0;

			// 		if(data.count != 0)
			// 			procent = (data.optionvote[i].cnt * 100) / data.count;
			// 		else
			// 			procent = 0;
			// 		var formatnumber = procent.toFixed(1);
			// 		var firsttable = "<tr><td>" + data.optionvote[i].description + "</td><td>" + data.optionvote[i].cnt + "</td><td>" + formatnumber + "%</td></tr><tr><td colspan='3'><div class='progress'><div class='progress-bar progress-bar-danger' role='progressbar' aria-valuenow='80' aria-valuemin='0' aria-valuemax='100' style='width:" + width + "%'></div></div></td></tr>";

			// 		first[first.length] = firsttable;
			// 	}

			// 	var endtable = "</table>";

			// 	var firststr = first.join('');

			// 	var voteslink = "<a class='content' href='/votes'><h4>ВСЕ ГОЛОСОВАНИЯ</h4></a>";
			// 	vote_widget.html(headtitle + headvote + firststr + endtable + voteslink);
			// }
		}
	})
}