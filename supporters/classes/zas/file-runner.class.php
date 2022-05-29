<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Zas;

    #uns#


    class FileRunner extends AbstractCommandExecutor  {

        /**
         * Arguments to pass to the file
         * @var array
         */
        private array $arguments = [];

        # use traits
        #ut#
        /**
         * Set the argument to pass to the file
         * @param array $args
         * 
         * @return \Zas\FileRunner
         */
        public function withArg(array $args){
            $this->arguments = $args;
            return $this;
        }

        /**
         * Runs a background file
         * @param string $filePath
         * 
         * @return \Zas\FileRunner
         */
        public function runFile(string $filePath): \Zas\FileRunner{
            $fullPath = $this->zasConfig->path->actors->background.DIRECTORY_SEPARATOR.$filePath;
            $args = "";
            foreach($this->arguments as $arg){
                if(count(preg_split("/\s+/", $arg, -1, PREG_SPLIT_NO_EMPTY)) > 1) $arg = "\"$arg\"";
                if(!empty($args)) $args .= " ";
                $args .= $arg;
            }

            $cmd = "php .$fullPath $args";
            Cli::log(shell_exec($cmd));
            return $this;
        }
    }

?>