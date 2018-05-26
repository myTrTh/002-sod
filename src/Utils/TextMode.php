<?php

namespace App\Utils;

class TextMode
{
	private $container;
	public function __construct($container)
	{
		$this->container = $container;
	}

	public function textMode($message){

		// bb tag
		$patternB = "/\[b\](.*?)\[\/b\]/si";
		$message = preg_replace($patternB, "<b>$1</b>", $message);
		$patternB = "/\[i\](.*?)\[\/i\]/si";
		$message = preg_replace($patternB, "<em>$1</em>", $message);
		$patternB = "/\[u\](.*?)\[\/u\]/si";
		$message = preg_replace($patternB, "<span class='bbunderline'>$1</span>", $message);
		$patternB = "/\[s\](.*?)\[\/s\]/si";
		$message = preg_replace($patternB, "<del>$1</del>", $message);
		// $patternB = "/\[left\](.*?)\[\/left\]/si";
		// $message = preg_replace($patternB, "<span class='left'>$1</span>", $message);
		// $patternB = "/\[right\](.*?)\[\/right\]/si";
		// $message = preg_replace($patternB, "<span class='right'>$1</span>", $message);
		// $patternB = "/\[center\](.*?)\[\/center\]/si";
		// $message = preg_replace($patternB, "<span class='center'>$1</span>", $message);
		// $patternB = "/post(?:\:|\/)([0-9]+)/si";
		// $message = preg_replace($patternB, "<a href='/post/$1'>post/$1</a>", $message);		

		// $how_spoiler = substr_count($message, "[spoiler");		
		// $pattern_spoiler = "/\[spoiler\=*?([\s\S]+)?\]([\s\S]+)?\[\/spoiler\]/uiU";
		// for($i=0;$i<$how_spoiler;$i++) {
		// 	$message = preg_replace_callback($pattern_spoiler, function($matches) {
		// 		if($matches[1])
		// 			$head = $matches[1];
		// 		else
		// 			$head = 0;

		// 		if($matches[2])
		// 			$content = trim($matches[2]);
		// 		else
		// 			$content = "";

		// 		if($head === 0) {
		// 			$result = "<div class='spoiler'><span class='sign'>+</span><span class='spoiler-name'> спойлер</span></div><div class='spoiler-body'>".$content."</div>";
		// 		} else {
		// 			$result = "<div class='spoiler'><span class='sign'>+</span><span class='spoiler-name'> ".$head."</span></div><div class='spoiler-body'>".$content."</div>";
		// 		}

		// 		return $result;

		// 	}, $message);
		// }
		// 	// $message = preg_replace($patternB, "<div class='spoiler'><span class='sign'>+</span><span class='spoiler-name'> спойлер</span><div class='spoiler-body'>$1</div></div>", $message);
	
		$pattern_quote = "/(\[quote)(?:\ ?author=([a-zA-Z0-9а-яёА-ЯЁ\_\@]+))?(?:\ ?date=([a-zA-Zа-яёА-ЯЁ0-9\ \-\.\,\:]+))?(?:\ ?post=([0-9]+))?\]([\s\S]+)?(\[\/quote\])/siuU";
		$how_quote = substr_count($message, "[quote");
		for($i=0;$i<$how_quote;$i++) {
			$message = preg_replace_callback($pattern_quote, function($matches) {
				# 1 open quote 2 author 3 date 4 post id 5 message 6 close quote

				$start_quote = $matches[1];
				$content_quote = trim($matches[5]);
				$end_quote = $matches[6];

				if(!$matches[2] and !$matches[3]) {
					$result = "<div class='bookquote'>".$content_quote."</div>";
				} else {

					if($matches[2])
						$author = $matches[2];
					else
						$author = "";

					if($matches[3]) {
						$date = $this->container['dater']->beautiful_date($matches[3]);

						if($matches[2])
							$date = ", ".$date;
					} else {
						$date = "";
					}

					if($matches[4]) {
						$post = $matches[4];
						// $post = "<a class='nostyle' href='/post/".$post."'>".$author.$date."</a>";
						$post = $author.$date;
					} else {
						$post = $author.$date;
					}

					$result = "<div class='quote-author'>".$post."</div>
							   <div class='bookquote'>".$content_quote."</div>";

				}


				return $result;
			}, $message);
		}

		// links //
		$pattern = "/(\[(?:url=|img]))?(https?\:\/\/)?([\.a-zA-Z0-9\-]+\.[a-zA-Z]{2,6}(?:\/(?:[^\s\]\[\'\"\<\>]+)?)?)(?:\])?(?:(.*)(\[\/(?:url|img)\]))?/ui";
		
		$message = preg_replace_callback($pattern, function($matches) {

			$linkfullpath = $matches[2].$matches[3];
			$http = $matches[2];
			$link = $matches[3];

			if(strlen($linkfullpath) > 60) {
				$first = substr($linkfullpath, 0, 40);
				$middle = ".....";
				$end = substr($linkfullpath, -10);
				$modelink = $first.$middle.$end;
			} else {
				$modelink = $linkfullpath;
			}			
			
			if(isset($matches[1])) {
				$stag = $matches[1];

				if(isset($matches[5])) {
					$etag = $matches[5];
					if($stag == '[img]')
						return $stag.$http.$link.$etag;
				}				
			}
			if(isset($matches[4]))
				$content = $matches[4];

			if($stag and $etag) {
				if($content == '')
					return "<a class='link-out' target='_blank' href='http://{$link}'>{$modelink}</a>";
				else
					return "<a class='link-out' target='_blank' href='http://{$link}'>{$content}</a>";
			}

			return "<a class='link-out' target='_blank' href='http://{$link}'>{$modelink}</a>";
		}, $message);

		// // smiles
		// $patternS = "/(\:[a-z]+)/u";
		// $message = preg_replace_callback($patternS, function($matches) {
		// 	$smile = substr($matches[1], 1);
		// 	$path = 'public/images/smiles/'.$smile.'.gif';
		// 	$img_smile = "<img class='smile' src='/{$path}'>";
		// 	if(file_exists($path))
		// 		return $img_smile;
		// 	else
		// 		return $matches[1];
		// }, $message);

		return $message;
	}
}