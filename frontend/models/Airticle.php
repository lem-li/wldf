<?php
namespace frontend\models;

use common\models\AirticleDetailDB;
use common\models\FundDB;
use common\models\MyReadLogsDB;
use yii\base\Model;
use common\models\User;
use common\models\AirticleDB;

/**
 * Signup form
 */
class Airticle extends Model
{


    /**
     * 根据图书ID获取图书介信息
     * @param $id
     * @return null|void|\yii\web\IdentityInterface|static
     */
    public static function getById($id){
        if(empty($id)) return ;
        return AirticleDB::findIdentity($id);
    }


    /**
     * 根据图书和章节ID获取章节内容
     * @param $bid
     * @param $sn
     * @return null|void|static
     */
    public static function getDetail($bid, $sn){
        if(empty($bid) || empty($sn)) return ;

        return AirticleDetailDB::getDetail($bid , $sn);

    }

    public static function getMylog($uid, $bid){
        if(empty($bid) || empty($uid)) return ;

        return MyReadLogsDB::getMylog($uid, $bid);
    }

    public static function updateMylog($uid, $bid, $sn){
        if(empty($bid) || empty($uid)) return ;

        return MyReadLogsDB::updateMylog($uid, $bid, $sn);
    }


}