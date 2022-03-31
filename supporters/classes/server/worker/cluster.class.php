<?php
    // Comments with #.# are required by `zas` for code insertion.

    namespace Server\Worker;

    use \Server;
    use \Server\ServerInterface as sServerInterface;
    use \Human\ServerInterface as hServerInterface;
    use \Test\TestInterface;
    use \Server\ServerTrait as sServerTrait;
    use \Human\ServerTrait as hServerTrait;
    use \Test\TestTrait;
    #uns#


    class Cluster extends Server implements sServerInterface, hServerInterface, TestInterface {

        # use traits
        use sServerTrait;
        use hServerTrait;
        use TestTrait;
        #ut#

        public function __construct(){
            #code...
        }
    }

?>