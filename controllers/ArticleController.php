<?php

namespace app\controllers;

use Yii;
use app\models\Article;
use yii\rest\ActiveController;
use yii\filters\Cors;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\filters\auth\QueryParamAuth;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends ActiveController
{
    public $modelClass = 'app\models\Article';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {

        return ArrayHelper::merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => null,
                    'Access-Control-Max-Age' => 86400,
                    'Access-Control-Expose-Headers' => ['X-Pagination-Page-Count'],
                ],
            ],
            'authenticator' => [
                'class' => QueryParamAuth::className(),
                'tokenParam' => 'token',
                'optional' => [
                    'index',
                    'view',
                ],
            ],
        ]);
    }

    public function actions()
    {
        $actions =  parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        unset($actions['delete']);
        return $actions;
    }

    public function checkAccess($action, $model = null, $params = [])
    {

        if ($action === 'update' or $action === 'delete')
        {
            if ( Yii::$app->user->can('updatePost',['post'=>$model]) === false )
            {
                throw new \yii\web\ForbiddenHttpException('You can\'t '.$action.' this article.');
            }

        }
    }


    public function prepareDataProvider()
    {
        //判断blog前后端访问
        $isBack = Yii::$app->request->get('back');
        if($isBack == 1){
            $query = Article::find()->orderBy('id DESC' );
        }else{
            $query = Article::find()->where(['state'=>0])->orderBy('id DESC' );
        }
        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 5,
            ],
        ]);
        return $provider;

    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ( Yii::$app->user->can('updatePost',['post'=>$model]) === false )
        {
            throw new \yii\web\ForbiddenHttpException('You can\'t delete this article.');
        }

        $state = $model->state;
        $model->state = $state ? 0 : 1;
        $model->time= time();
        $model->save(false);

        Yii::$app->getResponse()->setStatusCode(204);
    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Article the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) !== null) {
            return $model;
        } else {
            throw new \yii\web\NotFoundHttpException('The requested page does not exist.');
        }
    }


}
