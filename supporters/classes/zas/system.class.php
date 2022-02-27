<?php
    namespace Zas;

    /**
     * The Object that interacts with the underlying system.
     * The System object 
     *  - creates files in a namespace
     *  
     */

    class System{
        /*
        | constants
        */
        const WINDOWS = 0,
              UNIX = 1;

        /**
         * @var string $OS  The operating system currently running
         * As specified by `System::WINDOWS or UNIX`
         */
        private $OS;

        public function __construct()
        {
            $this->setOs();
        }

        /**
         * Determines the OS PHP is running on.
         */
        private function setOs(){
            $this->OS = System::UNIX;

            if(strtoupper(substr(PHP_OS, 0, 3)) === "WIN"){
                $this->OS  = System::WINDOWS;
            }
        }

        /**
         * Returns the current OS of the system as 0 or 1. 0 Windows, 1 for unix.
         */
        public function getOs(){
            return $this->OS;
        }

        /**
         * Creates a new file
         */
        public function createFile(string $fileName, string $directory = "."){
            $fullPath = $directory . DIRECTORY_SEPARATOR . $fileName;

            if(file_exists($fullPath)) return $fullPath;

            $directory = $this->makeDirectory($directory);

            $output = [];
            $resultCode = null;
            $cmd = "";

            switch($this->OS){
                case System::WINDOWS:
                    {
                        $cmd = "type nul > $fullPath";
                        break;
                    }
                case System::UNIX:
                    {
                        $cmd = "touch $fullPath";
                        break;
                    }
            }

            exec($cmd, $output, $resultCode);

            if($resultCode == 0) return $fullPath;

            return false;
        }

        /**
         * Makes a directory and returns the directory path
         */
        public function makeDirectory(string $dirPath){

            if(is_dir($dirPath)) return realpath($dirPath);

            $output = [];
            $returnCode = null;
            $cmd = "";

            $dirPath = preg_replace("/\/\\/", DIRECTORY_SEPARATOR, $dirPath);

            switch($this->OS){
                case System::WINDOWS:
                    {
                        $cmd = "md $dirPath";
                        break;
                    }
                case System::UNIX:
                    {
                        $cmd = "mkdir -p $dirPath";
                        break;
                    }
            }

            exec($cmd, $output, $returnCode);

            if($returnCode == 0) return realpath($dirPath);
            return false;
        }

        
    }

?>