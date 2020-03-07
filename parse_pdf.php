<?php
	date_default_timezone_set('Asia/Tokyo');
	
	// 設定ファイルの読み込み
	require_once(__DIR__ . '/config.php');
	
	$csv = '地名,センター（center.csvを使わない場合は空白化）,可燃物,不燃物,スプレー缶 蛍光管等,資源プラ,ペットボトル,缶・びん,古紙・布類' . "\n";
	for($a = 1; $a <= 44; $a++) {
		$area_num = sprintf("%02d", $a);
		$pdf_file_name = __DIR__ . '/pdf/' . $area_num . '.pdf';
		$image_file_path = __DIR__ . '/pdf/' . $area_num;
	
		// ごみの種類と色データ
	
		// 可燃物 RGB(255, 246, 127)
		// 不燃物 RGB(245, 180, 210)
		// スプレー缶 蛍光管等 RGB(206, 224, 116)
		// 資源プラ RGB(188, 178, 217)
		// ペットボトル RGB(217, 174, 72)
		// 缶・びん RGB(242, 153, 135)
		// 古紙・布類 RGB(159, 216, 248)
		$garbage_types = array(
			'可燃物' => array('r' => 255, 'g' => 246, 'b' => 127, 'days' => array()),
			'不燃物' => array('r' => 245, 'g' => 180, 'b' => 210, 'days' => array()),
			'スプレー缶 蛍光管等' => array('r' => 206, 'g' => 224, 'b' => 116, 'days' => array()),
			'資源プラ' => array('r' => 188, 'g' => 178, 'b' => 217, 'days' => array()),
			'ペットボトル' => array('r' => 217, 'g' => 174, 'b' => 72, 'days' => array()),
			'缶・びん' => array('r' => 242, 'g' => 153, 'b' => 135, 'days' => array()),
			'古紙・布類' => array('r' => 159, 'g' => 216, 'b' => 248, 'days' => array()),
			'空欄' => array('r' => 255, 'g' => 255, 'b' => 255, 'days' => array())
		);
	
	//	var_dump($garbage_types);
	
		// 日付配列を作成
		$start_day = $fiscal_year . '-4-1';
		$end_day = ($fiscal_year + 1) . '-3-31';
		$start_time = strtotime($start_day);
		$end_time = strtotime($end_day);
		$arr_j_weekday = array('日', '月', '火', '水', '木', '金', '土');
	
		$arr_date = array();
		$box_x = -1;
		$box_y = 0;
		$cell_x = 0;
		$cell_y = 0;
	
		$last_month = 4;
		$last_day = 1;
		for($time = $start_time; $time <= $end_time; $time += 60*60*24) {
			if(date('d', $time) == 1) {
				// 月が変わった場合
				$cell_x = date('w', $time);
				$cell_y = 0;
				$box_x += 1;
			}
			else if(date('w', $time) == 0) {
				// 週が一週した場合
				$cell_x = 0;
				$cell_y += 1;
			}
		
			// 月の段が変わった場合
			if($box_x == 4) {
				$box_x = 0;
				$box_y += 1;
			}
		
			$arr_date[] = array(
			'time' => $time,
			'date' => date('Y-m-d', $time),
			'date5374' => date('Ymd', $time),
			'y' => date('Y', $time),
			'm' => date('m', $time),
			'd' => date('d', $time),
			'weekday' => date('w', $time),
			'j_weekday' => $arr_j_weekday[date('w', $time)],
			'box_x' => $box_x,
			'box_y' => $box_y,
			'cell_x' => $cell_x,
			'cell_y' => $cell_y,
			);
		
			$cell_x += 1;
		}
	//	var_dump($arr_date);
	
		$imagick = new Imagick();
		$imagick->setresolution(144, 144);
		if(file_exists($pdf_file_name)) { $imagick->readImage($pdf_file_name . '[0]'); } else { continue; }
		
		$draw = new ImagickDraw();
		$draw->setFillColor('purple');
		for($i = 0; $i < count($arr_date); $i++) {
			// 色を取得するべきレクタングル
			$rect_start_x = ceil(90 + $arr_date[$i]['cell_x'] * 76 + $arr_date[$i]['box_x'] * 557.5);
			$rect_start_y = ceil(338 + $arr_date[$i]['cell_y'] * 72.5 + $arr_date[$i]['box_y'] * 456);
			$rect_end_x = floor(94 + $arr_date[$i]['cell_x'] * 76 + $arr_date[$i]['box_x'] * 557.5);
			$rect_end_y = floor(343 + $arr_date[$i]['cell_y'] * 72.5 + $arr_date[$i]['box_y'] * 456);
	
		//					var_dump('box(' . $arr_date[$i]['box_x'] . ', ' . $arr_date[$i]['box_y'] . ') cell(' . $arr_date[$i]['cell_x'] . ', ' . $arr_date[$i]['cell_y'] . ') pixel(' . $rect_start_x . ', ' . $rect_start_y . ')');
	
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
	
		//					var_dump('RGB:(' . $r . ', ' . $g . ', ' . $b . ')');
	
			// ごみの種類ごとに色差判定
			$min_distance = 999999999;
			$min_g_type = '空欄';
			foreach($garbage_types as $g_key => $g_color) {
				$rgb1 = array('r' => $r, 'g' => $g, 'b' => $b);
				$rgb2 = array('r' => $g_color['r'], 'g' => $g_color['g'], 'b' => $g_color['b']);
				$distance = colorDistance($rgb1, $rgb2);
		
				// 色差が最小のごみの種類が正解
				if($distance < $min_distance) {
					$min_distance = $distance;
					$min_g_type = $g_key;
				}
			}
			echo $arr_date[$i]['date']  . ' ' . $min_g_type . "\n";
		
			// 日付を追加
			$garbage_types[$min_g_type]['days'][] = $arr_date[$i]['date5374'];
		
		/*
			$draw->rectangle(
			$rect_start_x,
			$rect_start_y,
			$rect_end_x,
			$rect_end_y);
			$imagick->drawImage($draw);
		*/
		}
	
		var_dump($garbage_types);
	
		// CSVの書き出し
		$csv .= $area_num . ',,' . implode(' ', $garbage_types['可燃物']['days']);
		$csv .= ',' . implode(' ', $garbage_types['不燃物']['days']);
		$csv .= ',' . implode(' ', $garbage_types['スプレー缶 蛍光管等']['days']);
		$csv .= ',' . implode(' ', $garbage_types['資源プラ']['days']);
		$csv .= ',' . implode(' ', $garbage_types['ペットボトル']['days']);
		$csv .= ',' . implode(' ', $garbage_types['缶・びん']['days']);
		$csv .= ',' . implode(' ', $garbage_types['古紙・布類']['days']);
		$csv .= "\n";
		file_put_contents(__DIR__ . '/csv/' . $area_num . '.csv', $csv);
		
		//	$imagick->setImageFormat('png');
		//	$imagick->writeimage($image_file_path . '.png');
	}
	file_put_contents(__DIR__ . '/csv/area_days.csv', $csv);
	
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
	
// RGB色差判定
function colorDistance($rgb1, $rgb2){
  $distance = 0;
  $distance += abs($rgb1['r']   - $rgb2['r']);
  $distance += abs($rgb1['g']   - $rgb2['g']);
  $distance += abs($rgb1['b']   - $rgb2['b']);
  return $distance;
}
?>
