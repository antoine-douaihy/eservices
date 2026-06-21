<?php

use Illuminate\Container\Container;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request as RequestFacade;
use Illuminate\Support\Facades\View;

if (! function_exists('view')) {
    /**
     * @param string|null $view
     * @param array<string,mixed> $data
     * @param array<string,mixed> $mergeData
     * @return mixed
     */
    function view($view = null, $data = [], $mergeData = [])
    {
        return View::make($view, $data, $mergeData);
    }
}

if (! function_exists('request')) {
    /**
     * @param string|null $key
     * @param mixed $default
     * @return Request|mixed
     */
    function request($key = null, $default = null)
    {
        $request = RequestFacade::instance();

        return $key === null ? $request : $request->input($key, $default);
    }
}

if (! function_exists('now')) {
    /**
     * @return Carbon
     */
    function now()
    {
        return Carbon::now();
    }
}

if (! function_exists('back')) {
    /**
     * @return RedirectResponse
     */
    function back()
    {
        return Redirect::back();
    }
}

if (! function_exists('redirect')) {
    /**
     * @param string|null $to
     * @param int $status
     * @param array<string,string> $headers
     * @param bool|null $secure
     * @return RedirectResponse
     */
    function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        return $to === null
            ? Redirect::to('/')
            : Redirect::to($to, $status, $headers, $secure);
    }
}

if (! function_exists('config')) {
    /**
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    function config($key = null, $default = null)
    {
        return $key === null ? Config::all() : Config::get($key, $default);
    }
}

if (! function_exists('app')) {
    /**
     * @param string|null $abstract
     * @param array<string,mixed> $parameters
     * @return mixed
     */
    function app($abstract = null, array $parameters = [])
    {
        $container = Container::getInstance();

        return $abstract ? $container->make($abstract, $parameters) : $container;
    }
}

if (! function_exists('abort')) {
    /**
     * @param int $code
     * @param string $message
     * @param array<string,string> $headers
     * @return never
     */
    function abort($code, $message = '', array $headers = [])
    {
        app()->abort($code, $message, $headers);
    }
}

if (! function_exists('abort_if')) {
    /**
     * @param bool $boolean
     * @param int $statusCode
     * @param string $message
     * @param array<string,string> $headers
     * @return void
     */
    function abort_if($boolean, $statusCode = 403, $message = '', array $headers = [])
    {
        if ($boolean) {
            abort($statusCode, $message, $headers);
        }
    }
}
