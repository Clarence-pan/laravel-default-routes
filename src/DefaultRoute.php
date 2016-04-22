<?php

namespace Clarence\LaravelDefaultRoutes;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait DefaultRoute
{
    protected $defaultAction = 'index';

    public function runAction(Request $request, $action = null)
    {
        $method = $this->resolveActionInCertainController($request, get_class($this), $action);

        return app()->call([$this, substr(strrchr($method, '@'), 1)]);
    }

    public function runControllerAction(Request $request, $controller, $action = null)
    {
        $method = $this->resolveControllerAction($request, $controller, $action);

        return app()->call($method);
    }

    public function runModuleControllerAction(Request $request, $module, $controller, $action = null)
    {
        return $this->runControllerAction($request, $module.'/'.$controller, $action);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string                                    $controller
     * @param string|null                               $action
     *
     * @return \ReflectionMethod
     */
    protected function resolveControllerAction(Request $request, $controller, $action = null)
    {
        $controllerPrefix = $this->getControllerPrefix();
        $controllerSuffix = $this->getControllerSuffix();

        // foo/bar:
        // 1. FooController@bar
        // 2. Foo\BarController@index

        if (empty($action)) {
            $action = substr(strrchr($controller, '/'), 1);
            $controller = substr($controller, 0, strlen($controller) - strlen($action) + 1);
        }

        $controller = $this->normalizeName($controller);
        $action = $this->normalizeName($action);

        try {
            // firstly, try foo/bar as FooController@bar
            $controllerClass = $controllerPrefix.$controller.$controllerSuffix;

            return $this->resolveActionInCertainController($request, $controllerClass, $action);
        } catch (NotFoundHttpException $e) {
            // then, try foo/bar as Foo\BarController@index
            $controllerClass = $controllerPrefix.$controller.'\\'.$action.$controllerSuffix;

            return $this->resolveActionInCertainController($request, $controllerClass, $this->defaultAction);
        }
    }

    protected function getControllerPrefix()
    {
        return config('laravel-default-routes.controller-prefix', '\\App\\Http\\Controllers\\');
    }

    protected function getControllerSuffix()
    {
        return config('laravel-default-routes.controller-suffix', 'Controller');
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string                                    $controllerClass
     * @param string|null                               $action
     *
     * @return \ReflectionMethod
     */
    protected function resolveActionInCertainController(Request $request, $controllerClass, $action)
    {
        $action = $this->normalizeName($action);
        $action = $action ?: $this->defaultAction;

        $httpMethod = $request->getMethod();
        $classMethod = "do{$httpMethod}{$action}";

        try {
            $reflectionMethod = new \ReflectionMethod($controllerClass, $classMethod);

            // only public method are allowed to access
            if (!$reflectionMethod->isPublic()) {
                throw new MethodNotAllowedHttpException("Action $action is not allowed");
            }

            return $controllerClass.'@'.$classMethod;
        } catch (\ReflectionException $e) {
            throw new NotFoundHttpException("Action $action cannot be found");
        }
    }

    protected function normalizeName($name)
    {
        // convert foo-bar to FooBar
        $name = implode('', array_map('ucfirst', explode('-', $name)));

        // convert foo_bar to FooBar
        $name = implode('', array_map('ucfirst', explode('_', $name)));

        // convert foot/bar to Foo\Bar
        $name = implode('\\', array_map('ucfirst', explode('/', $name)));

        return $name;
    }
}
