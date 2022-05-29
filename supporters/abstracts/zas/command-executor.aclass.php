<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Zas;

    #uns#


    abstract class AbstractCommandExecutor   {

        /**
         * The root directory as specified in the Zas Config
         * @var string
         */
        protected $rootDir = "";
        
        /**
         * The Zas config object
         * @var object
         */
        protected $zasConfig;

        # use traits
        use NsUtilTrait;
        #ut#

        /**
         * Creates a new maker object.
         * @param object $zasConfig
         */
        public function __construct(object $zasConfig){
            $this->zasConfig = $zasConfig;
            $root = $this->zasConfig->directories->root;

            $parentDir = preg_split("/$root/", __DIR__);
            $this->rootDir = $parentDir[0].DIRECTORY_SEPARATOR."$root";
        }

        /**
         * Creates a directory in a subdirectory
         */
        protected function makeDirectory($path){
            return (new System())->makeDirectory($path);
        }

        protected function getFullPath(string $path){
            return $this->rootDir . $path;
        }

    }

?>