<?php namespace frame\api;

use frame\route\RouteNamespaceMacro;
use frame\route\Response;
use frame\config\ConfigRouter;
use frame\route\Router;
use frame\tools\JsonEncoder;

class ExecApi extends RouteNamespaceMacro
{
    protected function run()
    {
        $apiClass = $this->getApiClass();
        $response = Response::getDriver();
        if (is_subclass_of($apiClass, Api::class)) {
            $api = new $apiClass;
            $result = $api->exec();
            if ($result !== null) {
                $prettyJson = ConfigRouter::getDriver()->findConfig('core')->{'mode.debug'};
                $response->setText(JsonEncoder::forViewText($result, $prettyJson));
            }
        } else {
            $response->setCode(404);
            $response->finish();
        }
    }

    private function getApiClass(): string
    {
        $parts = Router::getDriver()->getCurrentRoute()->getPathParts();
        $lastIndex = count($parts) - 1;
        $parts[$lastIndex] = str_replace('-', '', ucwords($parts[$lastIndex], '-'));
        return '\\' . implode('\\', $parts) . 'Api';
    }
}