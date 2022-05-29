<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Zas;

    #uns#


    class FileMaker extends AbstractCommandExecutor  {

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
         * @param string $fileName
         * @param mixed $parentDirName
         * @param string $extension
         * 
         */
        public function make(string $fileName, $parentDirName, $extension = "php"){
            $system = new System();
            $dirName = $system->makeDirectory($this->currentDir.DIRECTORY_SEPARATOR.$parentDirName);

            return $system->createFile($dirName . DIRECTORY_SEPARATOR . $this->toZasName($fileName). ".$extension");
        }
        
    }

?>