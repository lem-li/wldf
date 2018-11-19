<?php
/**
 * Created by PhpStorm.
 * User: liwenling
 * Date: 2018/11/19
 * Time: 上午10:22
 */
namespace frontend\controllers;
use common\models\UsersDB;
use Yii;
use yii\web\Controller;
use yii\httpclient\Client;


class Y2048Controller extends Controller
{
    public function actionLogin()
    {
        $code = Yii::$app->request->get('code');
        $client = new Client();
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl('https://api.weixin.qq.com/sns/jscode2session')
            ->setData(['appid' => Yii::$app->params['appid'], 'secret' => Yii::$app->params['AppSecret'], 'js_code' => $code, 'grant_type' => 'authorization_code'])
            ->send();

        if($response->statusCode == 200){
            $data = json_decode($response->content, true);
            $openid = isset($data['openid'])?$data['opend']:'';
            if(empty(UsersDB::findByOpenid($openid))){
                $ar = new UsersDB();
                $ar->openid = $openid;
                $ar->save();
            }
            $this->echoOk($data,'登录成功');
        }else{
            $this->echoErr([],'登录失败');
        }
    }


}