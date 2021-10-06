<?php 

    require_once("lib/simple_html_dom.php");

    class PhoneMiner{

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
            return $infoDom -> children(1) -> plaintext;
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
            return $specDom -> first_child() -> children(1) -> plaintext;
        }

        public function getMainInfoArr($phoneHtml){
            
        }

    }

?>