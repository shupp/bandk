<?php

require_once('Net/URL2.php');
require_once('HTTP/Request2.php');

$searchURL  = "http://images.google.com/images?source=hp&q=kittens&btnG=Search+Images&gbv=1";
$userAgent = 'Googlebot/2.1 (http://www.googlebot.com/bot.html)';

function getContents($url, $start = 0, $userAgent = null) {
    if ($start > 0) {
        $url .= "&start=$start";
    }
    $http = new HTTP_Request2($url);
    $http->setAdapter('curl');
    if ($userAgent !== null) {
        $http->setHeader('User-Agent', $userAgent);
    }
    return $http->send();
}

$images = array();
for ($start = 0; $start < 200; $start += 20) {
    // echo $start . "\n";

    $html = getContents($searchURL, $start)->getBody();
    // $html = file_get_contents('images.html');
    $dom = new DOMDocument();
    @$dom->loadHTML($html);

    // grab all the on the page
    $xpath = new DOMXPath($dom);
    $hrefs = $xpath->evaluate('/html/body//div[@id="ImgCont"]//td//a');

    for ($i = 0; $i < $hrefs->length; $i++) {
        $url = $hrefs->item($i)->getAttribute('href');
        list(, $querystring) = explode('?', $url);
        $neturl = new Net_URL2("http://example.com/?$querystring");
        $vars = $neturl->getQueryVariables();
        if (isset($vars['imgurl'])) {
            $images[] = $vars['imgurl'];
        }
    }
}

foreach ($images as $count => $image) {
    preg_match_all('/([.][^.]+)$/', $image, $foo);
    if (!isset($foo[0][0])) {
        continue;
    }
    $ext = strtolower($foo[0][0]);
    $filename = "images/kittens/{$count}{$ext}";
    echo $filename . "\n";

    try {
        $contents = getContents($image, 0, $userAgent)->getBody();
    } catch (HTTP_Request2_Exception $e) {
        echo $e->getMessage() . "\n";
        continue;
    }
    file_put_contents($filename, $contents);
}

?>
