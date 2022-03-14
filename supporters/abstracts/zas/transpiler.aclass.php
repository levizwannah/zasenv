<?php
     namespace Zas;
    /**
     * Takes the code in the template file and converts it to a php code.
     */

     abstract class AbstractTranspiler {         
         /**
          * changeMap
          * The map containing the expressions to be changed in the template file and what to change them to.
          * @var array
          */
         protected $changeMap;         
         /**
          * templatePath
          * The path from which to load the template code (Absolute path)
          * @var string
          */
         protected $templatePath;
                  
         /**
          * templateCode
          * The code gotten from the template file
          * @var string
          */
         protected $templateCode;
                  
         /**
          * phpCode
          * The actual php code after transpilation
          * @var string
          */
         protected $phpCode;

         /**
          * __construct
          *
          * @param  array $changeMap - The elements in the template file to change and their changes.
          * This is an associative array containin [change] => [value];
          * for example, in the class template, [CN] => Transpiler
          * @return void
          */
         public function __construct(array $changeMap = [], $templatePath)
         {
             $this->changeMap = $changeMap;
             $this->templatePath = $templatePath;
             $this->loadTemplateCode();
         }

         /**
          * Do the actual transpilation
          */
         abstract function transpile();

         
         /**
          * Get the value of changeMap
          */ 
         public function getChangeMap()
         {
                  return $this->changeMap;
         }

         /**
          * Set the value of changeMap
          *
          * @return  self
          */ 
         public function setChangeMap(array $changeMap)
         {
                  $this->changeMap = $changeMap;

                  return $this;
         }

         /**
          * Get the value of templatePath
          */ 
         public function getTemplatePath()
         {
                  return $this->templatePath;
         }

         /**
          * Set the value of templatePath
          *
          * @return  self
          */ 
         public function setTemplatePath($templatePath)
         {
                  $this->templatePath = $templatePath;
                  $this->loadTemplateCode();
                  return $this;
         }

         /**
          * Get the value of templateCode
          */ 
         public function getTemplateCode()
         {
                  return $this->templateCode;
         }

         /**
          * Get the value of phpCode
          */ 
         public function getPhpCode()
         {
                  return $this->phpCode;
         }
         
         /**
          * loadTemplateCode
          * Loads the template code from the file path specified.
          * @return void
          */
         private function loadTemplateCode(){
             $this->templateCode = file_get_contents($this->templatePath);
         }
     }
?>