<?php
	$pdf_file_name = __DIR__ . '/pdf/01.pdf';
	$image_file_path = __DIR__ . '/pdf/01';
	
	$imagick = new Imagick();
	$imagick->setresolution(144, 144);
	$imagick->readImage($pdf_file_name . '[0]');
	
	$draw = new ImagickDraw();
	$draw->setFillColor('purple');
	for($box_x = 0; $box_x <= 3; $box_x++) {
		for($box_y = 0; $box_y <= 2; $box_y++) {
			for($cell_x = 0; $cell_x <= 6; $cell_x++) {
				for($cell_y = 0; $cell_y <= 4; $cell_y++) {
					var_dump('box(' . $box_x . ', ' . $box_y . ') cell(' . $cell_x . ', ' . $cell_y . ')');
					
					// 色を取得するべきレクタングル
					$rect_start_x = ceil(90 + $cell_x * 76 + $box_x * 557.5);
					$rect_start_y = ceil(338 + $cell_y * 72.5 + $box_y * 456);
					$rect_end_x = floor(94 + $cell_x * 76 + $box_x * 557.5);
					$rect_end_y = floor(343 + $cell_y * 72.5 + $box_y * 456);
					
					// ピクセルの取得
					$r = 0; $g = 0; $b = 0;
					for($color_x = $rect_start_x; $color_x <= $rect_end_x; $color_x++) {
						for($color_y = $rect_start_y; $color_y <= $rect_end_y; $color_y++) {
							$pixel = $imagick->getImagePixelColor($color_x, $color_y);
							$colors = $pixel->getColor();
							$r += $colors['r'];
							$g += $colors['g'];
							$b += $colors['b'];
						}
					}
					
					// 範囲内のピクセルの平均色
					$r = round($r / (($rect_end_x - $rect_start_x + 1) * ($rect_end_y - $rect_start_y + 1)));
					$g = round($g / (($rect_end_x - $rect_start_x + 1) * ($rect_end_y - $rect_start_y + 1)));
					$b = round($b / (($rect_end_x - $rect_start_x + 1) * ($rect_end_y - $rect_start_y + 1)));
					
					var_dump('RGB:(' . $r . ', ' . $g . ', ' . $b . ')');
					
					// HSV変換
					list($h, $s, $v) = rgb2hsv($r, $g, $b);
					var_dump('HSV:(' . $h . ', ' . $s . ', ' . $v . ')');
					
					$draw->rectangle(
					$rect_start_x,
					$rect_start_y,
					$rect_end_x,
					$rect_end_y);
					$imagick->drawImage($draw);
				}
			}
		}
	}
	
	$imagick->setImageFormat('png');
	$imagick->writeimage($image_file_path . '.png');
	
	// 可燃物 RGB(255, 246, 127)
	// 不燃物 RGB(245, 180, 210)
	// スプレー缶 蛍光管等 RGB(206, 224, 116)
	// 資源プラ RGB(188, 178, 217)
	// ペットボトル RGB(217, 174, 72)
	// 缶・びん RGB(242, 153, 135)
	// 古紙・布類 RGB(159, 216, 248)
	
	
	// 4/30の左上座標 241, 597
	// 4/30の右下座標 249, 635
	// 間隔 8, 38
	
	// 4/29の左上座標 165, 597
	// 4/29の右下座標 173, 635
	// 間隔 8, 38
	
	// 4/23の左上座標 241, 524
	
	// 4/16の左上座標 241, 452
	
	// 4/9の左上座標 241, 380
	
	// 4/2の左上座標 241, 306
	
	// 4/1の左上座標 165, 306
	
	// 4月の最初のセルの左上座標 89, 307
	
	// 5月の最初のセルの左上座標 647, 307
	
	// 8月の最初のセルの左上座標 89, 763
	
	// 左右に並ぶ日付のピクセル差 76
	// 上下に並ぶ日付セルのピクセル差 72
	
	// 5 * 35 の矩形から色の平均を取る
	
// HSV変換
function rgb2hsv($r, $g, $b){
	$max = max($r, $g, $b);
	$min = min($r, $g, $b);
	$v = $max;

	if($max === $min){
	$h = 0;
	} else if($r === $max){
	$h = 60 * ( ($g - $b) / ($max - $min) ) + 0;
	} else if($g === $max){
	$h = 60 * ( ($b - $r) / ($max - $min) ) + 120;
	} else {
	$h = 60 * ( ($r - $g) / ($max - $min) ) + 240;
	}
	if($h < 0) $h = $h + 360;

	$s = ($v != 0) ? ($max - $min) / $max : 0;

	$hsv = array($h, $s, $v);
	return $hsv;
}
?>
