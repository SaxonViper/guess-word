<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "results".
 *
 * @property string $ID
 * @property string $player
 * @property string $word
 * @property string $date_played
 * @property integer $win
 * @property integer $moves
 * @property integer $time
 * @property integer $score
 */
class Result extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'results';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date_played'], 'safe'],
            [['win', 'moves', 'time', 'score'], 'integer'],
            [['moves', 'time', 'word'], 'required'],
            [['player'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ID' => 'ID',
            'player' => 'Player',
            'date_played' => 'Date Played',
            'win' => 'Win',
            'moves' => 'Moves',
            'time' => 'Time',
            'score' => 'Score',
        ];
    }
}
