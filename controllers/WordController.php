<?php

namespace app\controllers;
use Yii;
use app\models\Word;
use app\models\Result;
use yii\db\Query;

class WordController extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    public function actionIndex()
    {
        $guesses = Yii::$app->session['guesses'];
        $word = \Yii::$app->session['word'];
        $timePassed = \Yii::$app->session['time'] ? time() - \Yii::$app->session['time'] : 0;
        $highscores = $this->getHighscores();

        return $this->render('index', [
            'word' => $word,
            'guesses' => $guesses,
            'time' => $timePassed,
            'highscores' => $highscores
        ]);
    }

    public function actionResign()
    {
        Yii::$app->session['guesses'] = [];
        return $this->render('resign');
    }

    public function actionStart()
    {
        $this->generateWord();
        return $this->redirect('index',302);
    }

    /**
     * Загадывает новое слово, записывает его в сессию и возвращает
     * @return string
     */
    private function generateWord() {
        $allWords = Word::find(['verified' => 1])->all();
        shuffle($allWords);
        $wordObj = $allWords[0];
        $wordObj instanceof Word;
        $wordObj['played'] = $wordObj['played'] + 1;
        $wordObj['last_played'] = date('Y-m-d H:i:s');
        $wordObj->save();
        Yii::$app->session['guesses'] = [];
        return Yii::$app->session['word'] = $wordObj['word'];
    }

    private function checkGuess($guess) {

        $wordInBase = Word::findOne(['word' => $guess]);

        return [
            'kills' => 3,
            'hits' => 3,
            'guess' => $wordInBase['word']
        ];
        if ($wordInBase) {
            $wordInBase instanceof Word;
            $wordInBase['entered'] = $wordInBase['entered'] + 1;
            $wordInBase->save();
        } else {
            $newWord = new Word();
            $newWord['word'] = $guess;
            $newWord['entered'] = 1;
            $newWord->save();
        }

        // Подсчитаем количество точных и обычных попаданий
        $hits = $kills = 0;
        $word = Yii::$app->session['word'];
        for ($i = 0; $i < 5; $i++) {
            if (mb_substr($guess, $i, 1) == mb_substr($word, $i, 1)) {
                $kills++;
            } elseif (mb_strpos($word, mb_substr($guess, $i, 1)) !== false) {
                $hits++;
            }
        }

        $answer = [
            'kills' => $kills,
            'hits' => $hits,
            'guess' => $guess
        ];
        return $answer;
    }

    /**
     * AJAX-метод
     * возвращает слово КАПСОМ для показа на сайте
     * @return mixed
     */
    public function actionGetword() {
        return mb_strtoupper(Yii::$app->session['word']);
    }

    /**
     * просто инфа
     * @return bool
     */
    public function actionInfo() {
        return phpinfo();
    }

    /**
     * AJAX-метод
     * проверяет введённое предположение, возвращает количество попаданий
     * @return string
     */
    public function actionCheck() {
        $request = Yii::$app->request;
        $guesses = Yii::$app->session['guesses'];
        $guess = $request->post('guess');
        // Если это первая догадка, то начинаем отсчёт времени
        if (!count($guesses)) {
            Yii::$app->session['time'] = time();
        }

        if ($guess = mb_strtolower($guess)) {
            $check = $this->checkGuess($guess);
            $guesses[] = $check;
            Yii::$app->session['guesses'] = $guesses;
        }


        return json_encode($check);
    }

    /**
     * Завершает игру, записывает в базу нужную информацию
     */
    public function actionEndgame() {
        $word = Yii::$app->session['word'];
        $request = Yii::$app->request;
        $win = $request->get('win');

        $wordInBase = Word::findOne(['word' => $word]);
        $wordInBase instanceof Word;
        if ($win == 1 && $wordInBase) {
            // Если слово отгадано, обновим его в БД
            $wordInBase['guessed'] = $wordInBase['guessed'] + 1;
            $wordInBase->save();
        }

        // Записываем результат юзера в таблицу рекордов
        $moves = count(Yii::$app->session['guesses']);
        $time  = time() - Yii::$app->session['time'];
        $score = $win ? round((1010 - 10 * $moves) * pow(0.999, $time)) : 0;

        $writeRes = new Result();
        $writeRes->setAttributes([
            'moves'  => $moves,
            'time'   => $time,
            'score'  => $score,
            'win'    => $win,
            'player' => 'Аноним',
            'word'   => $word,
            'date_played'  => date('Y-m-d H:i:s')
        ]);
        $writeRes->save();
        $resultId = Yii::$app->db->getLastInsertID();

        Yii::$app->session['guesses'] = [];
        unset(Yii::$app->session['time']);
        unset(Yii::$app->session['word']);

        // Вернем статистику по слову
        $stats = [
            'played'  => $wordInBase['played'] . ' ' . $this->raz($wordInBase['played']),
            'guessed' => $wordInBase['guessed'] . ' ' . $this->raz($wordInBase['guessed']),
            'score'   => $score,
            'resId'   => $resultId
        ];
        return json_encode($stats, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Склоняет раз / раза, в зависимости от числительного
     * @param $num
     */
    private function raz($num) {
        if (in_array($num % 10, [2,3,4]) && !in_array($num % 100, [12,13,14])) {
            return 'раза';
        } else {
            return 'раз';
        }
    }

    private function getHighscores(){
        $highscores = Result::find()->where(['win' => 1])->orderBy(['score' => SORT_DESC])->limit(10)->all();
        return $highscores;
    }

    public function actionUpdatehs() {
        $request = Yii::$app->request;
        $name = $request->post('name');
        $result = Result::findOne(['id' => $request->post('id')]);
        $result instanceof Result;
        $result['player'] = $name ? $name : 'Аноним';
        $result->save();
    }

    /**
     * Отправляет на сервер новые слова
     */
    public function actionUploadwords(){
        $url = 'http://wordquest.hol.es/web/word/getwords';
        $output = '';

        $query = (new Query)
            ->from('words')
            ->where(['verified' => 1])
            ->orderBy('id');

        foreach ($query->batch(100) as $wordsArr) {
            $words = [];
            foreach ($wordsArr as $wordObj) {
                $words[] = $wordObj['word'];
            }

            $params = array(
                'words' => implode(',', $words)
            );

            $result = file_get_contents($url, false, stream_context_create(array(
                'http' => array(
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => http_build_query($params)
                )
            )));

            $output .= "<p>$result</p>";
        }
        return $output;
    }

    public function actionGetwords(){
        $updated = 0;
        $added = 0;

        $request = Yii::$app->request;
        $words = $request->post('words');
        $words = explode(',', $words);
        foreach ($words as $word) {
            $wordInBase = Word::findOne(['word' => $word]);
            $wordInBase instanceof Word;
            if (!$wordInBase) {
                $wordInBase = new Word();
                $wordInBase['word'] = $word;
                $wordInBase['verified'] = 1;
                $wordInBase->save();
                $added++;
            } elseif ($wordInBase['verified'] == 0) {
                $wordInBase['verified'] = 1;
                $wordInBase->save();
                $updated++;
            }
        }
        return 'Добавлено слов: ' . $added . '<br/>Обновлено: ' . $updated;
    }

    public function actionVerify(){
        // @todo - сделать доступ только мне
        $words = Word::find()->where(['verified' => 0])->all();
        return $this->render('verify', [
            'words' => $words
        ]);
    }

    public function actionVerifyword(){
        $request = Yii::$app->request;
        $word = $request->get('word');
        $wordInBase = Word::findOne(['word' => $word]);
        $wordInBase instanceof Word;
        $wordInBase['verified'] = 1;
        $wordInBase->save();
    }

    public function actionDeleteword(){
        $request = Yii::$app->request;
        $word = $request->get('word');
        $wordInBase = Word::findOne(['word' => $word]);
        $wordInBase instanceof Word;
        $wordInBase->delete();
    }

    public function actionVerifyall(){
        $request = Yii::$app->request;
        $maxID = intval($request->get('maxId'));
        Yii::$app->db->createCommand()->update('words', ['verified' => 1], ['and', "ID <= {$maxID}", 'verified = 0'])->execute();
    }

    public function actionDeleteall(){
        $request = Yii::$app->request;
        $maxID = intval($request->get('maxId'));
        Yii::$app->db->createCommand()->delete('words', ['and', "ID <= {$maxID}", 'verified = 0'])->execute();
    }

}
