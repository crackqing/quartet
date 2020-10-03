<?php

namespace App\Service;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

use Illuminate\Http\Request;

class ExceptionReport
{
    use ApiResponse;

    /**
     * @var Exception
     */
    public $exception;
    /**
     * @var Request
     */
    public $request;

    /**
     * @var
     */
    protected $report;

    /**
     * ExceptionReport constructor.
     * @param Request $request
     * @param Exception $exception
     */
    public function __construct(Request $request, Exception $exception)
    {
        $this->request = $request;
        $this->exception = $exception;
    }

    /**
     * @var array
     */
    public $doReport = [
        AuthenticationException::class => ['未授权',401],
        AuthorizationException::class => ['权限拒绝',403],
        ModelNotFoundException::class => ['该模型未找到',404],
        ValidationException::class => [],
        UnauthorizedHttpException::class => ['未登录或登录状态失效',422],
        MethodNotAllowedHttpException::class => ['访问方式不正确',400],
        NotFoundHttpException::class => ['未定义的HTTP控制器方法',500],
        \Illuminate\Database\QueryException::class  => ['重复添加or删除错误',502]
    ];

    /**
     * @return bool
     */
    public function shouldReturn()
    {
        if (! ($this->request->wantsJson() || $this->request->ajax())) {
            return false;
        }

        foreach (array_keys($this->doReport) as $report) {
            if ($this->exception instanceof $report) {
                $this->report = $report;
                return true;
            }
        }

        return false;
    }

    /**
     * @param Exception $e
     * @return static
     */
    public static function make(Exception $e)
    {
        return new static(\request(),$e);
    }

    /**
     * @return mixed
     */
    public function report()
    {
        if ($this->exception instanceof ValidationException) {
            return $this->failed($this->exception->errors());
        }
        $message = $this->doReport[$this->report];
        return $this->failed($message[0], $message[1]);
    }

    public function prodReport()
    {
        return $this->failed('服务器错误','500');
    }
}
