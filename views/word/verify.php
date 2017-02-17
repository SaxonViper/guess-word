<?php
use app\models\Word;
$this->registerJsFile(Yii::$app->request->baseUrl.'../js/verify.js',['depends' => [\yii\web\JqueryAsset::className()]]);

?>
<h2>Проверка введённых слов</h2>
<? if (count($words)) { ?>
    <table id="verifyTable">
        <? foreach ($words as $word) {
            $word instanceof Word; ?>
            <tr data-word="<?= $word['word'] ?>">
                <td><?= $word['word'] ?></td>
                <td><a class="verifyWord">Подтвердить</a></td>
                <td><a class="deleteWord">Удалить</a></td>
            </tr>
        <? } ?>
    </table>
    <button class="btn btn-success verifyAll">Подтвердить все</button>
    <button class="btn btn-danger deleteAll">Удалить все</button>
    <input name="maxid" type="hidden" id="maxId" value="<?= $word['ID'] ?>">
<? } else { ?>
    <h3>Неподтверждённых слов нет!</h3>
<? } ?>