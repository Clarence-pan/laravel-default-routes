<?php


namespace App\Http\Controllers;


use Clarence\LaravelDefaultRoutes\DefaultRoute;
use Illuminate\Routing\Controller;

class LaravelDefaultRoutesTestController extends Controller
{
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


    public function doGetFoo()
    {
        return __METHOD__;
    }

    public function doPostFoo()
    {
        return __METHOD__;
    }

    public function doPutFoo()
    {
        return __METHOD__;
    }

    public function doDeleteFoo()
    {
        return __METHOD__;
    }

}