<?php
    # Comments with #...# are required by `zas` for code insertion. Do not remove nor modify them!

    namespace Zas;

    #uns#


    trait FileUtilTrait {
        #ut#

        /**
         * Converts a camel case name into a ZAS qualified name.
         * for example, SomeNamespace/someFile will return some-namespace/some-file.
         * However, the file separator is specified in the zasconfig.json.
         * 
         * takes O(n) time.
         * @param string $fileName
         * 
         * @return string
         */
        public function toZasName(string $fileName, string $separator = "-"): string{
            $fileName = trim($fileName);
            $length = strlen($fileName);
            
            $newStr = "";
            $firstCharMet = false;

            for($i = 0; $i < $length; $i++){

                if($firstCharMet && preg_match("/[A-Z]/", $fileName[$i])){
                    $newStr .= $separator . strtolower($fileName[$i]);
                    continue;
                }

                if(!$firstCharMet){
                    $firstCharMet = preg_match("/[a-zA-Z]/", $fileName[$i]) == true;
                }

                $newStr .= strtolower($fileName[$i]);
            }

            return $newStr;
        }
    }

?>