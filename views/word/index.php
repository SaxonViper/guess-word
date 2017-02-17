<?php
/* @var $this yii\web\View */
//$this->registerJsFile('../js/maskedinput.js');
?>
<? if (!$word) { ?>
    <a href="start" class="startGame btn btn-success">Начать игру</a>
<? } else { ?>
    <div id="scoreboard">
        <div class="movesCount">Ходы <span><?= count($guesses) ?></span></div>
        <div class="timeCount">Время <span data-start="<?= $time ?>">0:00</span></div>
    </div>

    <div id="highscores">
        <div class="highscoresHeader">Рекорды</div>
        <table id="highscoresTable">
            <? foreach ($highscores as $score) {
                $score instanceof Result ?>
               <tr>
                   <td><?= $score['player'] ?></td>
                   <td><?= $score['score'] ?></td>
               </tr>
            <? } ?>
        </table>
    </div>

    <div class="gameWrapper">
        <div class="status">Слово загадано. Удачи!</div>
        <div id="puzzleTable">
            <div class="letter"><span>?</span></div>
            <div class="letter"><span>?</span></div>
            <div class="letter"><span>?</span></div>
            <div class="letter"><span>?</span></div>
            <div class="letter"><span>?</span></div>
        </div>

        <form id="wordInputForm" method="post">
            <input id="wordInput" name="guess">
            <button class="btn btn-primary" id="wordCheck" href="check">Тест</button>
        </form>

        <div id="wordStats" style="display: none;">
            Это слово было сыграно <span class="playedNum"></span>, отгадано <span class="guessedNum"></span>
        </div>

        <table id="guessesTable">
            <tr>
                <td colspan="3">Ваши догадки</td>
            </tr>
            <? foreach ($guesses as $guess) { ?>
                <tr>
                    <td class="guessWord"><?= $guess['guess'] ?></td>
                    <td class="guessKills"><?= $guess['kills'] ?></td>
                    <td class="guessHits"><?= $guess['hits'] ?></td>
                </tr>
            <? } ?>
        </table>

        <a class="resignButton btn btn-danger">Сдаться</a>
        <a href="start" class="startGame btn btn-success" style="display:none;">Играть снова</a>
    </div>

    <div id="overlay" style="display: none">
        <div id="scorePopUp" style="display: none">
            <div class="scorePopUpHeader">
                Поздравляем! Вы набрали <span class="scoreVal"></span> очков(а).
            </div>
            <div class="scoreHsForm" style="display: none">
                Как вас записать в таблицу рекордов?
                <br>
                <input class="scoreHsInput" placeholder="Аноним">
            </div>
            <button class="btn btn-success scoreHsOk">ОК</button>
        </div>
    </div>

<? } ?>