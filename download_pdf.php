<?php
	// 設定ファイルの読み込み
	require_once(__DIR__ . '/config.php');
	require_once(__DIR__ . '/lib/phpQuery-onefile.php');
	
	// PDF一覧ページ読み込み
	$source = mb_convert_encoding(file_get_contents($pdf_root), 'UTF-8', 'auto');
	$doc = phpQuery::newDocumentHTML($source);
	
	$links = $doc['div#tmp_contents > ul > li > a'];
	
	// PDF個別ページへのリンクの取得
	foreach($links as $a_tag) {
		$base_url = parse_url($pdf_root);
		$pdf_page_url = $base_url['scheme'] . '://' . $base_url['host'] . pq($a_tag)->attr('href');
		
		// PDFファイルへのリンクの取得
		$source2 = mb_convert_encoding(file_get_contents($pdf_page_url), 'UTF-8', 'auto');
		$doc2 = phpQuery::newDocumentHTML($source2);
		$links2 = $doc2['div#tmp_contents > ul > li > span > a'];
		$pdf_url = $base_url['scheme'] . '://' . $base_url['host'] . pq($links2)->attr('href');
		
		// PDFファイルのダウンロード
		$pdf_data = file_get_contents($pdf_url);
		$pdf_save_path = __DIR__ . '/pdf/' . basename($pdf_url);
		file_put_contents($pdf_save_path, $pdf_data);
	}
?>
