// NAVIGATION
$(document).ready(function() {
	$('.nav-toggle').on('click', function() {
		$('nav > ul > li').not('.logo').slideToggle('fast');
	});

	$(window).resize(function() {
		if ( $(window).width() > 750) {
			$('nav > ul > li').removeAttr('style');
		}
	});	
});

// bb code
$(function(){
	$('.bbimg').on('click', function(){
		
		// var ttbody = $(this).parent().parent();
		var bbtag = $(this).attr('id');
		var textbody = $('textarea');

		// Набор bb кодов
		if (bbtag == 'post') {
			var tag_start = 'post:';
			var tag_end = '';
		} else {
			var tag_start = '[' + bbtag + ']';
			var tag_end = '[/' + bbtag + ']';
		}


		var start = textbody[0].selectionStart;
		var end = textbody[0].selectionEnd;
		var alltext = textbody.val();
		var needtext = textbody.val().substr(start, end - start);
		var bb_and_text = tag_start + needtext + tag_end;

		var cursor_position = textbody.val().length;

		var start_text = alltext.substr(0, start);
		var end_text = alltext.substr(end, cursor_position);
		textbody.val(start_text + bb_and_text + end_text);
		var newlength = textbody.val().length;
		textbody.focus();

		if (needtext.length == 0) {
			textbody[0].setSelectionRange(start+tag_start.length, start+tag_start.length);			
		} else {
			textbody[0].setSelectionRange(start, end+tag_start.length+tag_end.length);
		}

	});
});

// for image light box gallery
// $( 'a' ).imageLightbox(
//     selector:       'class="imagelightbox"',   // string;
//     allowedTypes:   'png|jpg|jpeg|gif',     // string;
//     animationSpeed: 250,                    // integer;
//     preloadNext:    true,                   // bool;            silently preload the next image
//     enableKeyboard: true,                   // bool;            enable keyboard shortcuts (arrows Left/Right and Esc)
//     quitOnEnd:      false,                  // bool;            quit after viewing the last image
//     quitOnImgClick: false,                  // bool;            quit when the viewed image is clicked
//     quitOnDocClick: true,                   // bool;            quit when anything but the viewed image is clicked
//     onStart:        false,                  // function/bool;   calls function when the lightbox starts
//     onEnd:          false,                  // function/bool;   calls function when the lightbox quits
//     onLoadStart:    false,                  // function/bool;   calls function when the image load begins
//     onLoadEnd:      false                   // function/bool;   calls function when the image finishes loading
// );
