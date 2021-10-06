<?php 
    require_once("lib/simple_html_dom.php");
    require_once("phoneMiner.php");

    $html = file_get_html("https://www.mgsm.pl/pl/katalog/");

    $site = "https://www.mgsm.pl";

    $miner = new PhoneMiner();

    $brandHtml = $miner -> getBrandHTML($html, 0);
    $modelsNum = $miner -> getNumberOfModels($brandHtml);

    $phoneLink =  $miner -> getPhoneLink($brandHtml,1);
    $phoneHtml = file_get_html($phoneLink);
    $categoryDom = $miner -> getCategoryDOM($phoneHtml, 2);
    //echo $miner -> getNumberOfInfo($phoneHtml);

    $mainDom = $miner -> getMainDOM($phoneHtml);
    //echo $mainDom;

    //$specDom = $miner -> getSpecDOM($categoryDom, 1);
    for($i = 0 ; $i < $miner -> getNumberOfInfo($phoneHtml) ; $i++){
        $infoDom = $miner -> getInfoDOM($phoneHtml, $i);

        echo $miner -> getInfoName($infoDom) . ": " . $miner -> getInfoValue($infoDom) . "<br>";

    }
?>