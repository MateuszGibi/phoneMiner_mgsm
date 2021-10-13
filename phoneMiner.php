<?php 

    require_once("lib/simple_html_dom.php");

    class PhoneMiner{

        public function getNumberOfBrands($html){
            return count($html -> find("div[class='large-6 medium-6 small-6 columns brand-box']"));
        }

        public function getBrandDOM($html, $index){
            return $html -> find("div[class='large-6 medium-6 small-6 columns brand-box']", $index);
        }

        private function getBrandHref($brandDOM){
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

        private function getNumberOfModels($brandHtml)
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

        private function getMainDOM($phoneHtml){
            return $phoneHtml -> find("table[class='PhoneData YesDict']", 0);
        }

        private function getNumberOfInfo($phoneHtml){
            return count($phoneHtml -> find("table[class='PhoneData YesDict']", 0)  -> find("tr"));
        }

        private function getInfoDOM($phoneHtml, $index){
            return $phoneHtml -> find("table[class='PhoneData YesDict']", 0)  -> find("tr", $index);
        }

        private function getInfoName($infoDom){
            return $infoDom -> children(0) -> plaintext;
        }

        private function getInfoValue($infoDom){
            
            //@ is for hide any warnings if value dont have class name
            @$valueClass = $infoDom -> children(1) -> first_child() -> class;
            
            //Some values are listed as image
            //To know witch string we need to return, we check name of value's class
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

        private function getProducerInfo($phoneHtml){
            $infoDom = $this -> getInfoDOM($phoneHtml, 0);
                
            $infoName = $this -> getInfoName($infoDom);
            $infoValue = $this -> getInfoValue($infoDom);

            //Remove all unnecessary spaces and tags
            $infoName = trim($infoName);
            $infoValue = trim($infoValue);
            $infoValue = str_replace("\r\n","", $infoValue);

            //Remove unnecessary substring from producer name
            $infoValue = substr($infoValue, -strpos($infoValue, "Zobacz"));

            $producerInfo = array();
            $producerInfo[$infoName] = $infoValue;
            return $producerInfo;
        }

        private function getNumberOfCategory($phoneHtml){
            return count($phoneHtml -> find("ul[class='PhoneData YesDict']"));
        }

        private function getCategoryDOM($phoneHtml, $index){
            return $phoneHtml -> find("ul[class='PhoneData YesDict']", $index);
        }

        private function getNumberOfSpec($categoryDom){
            return count($categoryDom -> find("li[!class][!style]"));
        }

        private function getSpecDOM($categoryDom, $index){
            return $categoryDom -> find("li[!class][!style]", $index);
        }

        private function getSpecName($specDom){
            return $specDom -> first_child() -> children(0) -> plaintext;
        }

        private function getSpecValue($specDom){

            //@ is for hide any warnings if value dont have class name
            @$valueClass = $specDom -> first_child() -> children(1) -> first_child() -> class;

            //Some values are listed as image
            //To know witch string we need to return, we check name of value's class
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

        private function getMainInfoArr($phoneHtml){

            $infoArr = array();

            //Add producer info to array
            $infoArr = array_merge($infoArr, $this -> getProducerInfo($phoneHtml));

            //Loop to go through all information in main section
            for($i = 1 ; $i < $this -> getNumberOfInfo($phoneHtml) ; $i++){
                
                $infoDom = $this -> getInfoDOM($phoneHtml, $i);
                
                $infoName = $this -> getInfoName($infoDom);
                $infoValue = $this -> getInfoValue($infoDom);

                //Remove all unnecessary spaces and tags
                $infoName = trim($infoName);
                $infoValue = trim($infoValue);
                $infoValue = str_replace("\r\n","", $infoValue);

                $infoArr[$infoName] = $infoValue;
                
            }

            //Remove unnecessary substring from information about standards
            $infoArr["Standardy"] = str_replace("czytaj więcej", "", $infoArr["Standardy"]);

            return $infoArr;

        }

        private function getSpecArr($phoneHtml, $index){

            $specArr = array();

            $categoryDom = $this ->getCategoryDOM($phoneHtml, $index);

            //Loop to go through all spec information in category of given index 
            for($i = 0 ; $i < $this -> getNumberOfSpec($categoryDom);$i++){
                $specDom = $this -> getSpecDOM($categoryDom, $i);

                $specName = $this -> getSpecName($specDom);
                $specValue = $this -> getSpecValue($specDom);

                //Remove all unnecessary spaces and tags
                $specName = trim($specName);
                $specValue = trim($specValue);

                $specArr[$specName] = $specValue;
            }

            return $specArr;

        }

        private function getAllSpecArr($phoneHtml){

            $allSpecArr = array();

            //Loop to go through all categories           
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

            //Merge all arrays to one
            $allInfoArr = array_merge($allInfoArr, $mainArr);
            $allInfoArr = array_merge($allInfoArr, $infoArr);

            return $allInfoArr;

        }

    }

?>