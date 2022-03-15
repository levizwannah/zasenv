<?php

    namespace Zas;
    
    
    /**
     * NsBST (Namespace Binary Search Tree) - For handling namespace name collision.
     * For example, Company\Worker and Server\Worker used in the same file will require that the
     * developer explicitly use the namespace when creating a worker. The NsBst  
     * is able to detect this and return an alias to be used such as `Company\Worker as cWorker` and `Server\Worker as sWorker` to be used
     * at the top of the file for namespace usage declaration.
     */
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
                          $current->addNs($node->namespace);
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

        public function _inorder(Node $node, &$container) {

                        if(!empty($node->left)) {
                           $this->_inorder($node->left, $container); 
                        } 

                        $node->putQUsageName($container);

                        if(!empty($node->right)) {
                           $this->_inorder($node->right, $container); 
                        } 
        }


        public function _preorder(Node $node, &$container) {

                        
                        $node->putQUsageName($container);
                        
                        if(!empty($node->left)) {
                           $this->_preorder($node->left, $container); 
                        } 


                        if(!empty($node->right)) {
                           $this->_preorder($node->right, $container); 
                        } 
        }


        public function _postorder(Node $node, &$container) {


                        if(!empty($node->left)) {
                           $this->_postorder($node->left, $container); 
                        } 


                        if(!empty($node->right)) {
                           $this->_postorder($node->right, $container); 
                        } 

                        $node->putQUsageName($container);

        }
    }

?>