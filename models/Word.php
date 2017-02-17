<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "words".
 *
 * @property string $ID
 * @property string $word
 * @property integer $played
 * @property integer $guessed
 * @property integer $entered
 * @property string $last_played
 * @property integer $verified
 */
class Word extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'words';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['word'], 'required'],
            [['ID', 'played', 'guessed', 'entered'], 'integer'],
            [['last_played'], 'safe'],
            [['word'], 'string', 'max' => 5]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'word' => 'Word',
            'played' => 'Played',
            'guessed' => 'Guessed',
            'entered' => 'Entered',
            'last_played' => 'Last Played',
        ];
    }
}
