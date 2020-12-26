<?php

declare(strict_types=1);

namespace Tests\Kernel;

use Leevel\Kernel\Exceptions\HttpException as ExceptionHttpException;
use RuntimeException;
use Tests\Kernel\Exception\BusinessException;
use Tests\Kernel\Exception\HttpException;
use Tests\TestCase;

class ExceptionTest extends TestCase
{
    public function testBaseUse(): void
    {
        $e = new HttpException(500, 'hello', 4000);

        $this->assertInstanceof(RuntimeException::class, $e);
        $this->assertSame('hello', $e->getMessage());
        $this->assertSame(4000, $e->getCode());
        $this->assertSame(500, $e->getStatusCode());

        $e->setStatusCode(404);
        $this->assertSame(404, $e->getStatusCode());
        $this->assertSame([], $e->getHeaders());

        $e->setHeaders(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $e->getHeaders());
    }

    /**
     * @dataProvider getHttpExceptionData
     *
     * @param string $code
     */
    public function testHttpException(string $exception, int $code, string $message): void
    {
        $exceptionName = 'Tests\\Kernel\\Exception\\'.$exception;
        $e = new $exceptionName($message);

        $this->assertInstanceof(RuntimeException::class, $e);
        $this->assertSame('hello '.$exception, $e->getMessage());
        $this->assertSame($code, $e->getStatusCode());
        $this->assertSame([], $e->getHeaders());

        $e->setHeaders(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $e->getHeaders());
    }

    public function getHttpExceptionData()
    {
        return [
            ['BadRequestHttpException', 400, 'hello BadRequestHttpException'],
            ['ForbiddenHttpException', 403, 'hello ForbiddenHttpException'],
            ['InternalServerErrorHttpException', 500, 'hello InternalServerErrorHttpException'],
            ['MethodNotAllowedHttpException', 405, 'hello MethodNotAllowedHttpException'],
            ['NotFoundHttpException', 404, 'hello NotFoundHttpException'],
            ['TooManyRequestsHttpException', 429, 'hello TooManyRequestsHttpException'],
            ['UnauthorizedHttpException', 401, 'hello UnauthorizedHttpException'],
            ['UnprocessableEntityHttpException', 422, 'hello UnprocessableEntityHttpException'],
        ];
    }

    public function testBusinessException(): void
    {
        $e = new BusinessException('hello', 500000, 5);
        $this->assertInstanceof(RuntimeException::class, $e);

        $this->assertSame('hello', $e->getMessage());
        $this->assertSame(500000, $e->getCode());
        $this->assertSame(5, $e->getImportance());
        $e->setImportance(10);
        $this->assertSame(10, $e->getImportance());
    }

    public function testHttpExceptionReportable(): void
    {
        $e = new class(0) extends ExceptionHttpException {
        };
        $this->assertFalse($e->reportable());
    }
}
