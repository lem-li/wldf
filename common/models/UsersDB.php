<?php
/**
 * Created by PhpStorm.
 * User: liwenling
 * Date: 2018/11/19
 * Time: 下午12:03
 */
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


class UsersDB extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     * @inheritdoc
     */
    public static function findById($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findByOpenid($openid)
    {
        return static::findOne(['openid' => $openid]);
    }


    public static function updateUserByOpenid($openid , $data){
        $ar = static::findOne(['openid' => $openid]);
        if(empty($ar)){
            $ar = new UsersDB();
            $ar->openid = $openid;
            $ar->ctime = date("Y-m-d H:i:s");
        }
        if($ar->nickName != $data['nickName']){
            $ar->nickName = $data['nickName'];
        }
        if($ar->gender != $data['gender']){
            $ar->gender = $data['gender'];
        }
        if($ar->city != $data['city']){
            $ar->city = $data['city'];
        }
        if($ar->province != $data['province']){
            $ar->province = $data['province'];
        }
        if($ar->country != $data['country']){
            $ar->country = $data['country'];
        }
        if($ar->photo != $data['avatarUrl']){
            $ar->photo = $data['avatarUrl'];
        }
        return $ar->save() ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

}
