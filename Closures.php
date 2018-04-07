<?php

namespace Leevel\Aop;

use ClosureAnalysisException;
use ReflectionFunction;
use SplFileObject;
use RuntimeException;

// https://github.com/jeremeamia/super_closure

class Closures
{

    protected $closure;
    protected $code;
    protected $source = [];
    protected $tokens = [];

    public function __construct($closure) {
        $this->closure = $closure;
    }

    public function parse() {
        $this->parseTokens();        
        return $this->__toString();
    }

    protected function makeReflection() {
        return new ReflectionFunction($this->closure);
    }

    protected function parseTokens()
    {
        $this->parseSourceTokens($this->makeReflection());

        $tokens = [];
        $braceLevel = $step = 0;

        foreach ($this->source as $token) {
            $token = new token($token);

            switch ($step) {

                // Handle tokens before the function declaration.
                case 0:
                    if ($token->is(T_FUNCTION)) {
                        $tokens[] = $token;
                        $step++;
                    }
                    break;

                // Handle tokens inside the function signature.
                case 1:
                    $tokens[] = $token;
                    if ($token->is('{')) {
                        $step++;
                        $braceLevel++;
                    }
                    break;

                // Handle tokens inside the function body.
                case 2:
                    $tokens[] = $token;
                    if ($token->is('{')) {
                        $braceLevel++;
                    } elseif ($token->is('}')) {
                        $braceLevel--;
                        if ($braceLevel === 0) {
                            $step++;
                        }
                    }
                    break;

                // Handle tokens after the function declaration.
                case 3:
                    if ($token->is(T_FUNCTION)) {
                        throw new RuntimeException('Multiple closures were declared on the same line of code.' );
                    }
                    break;
            }
        }

        return $this->tokens = $tokens;
    }




    protected function parseSourceTokens(ReflectionFunction $reflection)
    {
        // Load the file containing the code for the function.
        $fileName = $reflection->getFileName();

        if (!is_readable($fileName)) {
            throw new ClosureAnalysisException(sprintf('Cannot read the file containing the closure: %s', $fileName));
        }

        $code = '';

        $file = new SplFileObject($fileName);
        $file->seek($reflection->getStartLine() - 1);
        while ($file->key() < $reflection->getEndLine()) {
            $code .= $file->current();
            $file->next();
        }
        $code = trim($code);
        if (strpos($code, '<?php') !== 0) {
            $code = "<?php\n" . $code;
        }

        $this->code = $code;

        return $this->source = token_get_all($code);
    }

    public function __toString() {
        return implode('', $this->tokens);
    }
}