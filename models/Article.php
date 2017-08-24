<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $title
 * @property string $author
 * @property string $content
 * @property integer $time
 * @property integer $userId
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title','content'], 'required'],
            [['content'], 'string'],
            [['content'], 'string'],
            [['time'], 'integer'],
            [['userId'], 'integer'],
            [['state'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['author'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'author' => 'Author',
            'content' => 'Content',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if(\Yii::$app->user->id){
                $this->userId = \Yii::$app->user->id;
                $this->author = '冒险王0558';
            }
            $this->time = time();
            return true;
        }
        return false;
    }
}
