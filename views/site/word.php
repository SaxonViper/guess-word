<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Word */
/* @var $form ActiveForm */
?>
<div class="site-Word">
    <div>
        Всего слов в базе: <?= $numWords ?>
    </div>
    <? if ($newWord) { ?>
        <div>
            Слово только что загадано!
        </div>
    <? } else  { ?>
        <div>
            Добро пожаловать назад.
        </div>
    <? } ?>
    <h1>
        <?= mb_strtoupper($word, 'utf-8') ?>
    </h1>
</div><!-- site-Word -->
