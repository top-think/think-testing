<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2015 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: yunwuxin <448901948@qq.com>
// +----------------------------------------------------------------------
namespace think\testing;

use think\App;
use think\Cookie;
use think\helper\Str;
use think\Request;
use think\Response;

trait CrawlerTrait
{
    use InteractsWithPages;

    protected $currentUri;

    protected $serverVariables = [];

    /** @var  Response */
    protected $response;


    public function get($uri, array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);

        $this->call('GET', $uri, [], [], [], $server);

        return $this;
    }

    public function post($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);

        $this->call('POST', $uri, $data, [], [], $server);

        return $this;
    }

    public function put($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);

        $this->call('PUT', $uri, $data, [], [], $server);

        return $this;
    }

    public function delete($uri, array $data = [], array $headers = [])
    {
        $server = $this->transformHeadersToServerVars($headers);

        $this->call('DELETE', $uri, $data, [], [], $server);

        return $this;
    }


    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        $this->currentUri = $this->prepareUrlForRequest($uri);

        $request = Request::create(
            $this->currentUri, $method, $parameters,
            $cookies, $files, array_replace($this->serverVariables, $server)
        );

        $response = App::run($request);

        return $this->response = $response;
    }


    public function seeJson()
    {
        $this->assertJson(
            $this->response->getContent(), "JSON was not returned from [{$this->currentUri}]."
        );

        return $this;
    }


    protected function seeStatusCode($status)
    {
        $this->assertEquals($status, $this->response->getCode());

        return $this;
    }

    protected function seeHeader($headerName, $value = null)
    {
        $headers = $this->response->getHeader();

        $this->assertTrue(!empty($headers[$headerName]), "Header [{$headerName}] not present on response.");

        if (!is_null($value)) {
            $this->assertEquals(
                $headers[$headerName], $value,
                "Header [{$headerName}] was found, but value [{$headers[$headerName]}] does not match [{$value}]."
            );
        }

        return $this;
    }

    protected function seeCookie($cookieName, $value = null)
    {

        $exist = Cookie::has($cookieName);

        $this->assertTrue($exist, "Cookie [{$cookieName}] not present on response.");

        if (!is_null($value)) {
            $cookie = Cookie::get($cookieName);
            $this->assertEquals(
                $cookie, $value,
                "Cookie [{$cookieName}] was found, but value [{$cookie}] does not match [{$value}]."
            );
        }

        return $this;
    }

    protected function withServerVariables(array $server)
    {
        $this->serverVariables = $server;

        return $this;
    }

    protected function transformHeadersToServerVars(array $headers)
    {
        $server = [];
        $prefix = 'HTTP_';

        foreach ($headers as $name => $value) {
            $name = strtr(strtoupper($name), '-', '_');

            if (!Str::startsWith($name, $prefix) && $name != 'CONTENT_TYPE') {
                $name = $prefix . $name;
            }

            $server[$name] = $value;
        }

        return $server;
    }
}