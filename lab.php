<?php 
    require_once("lib/simple_html_dom.php");
    require_once("phoneMiner.php");

    $html = file_get_html("https://www.mgsm.pl/pl/katalog/");

    $miner = new PhoneMiner();

    $brandsNum = 1; //$miner -> getNumberOfBrands($html);

    $finalPhoneArr = array();

    for($i = 0 ; $i < $brandsNum ; $i++){
        $brandHtml = $miner -> getBrandHTML($html, $i);
        $brandDom = $miner -> getBrandDOM($html, $i);
        for($j = 0 ; $j < 1/*$miner -> getNumberOfPages($brandHtml)*/ ; $j++){
            $pageNum  = $j * 40;
            $linkPage = $miner -> getBrandLink($brandDom) . "models/" . $pageNum . "/";
            $pageHtml = file_get_html($linkPage);
            $phonesNum = $miner -> getNumberOfPhones($pageHtml);
            for($k = 0 ; $k < $phonesNum ; $k++){
                $phoneLink = $miner -> getPhoneLink($pageHtml, $k);
                $phoneHtml = file_get_html($phoneLink);

                $phoneInfoArr = array();

                $phoneInfoArr = $miner -> getAllInfo($phoneHtml);

                array_push($finalPhoneArr, $phoneInfoArr);
            }
        }
    }

    echo json_encode($finalPhoneArr);

    // $dom = $miner -> getBrandDOM($html, 1);
    // echo $miner -> getBrandLink($dom);

    // $site = $miner -> getBrandLink($dom) . "models/" . 40 . "/";

    // echo file_get_html($site);


    //echo json_encode($allSpecArr);

?>