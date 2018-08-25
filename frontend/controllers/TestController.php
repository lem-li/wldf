<?php
/**
 * Created by PhpStorm.
 * User: liwenling
 * Date: 2018/5/23
 * Time: 下午4:40
 */

namespace frontend\controllers;

use common\models\User;
use Yii;
use yii\log;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class TestController extends Controller
{

    public function actionT(){

        $res = array('aaa','bbb');

        return json_encode($res);
    }

    public function actionBooks(){



    }




}