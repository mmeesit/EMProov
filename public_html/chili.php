<?php
$WebsiteRoot=$_SERVER['DOCUMENT_ROOT'];
include $WebsiteRoot.'/resources/language.php';

$lang = getPreferredLanguage();

$xmlURLs = [
    'https://web2.chilli.ee/index/postimees-main-banner-xml',
    'https://web2.chilli.ee/index/postimees-second-banner-xml',
];

$localizedXmlURLs = preg_filter('/$/', '?locale='.$lang, $xmlURLs);

$scrollTimers = [0, 1500];
$ads = [];
foreach ($localizedXmlURLs as $index => $xmlURL) {
    $ch = curl_init($xmlURL);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Some remote certificates are not nice, sadly
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2049.0 Safari/537.36');
    $content = curl_exec($ch);
    $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($responseCode !== 200) {
        header('HTTP/1.0 504 Gateway Timeout');
        exit;
    }
    if (empty($content)) {
        header('HTTP/1.0 504 Gateway Timeout');
        exit;
    }
    $xml = simplexml_load_string($content, "SimpleXMLElement", 0);
    if (!$xml || empty($xml->product)) {
        header('HTTP/1.0 504 Gateway Timeout');
        exit;
    }
    foreach ($xml->product as $item) {
        $ads[$index][] = $item;
    }
    if (empty($ads[$index])) {
        header('HTTP/1.0 504 Gateway Timeout');
        exit;
    }
    shuffle($ads[$index]);
}
function priceToString($price) {
    $price = (int)trim(strip_tags($price));
    if (empty($price)) return '';
    return number_format($price, 2, ',', ' ');
}
?>
<!DOCTYPE html>
<html lang="et">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body onclick="window.open(getClickTag('https://www.chilli.ee')); return false;">
<div id="header">Chilli.ee</div>
<div id="today"><strong>TÄNA CHILLIS</strong> <?php echo date("d.m.Y") ?></div>
<?php foreach ($ads as $index => $adBlock): ?>
    <div class="adblock-container">
        <a href="#" class="arrow prev" onclick="if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;scrollRight<?php echo $index; ?>();return false;"></a>
        <a href="#" class="arrow next" onclick="if(event.stopPropagation){event.stopPropagation();}event.cancelBubble=true;scrollLeft<?php echo $index; ?>();return false;"></a>
        <div id="ads<?php echo $index; ?>" class="adblock">
            <?php foreach ($adBlock as $ad): ?>
                <a class="ad" href="#"
                   onclick="window.open(getClickTag('<?php echo $ad->url; ?>')); return false;"
                   title="<?php echo trim(strip_tags($ad->description)); ?>">
                    <div class="image" style="background-image: url('<?php echo trim(strip_tags($ad->image)); ?>');"></div>
                    <div class="prices">
                        <div class="current-price"><?php echo priceToString($ad->price); ?></div>
                        <div class="old-price"><?php echo priceToString($ad->old_price); ?></div>
                    </div>
                    <div class="description"><?php echo trim(strip_tags($ad->description)); ?></div>
                </a>
            <?php endforeach;  ?>
        </div>
    </div>
<?php endforeach;  ?>
<script>
    function getQueryStringValue(key) {
        return decodeURIComponent(window.location.search.replace(new RegExp("^(?:.*[&\\?]" + encodeURIComponent(key).replace(/[\.\+\*]/g, "\\$&") + "(?:\\=([^&]*))?)?.*$", "i"), "$1"));
    }
    function getClickTag(url) {
        if (getQueryStringValue("clickTAG")) {
            return getQueryStringValue("clickTAG") + encodeURIComponent(url)
        }
        return url;
    }
    function scrollTo(element, to, duration) {
        if (duration <= 0) return;
        var difference = to - element.scrollLeft;
        var perTick = difference / duration * 10;
        setTimeout(function() {
            element.scrollLeft = element.scrollLeft + perTick;
            if (element.scrollLeft === to) return;
            scrollTo(element, to, duration - 10);
        }, 10);
    }
    <?php foreach ($ads as $index => $adBlock): ?>
    var scrollToElement<?php echo $index; ?> = document.getElementById('ads<?php echo $index; ?>').firstElementChild;
    function scrollRight<?php echo $index; ?>(){
        window.scrollToElement<?php echo $index; ?> = window.scrollToElement<?php echo $index; ?>.previousElementSibling  || document.getElementById('ads<?php echo $index; ?>').lastElementChild;
        scrollTo(document.getElementById('ads<?php echo $index; ?>'), window.scrollToElement<?php echo $index; ?>.offsetLeft, 300);
        return false;
    }
    function scrollLeft<?php echo $index; ?>(){
        window.scrollToElement<?php echo $index; ?> = window.scrollToElement<?php echo $index; ?>.nextElementSibling || document.getElementById('ads<?php echo $index; ?>').firstElementChild;
        scrollTo(document.getElementById('ads<?php echo $index; ?>'), window.scrollToElement<?php echo $index; ?>.offsetLeft, 300);
        return false;
    }
    <?php endforeach;  ?>
</script>
</body>
</html>