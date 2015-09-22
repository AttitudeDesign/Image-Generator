<?php

	//Send a generated image to the browser
	create_image();
	exit();

	function create_image() {
	$md5 = md5(rand(0,999)); 

	$fullWidth = 5315;
	$fullHeight = 3780;
	$prevWidth = 786;
	$prevHeight = 588;

	$image = imagecreatetruecolor($fullWidth, $fullHeight);  

	//We are making three colors, white, black and gray
	$white = imagecolorallocate($image, 255, 255, 255);
	$black = imagecolorallocate($image, 0, 0, 0);
	$grey = imagecolorallocate($image, 204, 204, 204);

	imagefill($image, 0, 0, $black); 

	$src = imagecreatefromjpeg('final.jpg');
	imagecopymerge($image, $src, 0, 0, 0, 0, $fullWidth, $fullHeight, 100);
	imageantialias ($image , TRUE);
	if(isset($_GET['preview'])) {
		imagettftext($image, 300, 0, 1865, 1225, $white, '28DaysLater.ttf', 'Established');

		if(isset($_GET['title'])) { $title = $_GET['title']; } else { $title = 'Title here'; }
		$type_space = imagettfbbox(120, 0, 'Rockwell.ttf', $date);
		imagettftext($image, 200, 0, 2418, 2325, $white, '28DaysLater.ttf', $title);
	}

	if(isset($_GET['date'])) { $date = $_GET['date']; } else { $date = 'Date here'; }
	$type_space = imagettfbbox(120, 0, 'Rockwell.ttf', $date);
	imagettftext($image, 120, 0, 3750-$type_space[4], 1350, $black, 'Rockwell.ttf', $date);

	if(isset($_GET['names'])) { $names = $_GET['names']; } else { $names = 'Names here'; }
	imagettftext($image, 120, 0, 2418, 2450, $black, 'Rockwell.ttf', $names);


	$src2 = imagecreatefromjpeg('http://lorempixel.com/1280/1280/people/Sample Image');
	$src2width = ImageSx($src2);
	$src2height = ImageSy($src2);
	//$png = imagecreatetruecolor($src2width, $src2height);
	imagesavealpha($src2 , true);
	$src2Transparency = imagecolorallocatealpha($src2 , 0, 0, 0, 127);
	imagefill($src2 , 0, 0, $src2Transparency);

	$src2 = imagerotate($src2, 5, $src2Transparency);
	$src2width = ImageSx($src2);
	$src2height = ImageSy($src2);
	imagealphablending($src2, false);
	imagesavealpha($src2, true);
	ImageCopyResampled($image, $src2, 870, 1650, 0, 0, $src2width, $src2height, $src2width, $src2height);


	if(isset($_GET['preview'])) {
		$finalImage = imagecreatetruecolor($prevWidth, $prevHeight); 
		$width = ImageSx($image);
		$height = ImageSy($image);
		if(($prevWidth - $width) > ($prevHeight - $height)) { // use height
			$s = $prevHeight / $height;
			$nw = round($width * $s);
			$nh = round($height * $s);
		}
		else {
			$s = $prevWidth / $width;
			$nw = round($width * $s);
			$nh = round($height * $s);
		}

		$finalImage = ImageCreateTrueColor($nw, $nh);
		ImageCopyResampled($finalImage, $image, 0, 0, 0, 0, $nw, $nh, $width, $height);

		header("Content-Type: image/png"); 
		imagepng($finalImage);
		imagedestroy($finalImage);
	}
	else {
		header("Content-Type: image/png"); 
		imagepng($image);
	}

	imagedestroy($image);
	imagedestroy($src);
	} 

?>