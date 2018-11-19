<?php
/**
 * Created by PhpStorm.
 * User: liwenling
 * Date: 2018/11/19
 * Time: 上午10:22
 */
namespace frontend\controllers;
use Yii;
use yii\web\Controller;
use yii\httpclient\Client;


class Y2048Controller extends Controller
{
    public function actionLogin()
    {
        $code = Yii::$app->request->get('code');
        //https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code


        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://api.weixin.qq.com/sns/jscode2session')
            ->setData(['appid' => Yii::$app->params['appid'], 'secret' => Yii::$app->params['AppSecret'], 'js_code' => $code, 'grant_type' => 'authorization_code'])
            ->send();

        return $response->content['openid'];
//        print_r(Yii::$app->params);
//        return json_decode($response->getContent());
    }


}