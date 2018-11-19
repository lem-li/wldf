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

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class UsersDB extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%airticle}}';
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

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
}
