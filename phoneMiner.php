<?php 

    require_once("lib/simple_html_dom.php");

    class PhoneMiner{

        public function getNumberOfBrands($html){
            return count($html -> find("div[class='large-6 medium-6 small-6 columns brand-box']"));
        }

        public function getBrandDOM($html, $index){
            return $html -> find("div[class='large-6 medium-6 small-6 columns brand-box']", $index);
        }

        public function getBrandHref($brandDOM){
            return $brandDOM -> first_child() -> first_child() -> href;
        }

        public function getBrandLink($brandDOM){
            $brandHref = $this -> getBrandHref($brandDOM);

            return "https://www.mgsm.pl" . $brandHref;
        }

        public function getBrandHTML($html, $index){
            $brandBox = $this -> getBrandDOM($html, $index);
            $brandLink = $this -> getBrandLink($brandBox);

            return file_get_html($brandLink);
        }

        public function getNumberOfModels($brandHtml)
        {
            $numOfModels = $brandHtml -> find("div[id='InfoLine']", 0) -> next_sibling() -> plaintext;
            return intval($numOfModels);
        }

        public function getNumberOfPhones($brandHtml){
            return count($brandHtml -> find("div[class*='phone-item phone-item--wide']"));
        }
        
        public function getNumberOfPages($brandHtml){
            $numOfModels = $this -> getNumberOfModels($brandHtml);
            $numOfPages = intval($numOfModels / 40);
            return $numOfPages;
        }

        public function getPhoneLink($brandHtml, $index){
            $phoneHref = $brandHtml -> find("div[class*='phone-item phone-item--wide']", $index) -> first_child() -> href;
            return "https://www.mgsm.pl" . $phoneHref;
        }

        public function getMainDOM($phoneHtml){
            return $phoneHtml -> find("table[class='PhoneData YesDict']", 0);
        }

        public function getNumberOfInfo($phoneHtml){
            return count($phoneHtml -> find("table[class='PhoneData YesDict']", 0)  -> find("tr"));
        }

        public function getInfoDOM($phoneHtml, $index){
            return $phoneHtml -> find("table[class='PhoneData YesDict']", 0)  -> find("tr", $index);
        }

        public function getInfoName($infoDom){
            return $infoDom -> children(0) -> plaintext;
        }

        public function getInfoValue($infoDom){

            @$valueClass = $infoDom -> children(1) -> first_child() -> class;
            
            if($valueClass == "question"){
                return "Unknown";
            }
            else if($valueClass == "cross"){
                return "Nie";
            }
            else if($valueClass == "tick"){
                return "Tak";
            }
            else{
                return $infoDom -> children(1) -> plaintext;
            }
        }

        public function getNumberOfCategory($phoneHtml){
            return count($phoneHtml -> find("ul[class='PhoneData YesDict']"));
        }

        public function getCategoryDOM($phoneHtml, $index){
            return $phoneHtml -> find("ul[class='PhoneData YesDict']", $index);
        }

        public function getNumberOfSpec($categoryDom){
            return count($categoryDom -> find("li[!class][!style]"));
        }

        public function getSpecDOM($categoryDom, $index){
            return $categoryDom -> find("li[!class][!style]", $index);
        }

        public function getSpecName($specDom){
            return $specDom -> first_child() -> children(0) -> plaintext;
        }

        public function getSpecValue($specDom){

            @$valueClass = $specDom -> first_child() -> children(1) -> first_child() -> class;
            //echo $valueClass;

            if($valueClass == "question"){
                return "Unknown";
            }
            else if($valueClass == "cross"){
                return "Nie";
            }
            else if($valueClass == "tick"){
                return "Tak";
            }
            else{
                return $specDom -> first_child() -> children(1) -> plaintext;
            }

        }

        public function getMainInfoArr($phoneHtml){

            $infoArr = array();

            for($i = 0 ; $i < $this -> getNumberOfInfo($phoneHtml) ; $i++){
                
                $infoDom = $this -> getInfoDOM($phoneHtml, $i);
                
                $infoName = $this -> getInfoName($infoDom);
                $infoValue = $this -> getInfoValue($infoDom);

                $infoName = trim($infoName);
                $infoValue = trim($infoValue);
                $infoValue = str_replace("\r\n","", $infoValue);

                $infoArr[$infoName] = $infoValue;
                
            }

            return $infoArr;

        }

        public function getSpecArr($phoneHtml, $index){

            $specArr = array();

            $categoryDom = $this ->getCategoryDOM($phoneHtml, $index);

            for($i = 0 ; $i < $this -> getNumberOfSpec($categoryDom);$i++){
                $specDom = $this -> getSpecDOM($categoryDom, $i);

                $specName = $this -> getSpecName($specDom);
                $specValue = $this -> getSpecValue($specDom);

                $specName = trim($specName);
                $specValue = trim($specValue);

                $specArr[$specName] = $specValue;
            }

            return $specArr;

        }

        public function getAllSpecArr($phoneHtml){

            $allSpecArr = array();

            for($i = 0 ;  $i < $this -> getNumberOfCategory($phoneHtml) ; $i++){

                $infoSpecArr = $this -> getSpecArr($phoneHtml, $i);

                $allSpecArr = array_merge($allSpecArr , $infoSpecArr);
            }

            return $allSpecArr;

        }

        public function getAllInfo($phoneHtml){

            $allInfoArr = array();

            $mainArr = $this -> getMainInfoArr($phoneHtml);
            $infoArr = $this -> getAllSpecArr($phoneHtml);

            $allInfoArr = array_merge($allInfoArr, $mainArr);
            $allInfoArr = array_merge($allInfoArr, $infoArr);

            return $allInfoArr;

        }

    }

?>