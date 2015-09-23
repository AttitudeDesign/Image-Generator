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


	if(!isset($_GET['image'])) { $src2 = imagecreatefromjpeg('http://lorempixel.com/1280/1280/people/Sample Image'); }
	else {
		if(substr($_GET['image'],0,5)!='http:') { $_GET['image'] = 'http://'.$_SERVER['SERVER_NAME'].'/'.$_GET['image']; }
		$src2 = imagecreatefromjpeg($_GET['image']);
	}
	$imgCont = ImageCreateTrueColor(1280,1280);

	$src2width = ImageSx($src2);
	$src2height = ImageSy($src2);
	imagecopyresized($imgCont, $src2, 0, 0, 0, 0, 1280, 1280, $src2width, $src2height);


	imagesavealpha($imgCont , true);
	$src2Transparency = imagecolorallocatealpha($imgCont , 0, 0, 0, 127);
	imagefill($imgCont , 0, 0, $src2Transparency);

	$imgCont = imagerotate($imgCont, 5, $src2Transparency);
	$imgContwidth = ImageSx($imgCont);
	$imgContheight = ImageSy($imgCont);
	imagealphablending($imgCont, false);
	imagesavealpha($imgCont, true);
	$src2width = ImageSx($imgCont);
	$src2height = ImageSy($imgCont);
	ImageCopyResampled($image, $imgCont, 870, 1650, 0, 0, $src2width, $src2height, $src2width, $src2height);


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
		ImageCopyResampled($finalImage, $image, -110, -55, 0, 0, $nw+220, $nh+110, $width, $height);

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
