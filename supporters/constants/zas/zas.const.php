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
        const   ZC_MAKE = "make",
                ZC_CLASS = "class",
                ZC_TRAIT = "trait",
                ZC_ABCLASS = "ab-class",
                ZC_INFC = "interface",
                ZC_CONST = "const-class";

        /**
         * Dash constants 
         */
        const   DASH_I = "-i",
                DASH_P = "-p",
                DASH_T = "-t",
                DASH_E = "-e",
                DASH_DASH_F = "--f";
        
        /**
         * Constants from the zas-config file
         */
        const   ZCFG_CLASS = "class",
                ZCFG_IFC   = "interface",
                ZCFG_ACLASS = "abstractClass",
                ZCFG_TRAIT = "trait",
                ZCFG_CONST = "constantsClass";

        private function __construct(){}
    }
?>