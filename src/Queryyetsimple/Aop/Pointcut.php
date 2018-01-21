<?php

namespace Queryyetsimple\Aop;

// Aspect（切面）：横向切面关系被成组的放进一个类中。
// Advice（通知）：用于调用切面，定义某种情况下做什么和什么时间做这件事情。通知又分为：前通知、返回后通知、抛出后通知和周边通知。
// Joinpoint（接入点）：创建通知的位置。
// Pointcut（点切割）：定义了一种把通知匹配到某些接入点的方式。

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
use ReflectionClass;

class Pointcut {

    protected $cachepath;

    protected $container;

    public function __construct($container) {
        $this->container = $container; 
    }

    CONST AOP_DEFORE = 1;
    CONST AOP_AFTER = 2;

    //protected $methods ;

    public function parse($aopclass,$methods,$code) {     

        $classref = new ReflectionClass($aopclass);

       $file = $classref->getfilename();

        $parser        = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $traverser     = new NodeTraverser;
        $prettyPrinter = new Standard;

$stmts2 = $parser->parse('<'.'?php '.$code.';');

$methods = [$methods];

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
                
                    $return = array_pop($this->stmts);

                    $item2 = $return->expr;



                    foreach ($methods as $item) {



                        $methodName = $item->name->name;
              
                        if(in_array($methodName, $this->methods)) {



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

                            
                            array_unshift($item->stmts, $expression);
                           // print_r($item);
                        }
                    }
                }
            }
        });

        try {
                $code = file_get_contents($file);

                // parse
                $stmts = $parser->parse($code);

                // traverse
                $stmts = $traverser->traverse($stmts);

                // pretty print
                $code = $prettyPrinter->prettyPrintFile($stmts);

               ddd(substr($code,5));// $code;


               $file =  $this->container->pathApplicationCache('aop').'/'.str_replace('\\','/',$aopclass).'.php';
               $dir = dirname($file);

                if(!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }

                file_put_contents($file, $code);

            } catch (Error $e) {
                echo 'Parse Error: ', $e->getMessage();
            }
    }

    protected function parseAdviceClosures($advice){
        return (new closures($advice))->parse();
    }
}

