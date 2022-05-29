<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Zas;

    #uns#


    class FolderMaker extends AbstractCommandExecutor  {

        # use traits
        #ut#
        /**
         * The current main directory from root in which we are making the folder
         * @var string
         */
        private string $currentDir;

        /**
         * set the root folder (actors or supporters)
         * @param string $rootDir
         * 
         * @return self
         */
        public function in(string $rootDir){
            $this->currentDir = $this->getFullPath($rootDir);
            return $this;
        }

        /**
         * Make the folder in the root folder
         * @param string $folderName
         * 
         */
        public function make(string $folderName){
            return (new System())->makeDirectory($this->currentDir. DIRECTORY_SEPARATOR . $this->toZasName($folderName));
        }
    }

?>