<?php
 
require_once 'lib/IdentifyUnusedCss.class.php';
 
define('SET_VERBOSE_MODE', true);

$_iucss = new IdentifyUnusedCss();
$_iucss->init(SET_VERBOSE_MODE);
$_iucss->addPageUrl('http://bieli.net/index.html');
$_iucss->addtPageUrl('http://bieli.net/kontakt.html');
$_iucss->addtPageUrl('file:////tmp/test.html');

$_iucss->runScanner();

var_dump(
    $_iucss->getAllCssData()
);

var_dump(
    $_iucss->getRaport(self::PLAIN_TEXT)
);

