<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Upload {

	private $container;
	private $pre_path;

	public function __construct($container)
	{
		$this->container = $container;

		$this->pre_path = $this->container['config']['filepath'];
	}


	public function upload(UploadedFile $file, string $directory,  int $fileSize, int $mustBeWidth = 1000, int $mustBeHeight = 1000) {

		# create file name
		$fileName = substr(sha1(uniqid()), 0, 15);
		
		# guess file expansion
		if($file->guessExtension() == 'jpeg')
			$exp = 'jpg';
		else if($file->guessExtension() == 'gif')
			$exp = 'gif';
		else if($file->guessExtension() == 'png')
			$exp = 'png';
		else
			return array(false, 'Не поддерживаемый формат изображения');

		# check file size
		// echo 'file size: '.$file->getSize();
		if($file->getSize() > $fileSize)
			return array(false, 'Превышен максимальный размер изображения');

		$fullName = $fileName.".".$exp;

		$file->move($this->pre_path.$directory, $fullName);

		$this->resize($this->pre_path.$directory."/".$fullName, $mustBeWidth, $mustBeHeight);
		
		return array(true, $fullName);
	}

	# delete file
	public function delete($image = null, $directory) {

		if ($image == null)
			return;
		$fullPath = $this->pre_path.$directory."/".$image;
		if(file_exists($fullPath))
			unlink($fullPath);
	}

	# resize file
	private function resize($rowimage, $mustBeWidth, $mustBeHeight) {
		$info = getimagesize($rowimage); //получаем размеры картинки и ее тип

		# Если размеры не превышают допустимой нормы, возвращаем изображение как есть
		if($info[0] <= $mustBeWidth and $info[1] <= $mustBeHeight)
			return false;

		# Создаем размеры будущей картинки
		$wper = $info[0] / $info[1]; 
		$hper = $info[1] / $info[0]; 
		if($info[0] > $info[1]) {
			$width = $mustBeWidth;
			$height = $width * $hper;
		} else if ($info[0] < $info[1]) {
			$height = $mustBeHeight;
			$width = $height * $wper;
		} else {
			$width = $mustBeWidth;
			$height = $mustBeHeight;
		}

		$size = array($info[0], $info[1]); //закидываем размеры в массив

	    //В зависимости от расширения картинки вызываем соответствующую функцию
		if ($info['mime'] == 'image/png') {
			$src = imagecreatefrompng($rowimage); //создаём новое изображение из файла
		} else if ($info['mime'] == 'image/jpeg') {
			$src = imagecreatefromjpeg($rowimage);
		} else if ($info['mime'] == 'image/gif') {
	 		return false;
		}

		$thumb = imagecreatetruecolor($width, $height); //возвращает идентификатор изображения, представляющий черное изображение заданного размера

		imagecopyresampled($thumb, $src, 0, 0, 0, 0, $width, $height, $info[0], $info[1]);
		//Копирование и изменение размера изображения с ресемплированием

	    //В зависимости от расширения картинки вызываем файл для записи
		if ($info['mime'] == 'image/png') {
			imagepng($thumb, $rowimage);
		} else if ($info['mime'] == 'image/jpeg') {
			imagejpeg($thumb, $rowimage);
		}
	}
}