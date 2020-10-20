<?php

$url = "producturl.php?id=736375493?=tm";
$matches = array();
preg_match('/id=([0-9]+)\?/', $url, $matches);
print_r($matches);