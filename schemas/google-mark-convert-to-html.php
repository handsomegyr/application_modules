<?php
$filename = "source.html";
$data = file_get_contents($filename);

$data = preg_replace('/ADD_DATE=\".*\"/i', '', $data);
$data = preg_replace('/<DT><H3.*>(.*?)<\/H3>\r\n.*<DL><p>/i', '<li>$1</li><ul>', $data);
$data = preg_replace('/<\/DL><p>/i', '</ul>', $data);
$data = preg_replace('/<DT>(<A.*?<\/A>)/i', '<li>$1</li>', $data);
// $data = preg_replace('/<ul>\r\n +</ul>/i', '', $data);

// $data = iconv("gbk", "utf-8", $data);

$data = <<<html
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>note</title>
</head>
<body>
$data
</body>
</html>
html;

$filename = "result.html";
if (file_exists($filename)) {
    unlink($filename);
}
file_put_contents($filename, $data);
exit(0);