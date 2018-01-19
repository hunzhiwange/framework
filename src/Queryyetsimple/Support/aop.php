<?php
namespace queryyetsimple\support;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Stmt\Expression;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Error;


class aop {

    protected $container;

    public function __construct($container) {
        $this->container = $container; 
    }

    CONST AOP_DEFORE = 1;
    CONST AOP_AFTER = 2;

    //protected $methods ;

    public function parse($aopclass,$file,$methods) {
        if (! is_file($file)) {

        }

       // $this->methods = $methods;

       // ddd() ;
       // 
            
       

        $parser        = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser     = new NodeTraverser;
        $prettyPrinter = new Standard;


$code3 = <<<'eot'
<?php 
use qys\psr4;
use qys\psr5;
use qys\psr8;

//defined('hello', '5');

return [
    'home\app\controller\hello' => [
        'testBeforAdd1' => [
            'before' => function() {
                echo psr4::file('hello');
                echo 'before call';
            }
        ]
    ]
];
eot;
 
$stmts2 = $parser->parse($code3);

        // add your visitor
        $traverser->addVisitor(new class($methods, $stmts2) extends NodeVisitorAbstract {
            private $methods =[];
            private $stmts;
            public function __construct($methods,$stmts) {
                $this->methods = $methods;
                $this->stmts = $stmts;
            }   

            public function enterNode(Node $node) {

                
                if ($node instanceof Namespace_) {
                    $class = $node->stmts[0];

                    $methods = $class->stmts;

                    //print_r($this->methods);
                    //
                    

                    $return = array_pop($this->stmts);
                    
                    $item2 = $return->expr->items[0]->value->items[0]->value->items[0]->value;

                    array_unshift($node->stmts,...$this->stmts );

                    foreach ($methods as $item) {
                        $methodName = $item->name->name;
                        //echo $methodName;
                        //var_dump($this->methods[$methodName]);
                        if(isset($this->methods[$methodName])) {
                            echo 'i am here ';

                            $name = new Name('call_user_func');

                            //$argsvalue = new String_('a');
                            $argsvalue = $item2;

                            $args = new Arg(
                                $argsvalue
                                //new class ('a') extends \PhpParser\Node\Scalar\String_ {}
                            );

                            $call = new FuncCall($name, [$args]);

                            $expression = new Expression($call);

                            //print_r($item);
                            array_unshift($item->stmts, $expression);
                        }
                    }
//print_r($methods);
                    // if('testFunc1' == $node->name->name) {
                    //     echo '2222';

                    //     $name = new Name('call_user_func');

                    //     $argsvalue = new String_('a');

                    //     $args = new Arg(
                    //         $argsvalue
                    //         //new class ('a') extends \PhpParser\Node\Scalar\String_ {}
                    //     );

                    //     $call = new FuncCall($name, [$args]);

                    //     $expression = new Expression($call);

                    //     array_unshift($node->stmts, $expression);
                    // }
                }
            }
        });

        try {
            //$code = file_get_contents($fileName);
            $code = <<<'eot'
<?php 
function a() {
    echo 'i am here';
}


function a2() {
    //call_user_func('b');
    echo '11';
}

function testFunc1(){
   // call_user_func('a');
    echo 'aop_add_before <br/>';
}
eot;
$code = file_get_contents($file);




                // parse
                $stmts = $parser->parse($code);

                // traverse
                $stmts = $traverser->traverse($stmts);

                // pretty print
                $code = $prettyPrinter->prettyPrintFile($stmts);

             //  ddd(substr($code,5));// $code;

               //$file =app()->pathApplicationCache('aop', '22'); 

             //  echo $aopclass;

               $file =  $this->container->pathApplicationCache('aop').'/'.str_replace('\\','/',$aopclass).'.php';
               $dir = dirname($file);

              // echo $file;

                if(!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                //file_put_contents($file, $code);

               //file_put_contents(,$code);
               //
               

//exit();
              //  eval(substr($code, 5));
            } catch (Error $e) {
                echo 'Parse Error: ', $e->getMessage();
            }
    }
}

