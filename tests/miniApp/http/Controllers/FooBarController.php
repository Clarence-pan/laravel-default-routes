<?php

namespace App\Http\Controllers;

use Clarence\LaravelDefaultRoutes\DefaultRoute;
use Illuminate\Routing\Controller;

class FooBarController extends Controller
{
    use DefaultRoute;

    public function doGetIndex()
    {
        return __METHOD__;
    }

    public function doPostIndex()
    {
        return __METHOD__;
    }

    public function doPutIndex()
    {
        return __METHOD__;
    }

    public function doDeleteIndex()
    {
        return __METHOD__;
    }

    public function doGetJoy()
    {
        return __METHOD__;
    }

    public function doPostJoy()
    {
        return __METHOD__;
    }

    public function doPutJoy()
    {
        return __METHOD__;
    }

    public function doDeleteJoy()
    {
        return __METHOD__;
    }
}
