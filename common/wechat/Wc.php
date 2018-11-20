<?php
/**
 * Created by PhpStorm.
 * User: liwenling
 * Date: 2018/11/19
 * Time: 下午2:25
 */
namespace common\wechat;
use yii\httpclient\Client;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;


include_once "wxBizDataCrypt/wxBizDataCrypt.php";

class Wc
{
    private  $appid = 'wxc55c4ac5fdaa94b1';

    public static function bizDataCrypt($sessionKey, $iv, $encryptedData){
        $data = [];
        $pc = new WXBizDataCrypt(self::$appid, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );
        return $errCode==0?$data:false;
    }

}