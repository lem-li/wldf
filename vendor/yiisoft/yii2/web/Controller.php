<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace yii\web;

use Yii;
use yii\base\InlineAction;
use yii\helpers\Url;

/**
 * Controller is the base class of web controllers.
 *
 * For more details and usage information on Controller, see the [guide article on controllers](guide:structure-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Controller extends \yii\base\Controller
{
    /**
     * @var bool whether to enable CSRF validation for the actions in this controller.
     * CSRF validation is enabled only when both this property and [[\yii\web\Request::enableCsrfValidation]] are true.
     */
    public $enableCsrfValidation = true;
    /**
     * @var array the parameters bound to the current action.
     */
    public $actionParams = [];

    /**
     * isEncryptId
     * 是否对所有的id都加密
     *
     * @var mixed
     * @access protected
     */
    protected $isEncryptId = true;

    /**
     * Renders a view in response to an AJAX request.
     *
     * This method is similar to [[renderPartial()]] except that it will inject into
     * the rendering result with JS/CSS scripts and files which are registered with the view.
     * For this reason, you should use this method instead of [[renderPartial()]] to render
     * a view to respond to an AJAX request.
     *
     * @param string $view the view name. Please refer to [[render()]] on how to specify a view name.
     * @param array $params the parameters (name-value pairs) that should be made available in the view.
     * @return string the rendering result.
     */
    public function renderAjax($view, $params = [])
    {
        return $this->getView()->renderAjax($view, $params, $this);
    }

    /**
     * Send data formatted as JSON.
     *
     * This method is a shortcut for sending data formatted as JSON. It will return
     * the [[Application::getResponse()|response]] application component after configuring
     * the [[Response::$format|format]] and setting the [[Response::$data|data]] that should
     * be formatted. A common usage will be:
     *
     * ```php
     * return $this->asJson($data);
     * ```
     *
     * @param mixed $data the data that should be formatted.
     * @return Response a response that is configured to send `$data` formatted as JSON.
     * @since 2.0.11
     * @see Response::$format
     * @see Response::FORMAT_JSON
     * @see JsonResponseFormatter
     */
    public function asJson($data)
    {
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $response->data = $data;
        return $response;
    }

    /**
     * Send data formatted as XML.
     *
     * This method is a shortcut for sending data formatted as XML. It will return
     * the [[Application::getResponse()|response]] application component after configuring
     * the [[Response::$format|format]] and setting the [[Response::$data|data]] that should
     * be formatted. A common usage will be:
     *
     * ```php
     * return $this->asXml($data);
     * ```
     *
     * @param mixed $data the data that should be formatted.
     * @return Response a response that is configured to send `$data` formatted as XML.
     * @since 2.0.11
     * @see Response::$format
     * @see Response::FORMAT_XML
     * @see XmlResponseFormatter
     */
    public function asXml($data)
    {
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_XML;
        $response->data = $data;
        return $response;
    }

    /**
     * Binds the parameters to the action.
     * This method is invoked by [[\yii\base\Action]] when it begins to run with the given parameters.
     * This method will check the parameter names that the action requires and return
     * the provided parameters according to the requirement. If there is any missing parameter,
     * an exception will be thrown.
     * @param \yii\base\Action $action the action to be bound with parameters
     * @param array $params the parameters to be bound to the action
     * @return array the valid parameters that the action can run with.
     * @throws BadRequestHttpException if there are missing or invalid parameters.
     */
    public function bindActionParams($action, $params)
    {
        if ($action instanceof InlineAction) {
            $method = new \ReflectionMethod($this, $action->actionMethod);
        } else {
            $method = new \ReflectionMethod($action, 'run');
        }

        $args = [];
        $missing = [];
        $actionParams = [];
        foreach ($method->getParameters() as $param) {
            $name = $param->getName();
            if (array_key_exists($name, $params)) {
                if ($param->isArray()) {
                    $args[] = $actionParams[$name] = (array) $params[$name];
                } elseif (!is_array($params[$name])) {
                    $args[] = $actionParams[$name] = $params[$name];
                } else {
                    throw new BadRequestHttpException(Yii::t('yii', 'Invalid data received for parameter "{param}".', [
                        'param' => $name,
                    ]));
                }
                unset($params[$name]);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $actionParams[$name] = $param->getDefaultValue();
            } else {
                $missing[] = $name;
            }
        }

        if (!empty($missing)) {
            throw new BadRequestHttpException(Yii::t('yii', 'Missing required parameters: {params}', [
                'params' => implode(', ', $missing),
            ]));
        }

        $this->actionParams = $actionParams;

        return $args;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if ($this->enableCsrfValidation && Yii::$app->getErrorHandler()->exception === null && !Yii::$app->getRequest()->validateCsrfToken()) {
                throw new BadRequestHttpException(Yii::t('yii', 'Unable to verify your data submission.'));
            }

            return true;
        }

        return false;
    }

    /**
     * Redirects the browser to the specified URL.
     * This method is a shortcut to [[Response::redirect()]].
     *
     * You can use it in an action by returning the [[Response]] directly:
     *
     * ```php
     * // stop executing this action and redirect to login page
     * return $this->redirect(['login']);
     * ```
     *
     * @param string|array $url the URL to be redirected to. This can be in one of the following formats:
     *
     * - a string representing a URL (e.g. "http://example.com")
     * - a string representing a URL alias (e.g. "@example.com")
     * - an array in the format of `[$route, ...name-value pairs...]` (e.g. `['site/index', 'ref' => 1]`)
     *   [[Url::to()]] will be used to convert the array into a URL.
     *
     * Any relative URL that starts with a single forward slash "/" will be converted
     * into an absolute one by prepending it with the host info of the current request.
     *
     * @param int $statusCode the HTTP status code. Defaults to 302.
     * See <https://tools.ietf.org/html/rfc2616#section-10>
     * for details about HTTP status code
     * @return Response the current response object
     */
    public function redirect($url, $statusCode = 302)
    {
        return Yii::$app->getResponse()->redirect(Url::to($url), $statusCode);
    }

    /**
     * Redirects the browser to the home page.
     *
     * You can use this method in an action by returning the [[Response]] directly:
     *
     * ```php
     * // stop executing this action and redirect to home page
     * return $this->goHome();
     * ```
     *
     * @return Response the current response object
     */
    public function goHome()
    {
        return Yii::$app->getResponse()->redirect(Yii::$app->getHomeUrl());
    }

    /**
     * Redirects the browser to the last visited page.
     *
     * You can use this method in an action by returning the [[Response]] directly:
     *
     * ```php
     * // stop executing this action and redirect to last visited page
     * return $this->goBack();
     * ```
     *
     * For this function to work you have to [[User::setReturnUrl()|set the return URL]] in appropriate places before.
     *
     * @param string|array $defaultUrl the default return URL in case it was not set previously.
     * If this is null and the return URL was not set previously, [[Application::homeUrl]] will be redirected to.
     * Please refer to [[User::setReturnUrl()]] on accepted format of the URL.
     * @return Response the current response object
     * @see User::getReturnUrl()
     */
    public function goBack($defaultUrl = null)
    {
        return Yii::$app->getResponse()->redirect(Yii::$app->getUser()->getReturnUrl($defaultUrl));
    }

    /**
     * Refreshes the current page.
     * This method is a shortcut to [[Response::refresh()]].
     *
     * You can use it in an action by returning the [[Response]] directly:
     *
     * ```php
     * // stop executing this action and refresh the current page
     * return $this->refresh();
     * ```
     *
     * @param string $anchor the anchor that should be appended to the redirection URL.
     * Defaults to empty. Make sure the anchor starts with '#' if you want to specify it.
     * @return Response the response object itself
     */
    public function refresh($anchor = '')
    {
        return Yii::$app->getResponse()->redirect(Yii::$app->getRequest()->getUrl() . $anchor);
    }

    /**
     * HTTP Protocol defined status codes
     * HTTP协议状态码,调用函数时候只需要将$num赋予一个下表中的已知值就直接会返回状态了。
     * @param int $num
     */
    public function httpStatusCode($num) {
        $http = array (
            100 => "HTTP/1.1 100 Continue",
            101 => "HTTP/1.1 101 Switching Protocols",
            200 => "HTTP/1.1 200 OK",
            201 => "HTTP/1.1 201 Created",
            202 => "HTTP/1.1 202 Accepted",
            203 => "HTTP/1.1 203 Non-Authoritative Information",
            204 => "HTTP/1.1 204 No Content",
            205 => "HTTP/1.1 205 Reset Content",
            206 => "HTTP/1.1 206 Partial Content",
            300 => "HTTP/1.1 300 Multiple Choices",
            301 => "HTTP/1.1 301 Moved Permanently",
            302 => "HTTP/1.1 302 Found",
            303 => "HTTP/1.1 303 See Other",
            304 => "HTTP/1.1 304 Not Modified",
            305 => "HTTP/1.1 305 Use Proxy",
            307 => "HTTP/1.1 307 Temporary Redirect",
            400 => "HTTP/1.1 400 Bad Request",
            401 => "HTTP/1.1 401 Unauthorized",
            402 => "HTTP/1.1 402 Payment Required",
            403 => "HTTP/1.1 403 Forbidden",
            404 => "HTTP/1.1 404 Not Found",
            405 => "HTTP/1.1 405 Method Not Allowed",
            406 => "HTTP/1.1 406 Not Acceptable",
            407 => "HTTP/1.1 407 Proxy Authentication Required",
            408 => "HTTP/1.1 408 Request Time-out",
            409 => "HTTP/1.1 409 Conflict",
            410 => "HTTP/1.1 410 Gone",
            411 => "HTTP/1.1 411 Length Required",
            412 => "HTTP/1.1 412 Precondition Failed",
            413 => "HTTP/1.1 413 Request Entity Too Large",
            414 => "HTTP/1.1 414 Request-URI Too Large",
            415 => "HTTP/1.1 415 Unsupported Media Type",
            416 => "HTTP/1.1 416 Requested range not satisfiable",
            417 => "HTTP/1.1 417 Expectation Failed",
            500 => "HTTP/1.1 500 Internal Server Error",
            501 => "HTTP/1.1 501 Not Implemented",
            502 => "HTTP/1.1 502 Bad Gateway",
            503 => "HTTP/1.1 503 Service Unavailable",
            504 => "HTTP/1.1 504 Gateway Time-out"
        );
    }
    /**
     * echoJson
     * 输出json
     *
     * @param mixed $data
     * @access private
     * @return void
     */
    private function echoJson($data=array(),$code="00000",$msg="",$redirect="",$httpStatusCode=""){
        Yii::$app->response->format=Response::FORMAT_JSON;
        @ob_clean();
        //这里用text/html主要是因为ie6不支持application/json
        empty($httpStatusCode)?header( "Content-type:text/html; charset=utf-8" ) : header ( "Content-type:text/html; charset=utf-8" ,$this->httpStatusCode($httpStatusCode));
        if(!isset($data['status'])){
            $res = $data;
            $res['status']=substr($code,0,1);
            $res['code']=$code;
            $res['msg']=$msg;
            $res['redirect']=$redirect;
            return $res;
        }else{
            return $data;
        }
    }

    /**
     * echoJsonp
     * 输出jsonp
     *
     * @param mixed $data
     * @access private
     * @return void
     */
    private function echoJsonp($data=array(),$code="00000",$msg=""){
        $func = "jsoncallback";
        if(isset($_GET['jsoncallback'])){
            $func = $_GET['jsoncallback'];
        }
        header ( "Content-type:application/json; charset=utf-8" );
        if(!isset($data['status'])){
            $res = $data;
            $res['status']=substr($code,0,1);
            $res['code']=$code;
            $res['msg']=$msg;
            echo $func."(".json_encode ( $res ).")";
        }else{
            echo $func."(".json_encode ( $data ).")";
        }
    }

    public function echoOk($data=array(),$msg="",$code="00000",$type="json",$exit=true , $httpStatusCode=""){
        return $this->echoOut(array("data"=>$data),$code,$msg,$type,$exit,"",$httpStatusCode);
    }
    public function echoErr($data=array(),$msg="",$code="10000",$type="json",$exit=true,$redirect=""){
        $_data = $data;
        $_error = isset($data['error'])?$data['error']:array();
        unset($_data['error']);
        return $this->echoOut(array("data"=>$_data,"error"=>$_error),$code,$msg,$type,$exit,$redirect);
    }
    public function echoExp($data=array(),$msg="系统错误",$code="20000",$type="json",$exit=true){
        return $this->echoOut(array("data"=>$data),$code,$msg,$type,$exit);
    }

    /**
     * echoOut
     *
     * @param mixed $data
     * @param int $status
     * @param string $msg
     * @param string $type
     * @access public
     * @return void
     */
    public function echoOut($data=array(),$code="00000",$msg="",$type="json",$exit=true,$redirect="",$httpStatusCode=""){
        $data = $this->encryptId($data,true);
        if($type=="json" || (isset($_REQUEST['apiType']) && $_REQUEST['apiType']=="json")){
            $this->echoJson($data,$code,$msg,$redirect,$httpStatusCode);
        }else{
            $this->echoJsonp($data,$code,$msg,$redirect);
        }
        if($exit){
            $this->computeRunTime();
            Yii::app()->end();
        }
    }

    /**
     * encryptId
     *
     * @param mixed $data
     * @access public
     * @return void
     */
    public function encryptId($data,$isJson=false)
    {
        if(!$this->isEncryptId){
            return $data;
        }
        if(is_array($data)){
            foreach($data as $k=>$v){
                if(is_array($v)){
                    $data[$k]=$this->encryptId($v,$isJson);
                }elseif(is_string($v)){
                    if(substr(strtolower($k),-2,2)=="id" && strtolower($k) != 'openid'){
                        $_tmp = MathUtil::encrypt($v);
                        if($isJson){
                            $data[$k]=$_tmp;
                        }else{
                            $data["_".$k]=$_tmp;
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * computeRunTime
     * 计算处理时间
     * @note protected
     * @return void
     */
    protected function computeRunTime()
    {
        //如果为最外层的end
        if ($this->cEndLog && self::$actionCount == 1) {
            Yii::log ( "request end", CLogger::LEVEL_INFO, __METHOD__ );
            TimerUtil::stop( 'all' );
            $timer = TimerUtil::tree();
            Yii::log( json_encode($timer),  CLogger::LEVEL_INFO );

            if(is_array($timer['timers']['all'])){
                $runTime = $timer['timers']['all'][0];
            }else{
                $runTime = $timer['timers']['all'];
            }
            if ($runTime>$this->errorRunTime) {
                Yii::log('request run too long:'.print_r(TimerUtil::tree(),true), 'error');
            }else if ($runTime>$this->warnRunTime) {
                Yii::log('request run too long:'.print_r(TimerUtil::tree(),true), 'long');
            }
        }
        self::$actionCount--;
    }

    /**
     * 获取本次请求的ip
     * @return mixed
     */
    protected function getClientIp()
    {
        $unknown = 'unknown';
        /**
         * 处理多层代理IP的情况
         */
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        //if (false !== strpos($ip, ',')) $ip = reset(explode(',', $ip));
        return $ip;
    }


}
