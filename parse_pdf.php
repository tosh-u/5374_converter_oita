<?php
	$pdf_file_name = __DIR__ . '/pdf/01.pdf';
	$image_file_path = __DIR__ . '/pdf/01';
	
	$imagick = new Imagick();
	$imagick->setresolution(144, 144);
	$imagick->readImage($pdf_file_name . '[0]');
	
	$draw = new ImagickDraw();
	$draw->setFillColor('purple');
	for($box_x = 0; $box_x <= 3; $box_x++) {
		for($box_y = 0; $box_y <= 3; $box_y++) {
			for($cell_x = 0; $cell_x <= 6; $cell_x++) {
				for($cell_y = 0; $cell_y <= 4; $cell_y++) {
					$draw->rectangle(
					90 + $cell_x * 76 + $box_x * 557.5,
					338 + $cell_y * 72.5 + $box_y * 456,
					94 + $cell_x * 76 + $box_x * 557.5,
					343 + $cell_y * 72.5 + $box_y * 456);
					$imagick->drawImage($draw);
				}
			}
		}
	}
	
	$imagick->setImageFormat('png');
	$imagick->writeimage($image_file_path . '.png');
	
	// 可燃物(255, 246, 127)
	// 不燃物(245, 180, 210)
	// スプレー缶 蛍光管等(206, 224, 116)
	// 資源プラ(188, 178, 217)
	// ペットボトル(217, 174, 72)
	// 缶・びん(242, 153, 135)
	// 古紙・布類(159, 216, 248)
	
	
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
?>
