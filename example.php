<?php
require("Filterss.php");

$f = new Filterss();

$ignore = [
    'dc:creator' => [
        'itinvest',
    ],
];

$f->loadFromUrl("https://habrahabr.ru/rss/");
$f->filter(function(DOMElement $element) use($ignore) {
    /** @var DOMElement $item */
    foreach ($element->getElementsByTagName('*') as $item) {
        if($item->nodeName == 'dc:creator' && in_array($item->nodeValue, $ignore['dc:creator'])) {
           return false;
        }
    }

    return true;
});
echo $f->out();