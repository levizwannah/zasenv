<?php

    namespace Zas;

    class NsBST {        
        /**
         * root
         *
         * @var Node
         */
        public $root;
  
        # traits
        use NsUtilTrait;
        #ut#

        public function  __construct() {}

        public function insert($qualifiedName) {
               $node = new Node($qualifiedName);

               if(empty($this->root)) {

                  $this->root = new Node($qualifiedName);

               } else {

                  $current = $this->root;

                  while(true) {

                        if($node->name < $current->name) {
                       
                              if(!empty($current->left)) {
                                 $current = $current->left;
                              } else {
                                 $current->left = $node;
                                 break; 
                              }

                        } else if($node->name > $current->name){

                              if(!empty($current->right)) {
                                 $current = $current->right;
                              } else {
                                 $current->right = $node;
                                 break; 
                              }

                        } else {
                          $current->namespace[] =  $node->namespace;
                          break;
                        }
                  } 
               }
        }

        public function traverse($method, &$container) {

               switch($method) {

                   case 'inorder':
                   $this->_inorder($this->root, $container);
                   break;

                   case 'postorder':
                   $this->_postorder($this->root, $container);
                   break;
  
                   case 'preorder':
                   $this->_preorder($this->root, $container);
                   break;
 
                   default:
                   break;
               } 

        } 

        public function _inorder($node, &$container) {

                        if(!empty($node->left)) {
                           $this->_inorder($node->left, $container); 
                        } 

                        //echo $node. " ";
                        $container[] = $node->__toString();

                        if(!empty($node->right)) {
                           $this->_inorder($node->right, $container); 
                        } 
        }


        public function _preorder($node, &$container) {

                        //echo $node. " ";
                        $container[] = $node->__toString();

                        if(!empty($node->left)) {
                           $this->_preorder($node->left, $container); 
                        } 


                        if(!empty($node->right)) {
                           $this->_preorder($node->right, $container); 
                        } 
        }


        public function _postorder($node, &$container) {


                        if(!empty($node->left)) {
                           $this->_postorder($node->left, $container); 
                        } 


                        if(!empty($node->right)) {
                           $this->_postorder($node->right, $container); 
                        } 

                        //echo $node. " ";
                        $container[] = $node->__toString();

        }
    }

?>