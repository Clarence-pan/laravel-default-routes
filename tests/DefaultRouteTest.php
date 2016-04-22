<?php


namespace Clarence\LaravelDefaultRoutes;


use App\Http\Controllers\Foo\BarController;
use App\Http\Controllers\Foo\BarzController;
use App\Http\Controllers\FooBarController;
use App\Http\Controllers\FooController;
use App\Http\Controllers\LaravelDefaultRoutesTestController;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase;

class DefaultRouteTest extends TestCase
{
    protected $baseUrl = 'http://localhost';

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        // register autoloader
        // do not use composer's autoloader to avoid affect the primary application using this library.
        self::registerPsr4AutoLoader([
            'App\\Http\\Controllers\\' =>  __DIR__.'/miniApp/http/Controllers',
        ]);
    }


    public function setUp()
    {
        parent::setUp();

        $this->app->make('router')->any('/fb/{action}', FooBarController::class . '@runAction');
    }

    /**
     * @dataProvider successful_default_route_data_provider
     * @runInSeparateProcess
     */
    public function test_successful_default_route($method, $uri, $expect)
    {
        $response = $this->call($method, $uri);
        $this->assertResponseOk();
        $this->assertEquals($expect, $response->getContent());
    }

    public function successful_default_route_data_provider()
    {
        return [
            ['get',    'foo-bar/joy', FooBarController::class . '::doGetJoy',],
            ['post',   'foo_bar/joy', FooBarController::class . '::doPostJoy',],
            ['put',    'foo-Bar/joy', FooBarController::class . '::doPutJoy',],
            ['delete', 'fooBar/joy', FooBarController::class . '::doDeleteJoy',],

            ['get',    'fb/joy', FooBarController::class . '::doGetJoy',],
            ['post',   'fb/joy', FooBarController::class . '::doPostJoy',],
            ['put',    'fb/joy', FooBarController::class . '::doPutJoy',],
            ['delete', 'fb/joy', FooBarController::class . '::doDeleteJoy',],

            ['get',    'laravel-default-routes-test/foo', LaravelDefaultRoutesTestController::class . '::doGetFoo',],
            ['post',   'laravel-default-routes-test/foo', LaravelDefaultRoutesTestController::class . '::doPostFoo',],
            ['put',    'laravel-default-routes-test/foo', LaravelDefaultRoutesTestController::class . '::doPutFoo',],
            ['delete', 'laravel-default-routes-test/foo', LaravelDefaultRoutesTestController::class . '::doDeleteFoo',],

            ['get',    'laravel_default_routes_test/foo', LaravelDefaultRoutesTestController::class . '::doGetFoo',],
            ['post',   'laravel_default_routes_test/foo', LaravelDefaultRoutesTestController::class . '::doPostFoo',],
            ['put',    'laravel_default_routes_test/foo', LaravelDefaultRoutesTestController::class . '::doPutFoo',],
            ['delete', 'laravel_default_routes_test/foo', LaravelDefaultRoutesTestController::class . '::doDeleteFoo',],

            ['get',    'laravelDefaultRoutesTest/foo', LaravelDefaultRoutesTestController::class . '::doGetFoo',],
            ['post',   'laravelDefaultRoutesTest/foo', LaravelDefaultRoutesTestController::class . '::doPostFoo',],
            ['put',    'laravelDefaultRoutesTest/foo', LaravelDefaultRoutesTestController::class . '::doPutFoo',],
            ['delete', 'laravelDefaultRoutesTest/foo', LaravelDefaultRoutesTestController::class . '::doDeleteFoo',],

            ['get',    'foo/bar/fun', BarController::class . '::doGetFun',],
            ['post',   'foo/bar/fun', BarController::class . '::doPostFun',],
            ['put',    'foo/bar/fun', BarController::class . '::doPutFun',],
            ['delete', 'foo/bar/fun', BarController::class . '::doDeleteFun',],

            ['get',    'foo/bar', FooController::class . '::doGetBar',],
            ['post',   'foo/bar', FooController::class . '::doPostBar',],
            ['put',    'foo/bar', FooController::class . '::doPutBar',],
            ['delete', 'foo/bar', FooController::class . '::doDeleteBar',],


            ['get',    'foo/barz', BarzController::class . '::doGetIndex',],
            ['post',   'foo/barz', BarzController::class . '::doPostIndex',],
            ['put',    'foo/barz', BarzController::class . '::doPutIndex',],
            ['delete', 'foo/barz', BarzController::class . '::doDeleteIndex',],
        ];
    }


    /**
     * @dataProvider invalid_default_route_data_provider
     * @runInSeparateProcess
     */
    public function test_invalid_default_route($method, $uri, $expect)
    {
        $response = $this->call($method, $uri);
        $this->assertNotEquals($expect, $response->getContent());
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function invalid_default_route_data_provider()
    {
        return [
            ['get',    'foobar/joy', FooBarController::class . '::doGetJoy',],
            ['post',   'foo/bar/joy', FooBarController::class . '::doPostJoy',],
            ['put',    'fooBAR/joy', FooBarController::class . '::doPutJoy',],
            ['delete', 'FOOBAR/joy', FooBarController::class . '::doDeleteJoy',],
        ];
    }

    /**
     * Creates the application.
     *
     * Needs to be implemented by subclasses.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/miniApp/bootstrap/app.php';
        return $app;
    }


    protected static function registerPsr4AutoLoader($namespaces, $caseSensitive=true){
        spl_autoload_register(function($class) use ($namespaces, $caseSensitive){
            $class = ltrim($class, '\\');
            foreach ($namespaces as $ns => $dir) {
                if (strncmp($class, $ns, strlen($ns)) === 0){
                    $file = str_replace(['\\', '/'],
                            [DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR],
                            $dir . DIRECTORY_SEPARATOR . substr($class, strlen($ns))) . '.php';


                    if (is_file($file)){
                        if ($caseSensitive && realpath($file) != $file){
                            continue;
                        }

                        include_once($file);
                    }
                }
            }
        });
    }
}