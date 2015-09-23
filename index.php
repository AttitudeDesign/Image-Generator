<?php

	// Declare ourself as a PNG image
	header("Content-Type: image/png");

	// Create the image
	create_image();

	// Close the program
	exit();

	function create_image() {
		// To send to print sizes, per original asset.
		$sizes['print']['w'] = 5315;  
		$sizes['print']['h'] = 3780;

		// Preview size
		$sizes['preview']['w'] = 786;
		$sizes['preview']['h'] = 588;

		// Create print size document, so all additional components are in correct position
		$printCanvas = imagecreatetruecolor($sizes['print']['w'], $sizes['print']['h']);  

		// Colour references, white for foil blocked text (won't be on final print) and black for date and names.
		$colours['white'] = imagecolorallocate($printCanvas, 255, 255, 255);
		$colours['black'] = imagecolorallocate($printCanvas, 0, 0, 0);

		// Initially start with black image
		imagefill($printCanvas, 0, 0, $colours['black']); 

		// Load in print asset
		$printSrc = imagecreatefromjpeg('final.jpg');

		// Add print asset to current canvas
		imagecopymerge($printCanvas, $printSrc, 0, 0, 0, 0, $sizes['print']['w'], $sizes['print']['h'], 100);

		// Once merged, destroy source image
		imagedestroy($printSrc);

		// Add in foil blocked text if in preview mode
		if(isset($_GET['preview'])) {
			imagettftext($printCanvas, 300, 0, 1865, 1225, $colours['white'], '28DaysLater.ttf', 'Established');
			imagettftext($printCanvas, 200, 0, 2418, 2325, $colours['white'], '28DaysLater.ttf', (isset($_GET['title'])?$_GET['title']:'Title here'));
		}

		// If we have a valid information, set it, else just change to "Data here"
		if(isset($_GET['date'])) { $date = $_GET['date']; } else { $date = 'Date here'; }
		if(isset($_GET['names'])) { $names = $_GET['names']; } else { $names = 'Names here'; }
		if(isset($_GET['image'])) { $image = $_GET['image']; } else { $image = 'http://lorempixel.com/1280/1280/people/Sample Image'; }


		// Print names onto the canvas
		imagettftext($printCanvas, 120, 0, 2418, 2450, $colours['black'], 'Rockwell.ttf', $names);

		// Print date onto the canvas, make the end of it match up to the end of "Established" above
		$type_space = imagettfbbox(120, 0, 'Rockwell.ttf', $date);
		imagettftext($printCanvas, 120, 0, 3750-$type_space[4], 1350, $colours['black'], 'Rockwell.ttf', $date);

		// Ascertain if this is a external image, and if not, add on the current server address	
		if(substr($image,0,4)!='http') { $image = 'http://'.$_SERVER['SERVER_NAME'].'/'.$image; }

		// Create image using the supplied image, and log the size
		$imageSrc = imagecreatefromjpeg($image);
		$sizes['image']['w'] = ImageSx($imageSrc);
		$sizes['image']['h'] = ImageSy($imageSrc);

		// Create canvas for the supplied image to sit in
		$imageCanvas = ImageCreateTrueColor(1280,1280);

		imagecopyresized($imageCanvas, $imageSrc, 0, 0, 0, 0, 1280, 1280, $sizes['image']['w'], $sizes['image']['h']);

		// Prep the image for rotation
		$canvasTrans = imagecolorallocatealpha($imageCanvas , 0, 0, 0, 127);

		// Rotate image, and log new size
		$imageCanvas = imagerotate($imageCanvas, 5, $canvasTrans);
		$sizes['rotated-image']['w'] = ImageSx($imageCanvas);
		$sizes['rotated-image']['h'] = ImageSy($imageCanvas);

		// Add the rotated supplied image onto the print canvas
		ImageCopyResampled($printCanvas, $imageCanvas, 870, 1650, 0, 0, $sizes['rotated-image']['w'], $sizes['rotated-image']['h'], $sizes['rotated-image']['w'], $sizes['rotated-image']['h']);

		// Once copied, destroy image canvas, and source image
		imagedestroy($imageSrc);
		imagedestroy($imageCanvas);

		// If generating preview, adjust size of output
		if(isset($_GET['preview'])) {

			// Calculate how the aspect ratio should be calculated
			if(($sizes['preview']['w'] - $sizes['print']['w']) > ($sizes['preview']['h'] - $sizes['print']['h'])) {
				$s = $sizes['preview']['h'] / $sizes['print']['h'];
				$nw = round($sizes['print']['w'] * $s);
				$nh = round($sizes['print']['h'] * $s);
			}
			else {
				$s = $sizes['preview']['w'] / $sizes['print']['w'];
				$nw = round($sizes['print']['w'] * $s);
				$nh = round($sizes['print']['h'] * $s);
			}

			// Create canvas to match size of desired preview
			$previewCanvas = imagecreatetruecolor($sizes['preview']['w'], $sizes['preview']['h']); 

			// Copy the whole print canvas onto the preview canvas at the reduced size
			// This also repositions the canvas, as we don't need crop marks in the preview.
			ImageCopyResampled($previewCanvas, $printCanvas, -110, -56, 0, 0, $nw+220, $nh+139, $sizes['print']['w'], $sizes['print']['h']);
			
			// Output the preview canvas
			imagepng($previewCanvas);

			// Destroy the preview and print canvas, as now no longer needed.
			imagedestroy($previewCanvas);
			imagedestroy($printCanvas);
		}
		else {
			// Output the print canvas
			imagepng($printCanvas);

			// Destroy the preview and print canvas, as now no longer needed.
			imagedestroy($printCanvas);
		}
	} 
?>
