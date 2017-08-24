<?php

namespace app\controllers;

use Yii;
use yii\web\BadRequestHttpException;
use yii\web\UnauthorizedHttpException;
use app\models\LoginForm;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
/**
 * UserController implements the CRUD actions for UserForm model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(),[
            'corsFilter' => [
                'class' => Cors::className(),
            ],
        ]);
    }

    /*
     * 登录生成token并返回给客户端
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            throw new BadRequestHttpException('已登录');
        }

        $model = new LoginForm();
        $model->username = Yii::$app->request->post('username');
        $model->password = Yii::$app->request->post('password');

        if ($model->login()) {
            return [
                'status'=>200,
                'token' => $model->login(),
            ];
        }else{
            throw new UnauthorizedHttpException('验证失败');
        }

    }

}
