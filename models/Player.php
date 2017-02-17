<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "players".
 *
 * @property string $ID
 * @property string $FirstName
 * @property string $LastName
 * @property integer $Age
 */
class Player extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'players';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['FirstName', 'LastName', 'Age'], 'required'],
            [['Age'], 'integer'],
            [['FirstName', 'LastName'], 'string', 'max' => 30]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'FirstName' => 'First Name',
            'LastName' => 'Last Name',
            'Age' => 'Age',
        ];
    }
}
