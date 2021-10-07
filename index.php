<?php 

    require_once("lib/simple_html_dom.php");
    require_once("phoneMiner.php");

    $html = file_get_html("https://www.mgsm.pl/pl/katalog/");
    $miner = new PhoneMiner();

    //Number of brands to go through
    $brandsNum = 3; //$miner -> getNumberOfBrands($html); //Use ths function go through all brands

    $finalPhoneArr = array();

    //Loop to go through all brands
    for($i = 0 ; $i < $brandsNum ; $i++){

        //Get html of brand to get number of available pages to scrap
        $brandHtml = $miner -> getBrandHTML($html, $i);
        //Get acces to DOM to get page link
        $brandDom = $miner -> getBrandDOM($html, $i);

        //Loop to go through all available pages
        for($j = 0 ; $j < 1/*$miner -> getNumberOfPages($brandHtml)*/ ; $j++){

            //Number of page
            $pageNum  = $j * 40;
            //Get complete link of current page
            $linkPage = $miner -> getBrandLink($brandDom) . "models/" . $pageNum . "/";
            //Get html of current page
            $pageHtml = file_get_html($linkPage);

            //Number of phones to scrap on current page
            $phonesNum = 1; //$miner -> getNumberOfPhones($pageHtml); //Use ths function to scrap all phones on page

            //Loop to scrap all inforamtion from all phones
            for($k = 0 ; $k < $phonesNum ; $k++){

                //Get phone html
                $phoneLink = $miner -> getPhoneLink($pageHtml, $k);
                $phoneHtml = file_get_html($phoneLink);

                //Add scraped inforamtions to array 
                $phoneInfoArr = array();
                $phoneInfoArr = $miner -> getAllInfo($phoneHtml);

                //Push phone's information to final array
                array_push($finalPhoneArr, $phoneInfoArr);
            }
        }
    }

    //List inforamtions as JSON
    echo json_encode($finalPhoneArr);

?> 