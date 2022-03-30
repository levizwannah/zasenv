<?php
    namespace Zas;

    /**
     * Contains all the constants for Zas helper
     */

    class ZasConstants{
        /**
         * Errors
         */
        const ERR_CNE = "The specified class name contains errors or wasn't provided";

        /**
         * Commands constant
         */
        const   Z_MAKE = "make",
                Z_CLASS = "class",
                Z_TRAIT = "trait",
                Z_ABCLASS = "ab-class",
                Z_INFC = "interface",
                Z_CONST = "const-class";

        private function __construct(){}
    }
?>