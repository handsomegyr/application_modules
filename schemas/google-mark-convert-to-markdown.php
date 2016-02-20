<?php
$filename = "source.html";
$data = file_get_contents($filename);

$data = preg_replace('/ADD_DATE=\".*\"/i', '', $data);
$data = preg_replace('/<DT><H3.*>(.*?)<\/H3>\r\n.*<DL><p>/i', '- $1', $data);
$data = preg_replace('/<\/DL><p>/i', '', $data);
$data = preg_replace('/<DT>(<A.*?<\/A>)/i', '* $1', $data);
$data = preg_replace('/<A HREF=\"(.*?)\".*>(.*?)<\/A>/i', '[$2]($1)', $data);
$data = preg_replace('/                              /i', '', $data);
// $data = preg_replace('/<ul>\r\n +</ul>/i', '', $data);

// $data = iconv("gbk", "utf-8", $data);

$filename = "result.html";
if (file_exists($filename)) {
    unlink($filename);
}
file_put_contents($filename, $data);
exit(0);