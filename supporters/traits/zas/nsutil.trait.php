<?php
    namespace Zas;

    trait NsUtilTrait {

        /**
         * Splits a name into parts by non-alpha numeric characters.
         */
        public function removeSlashes($name){
            return preg_replace("/(^\W+)|(\W+$)/", "", $name);
        }

        /**
         * Given a fully qualified name, return the directory path component of the name.
         */
        public function homeDir($qualifiedName){
            return preg_replace("/(\W+)?[\w]+$/", "", $qualifiedName);
        } 

        /**
         * Returns the name from the qualified name.
         * for example, levi\zwannah will return zwannah.
         */
        public function getName($qualifiedName){
            $actualName = preg_split("/\W/", $qualifiedName);
            end($actualName);
            return current($actualName);
        }

        /**
         * Capitalizes letters in a string 
         * using $separator = " \t\r\n\f\v'/-.|\\"
         */
        public function capitalizeWords($string){
            return ucwords($string, " \t\r\n\f\v'/-.|\\");
        }

        /**
         * Makes a directory path a valid php namesapce name.
         */
        public function getNamespaceText($name){
            $name = preg_replace("/\W/", "\\", $name);
            if($name[0] !== "\\") $name = "\\".$name;
            
            return $this->capitalizeWords($name);
        }
    }

?>