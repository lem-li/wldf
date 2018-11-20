<?php
/**
 * Created by PhpStorm.
 * User: liwenling
 * Date: 2018/11/19
 * Time: 上午10:22
 */
namespace frontend\controllers;
use common\models\User;
use common\models\UsersDB;
use common\models\Y2048ScoreBestDB;
//use common\wechat\Wc;
use Yii;
use yii\web\Controller;
use yii\httpclient\Client;
use Jtcczu\Applet\Decrypt\AppletDecrypt;



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
        Yii::$app->response->format='json';
        if($response->statusCode == 200){
            $data = json_decode($response->content, true);
            $openid = isset($data['openid'])?$data['openid']:'';
            if(!empty($openid) && empty(UsersDB::findByOpenid($openid))){
                $ar = new UsersDB();
                $ar->openid = $openid;
                $ar->ctime = date("Y-m-d H:i:s");
                $ar->save();
            }
            return $data;
        }else{
            return ['登录失败'];
        }
    }

    public function actionSetBest(){
        Yii::$app->response->format='json';
        $openid = Yii::$app->request->post('openid');
        $score = Yii::$app->request->post('score');
        if(empty($openid) || empty($user = UsersDB::findByOpenid($openid))){
            return ['用户未登录'];
        }

        $res = Y2048ScoreBestDB::setBestScore($user->id, $score);
        return $res;
    }

    public function actionSetUser(){
        Yii::$app->response->format='json';
        $openid = Yii::$app->request->post('openid');
        $session_key = Yii::$app->request->post('session_key');
        $iv = Yii::$app->request->post('iv');
        $encryptedData = Yii::$app->request->post('encryptedData');

        $wxCrypt = new AppletDecrypt(Yii::$app->params['appid'], $session_key);
        $data = $wxCrypt->getUser($encryptedData, $iv);

        if($data){
            $res = UsersDB::updateUserByOpenid($data['openId'], $data);
            return $res ? $data : '失败';
        }
        return '失败';
    }


}