<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

if (!function_exists('dd')) {
    /**
     * @param mixed ...$args
     */
    function dd(...$args)
    {
        call_user_func_array('dump', $args);

        exit();
    }
}

if (!function_exists('mock_handler')) {
    /**
     * @return \GuzzleHttp\Client
     */
    function mock_handler($stub)
    {
        $mock = new MockHandler([
            new Response(200, [], $stub),
        ]);
        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }
}
