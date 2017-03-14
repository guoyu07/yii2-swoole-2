<?php
/**
 * @link http://www.tintsoft.com/
 * @copyright Copyright (c) 2012 TintSoft Technology Co. Ltd.
 * @license http://www.tintsoft.com/license/
 */
namespace xutl\swoole;

use Yii;
use yii\base\InvalidRouteException;

/**
 * Class Application
 * @package xutl\swoole
 */
class Application extends \yii\base\Application
{
    /**
     * The option name for specifying the application configuration file path.
     */
    const OPTION_APPCONFIG = 'appconfig';

    /**
     * @var string the default route of this application. Defaults to 'help',
     * meaning the `help` command.
     */
    public $defaultRoute = 'help';

    /**
     * @var Controller the currently active controller instance
     */
    public $controller;

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        $config = $this->loadConfig($config);
        parent::__construct($config);
    }

    /**
     * Loads the configuration.
     * This method will check if the command line option [[OPTION_APPCONFIG]] is specified.
     * If so, the corresponding file will be loaded as the application configuration.
     * Otherwise, the configuration provided as the parameter will be returned back.
     * @param array $config the configuration provided in the constructor.
     * @return array the actual configuration to be used by the application.
     */
    protected function loadConfig($config)
    {
        if (!empty($_SERVER['argv'])) {
            $option = '--' . self::OPTION_APPCONFIG . '=';
            foreach ($_SERVER['argv'] as $param) {
                if (strpos($param, $option) !== false) {
                    $path = substr($param, strlen($option));
                    if (!empty($path) && is_file($file = Yii::getAlias($path))) {
                        return require($file);
                    } else {
                        exit("The configuration file does not exist: $path\n");
                    }
                }
            }
        }

        return $config;
    }

    /**
     * Handles the specified request.
     * @param Request $request the request to be handled
     * @return Response the resulting response
     */
    public function handleRequest($request)
    {
        list ($route, $params) = $request->resolve();
        $this->requestedRoute = $route;
        $result = $this->runAction($route, $params);
        if ($result instanceof Response) {
            return $result;
        } else {
            $response = $this->getResponse();
            $response->exitStatus = $result;

            return $response;
        }
    }

    /**
     * Runs a controller action specified by a route.
     * This method parses the specified route and creates the corresponding child module(s), controller and action
     * instances. It then calls [[Controller::runAction()]] to run the action with the given parameters.
     * If the route is empty, the method will use [[defaultRoute]].
     *
     * For example, to run `public function actionTest($a, $b)` assuming that the controller has options the following
     * code should be used:
     *
     * ```php
     * \Yii::$app->runAction('controller/test', ['option' => 'value', $a, $b]);
     * ```
     *
     * @param string $route the route that specifies the action.
     * @param array $params the parameters to be passed to the action
     * @return int|Response the result of the action. This can be either an exit code or Response object.
     * Exit code 0 means normal, and other values mean abnormal. Exit code of `null` is treaded as `0` as well.
     * @throws Exception if the route is invalid
     */
    public function runAction($route, $params = [])
    {
        try {
            $res = parent::runAction($route, $params);
            return is_object($res) ? $res : (int)$res;
        } catch (InvalidRouteException $e) {
            throw new UnknownCommandException($route, $this, 0, $e);
        }
    }

    /**
     * Returns the error handler component.
     * @return ErrorHandler the error handler application component.
     */
    public function getErrorHandler()
    {
        return $this->get('errorHandler');
    }

    /**
     * Runs the application.
     * This is the main entrance of an application.
     * @return int the exit status (0 means normal, non-zero values mean abnormal)
     */
    public function run()
    {
        try {

            $this->state = self::STATE_BEFORE_REQUEST;
            $this->trigger(self::EVENT_BEFORE_REQUEST);

            $this->state = self::STATE_HANDLING_REQUEST;
            $response = $this->handleRequest($this->getRequest());

            $this->state = self::STATE_AFTER_REQUEST;
            $this->trigger(self::EVENT_AFTER_REQUEST);

            $this->state = self::STATE_SENDING_RESPONSE;
            $response->send();

            $this->state = self::STATE_END;

            return $response->exitStatus;

        } catch (ExitException $e) {

            $this->end($e->statusCode, isset($response) ? $response : null);
            return $e->statusCode;

        }
    }

    /**
     * Returns the request component.
     * @return Request the request component.
     */
    public function getRequest()
    {
        return $this->get('request');
    }

    /**
     * Returns the response component.
     * @return Response the response component.
     */
    public function getResponse()
    {
        return $this->get('response');
    }

    /**
     * @inheritdoc
     */
    public function coreComponents()
    {
        return array_merge(parent::coreComponents(), [
            'request' => [
                'class' => 'xutl\swoole\Request'
            ],
            'response' => [
                'class' => 'xutl\swoole\Response'
            ],
            'errorHandler' => [
                'class' => 'xutl\swoole\ErrorHandler'
            ],
        ]);
    }
}