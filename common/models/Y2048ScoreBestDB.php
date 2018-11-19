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


class Y2048ScoreBestDB extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%y2048_score_best}}';
    }

    /**
     * @inheritdoc
     */
    public static function findById($id)
    {
        return static::findOne(['id' => $id]);
    }

    public static function findByUid($userid)
    {
        return static::findOne(['userid' => $userid]);
    }

    public static function setBestScore($userid, $score)
    {
        if(empty($userid || $score)) return ;
        $ar = self::findByUid($userid);
        if(empty($ar)){
            $ar = new Y2048ScoreBestDB();
            $ar->userid = $userid;
            $ar->ctime = date("Y-m-d H:i:s");
            $ar->mtime = date("Y-m-d H:i:s");
        }
        $ar->baseScore = $score;
        return $ar->save();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
}
