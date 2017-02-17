$(function(){
    var timerObj;
    var resId = false;
    var timeCount = $('.timeCount span').data('start');
    if (timeCount > 0) {
        startTimer();
    }

    $.mask.definitions['r'] = "[а-яА-Я]";
    $('#wordInput').mask('rrrrr');

    $('#wordInputForm').submit(function(){
        var err = false;
        var word = $('#wordInput').val();
        if (word.length < 5) {
            err = true;
        } else {
            for (var i = 0; i < 5; i++) {
                for (var j = i+1; j < 5; j++) {
                    if (word[i] == word[j]) {
                        err = true;
                    }
                }
            }
        }

        if (err) {
            alert ("Слово долно содержать 5 различных букв!");
            return false;
        } else {
            $.post('check', {guess: word}, function(answer){
                // Начинаем отсчет времени, если нужно
                if (timeCount == 0) {
                    timeCount = 1;
                    startTimer();
                }
                // Добавляем строчку в таблицу и обновляем кол-во ходов
                console.log(answer);
                answer = JSON.parse(answer);
                var newRow = '<tr style="display: none; background-color: #BF9;">' +
                    '<td class="guessWord">' + answer.guess + '</td>' +
                    '<td class="guessKills">' + answer.kills + '</td>' +
                    '<td class="guessHits">' + answer.hits + '</td></tr>';
                $(newRow).appendTo($('#guessesTable tbody')).show(1800, function(){
                    setTimeout(function(){
                        $('#guessesTable tbody tr').last().css({backgroundColor: ''});
                    }, 500);
                });

                $('.movesCount span').text(parseInt($('.movesCount span').text()) + 1);
                /* Если 5 точных из 5, значит победа :) */
                if (answer.kills == 5) {
                    showWord('#090');
                    $('#wordInputForm').hide();
                    $('.resignButton').hide();
                    $('.startGame').show();
                    $('.status').css({color: 'green'}).text('Мерзкий смертный, ты победил меня!');
                    endgame(1);
                }
            });
            return false;
        }
    });

    /* Анимировано показывает слово, делая буквы заданным цветом */
    function showWord(color){
        $.get('getword', {}, function(word){
            $('.letter span').animate({opacity : 0}, 1000, function(){
                for (var i = 0; i < 5; i++) {
                    $('.letter').eq(i).find('span').text(word[i]);
                }
                $('.letter span').css({color : color}).animate({opacity: 1}, 2000);
            });
        });
    }

    $('.resignButton').click(function(){
        if (confirm('Хотите сдаться?')) {
            showWord('#600');
            $('#wordInputForm').hide();
            $('.resignButton').hide();
            $('.startGame').show();
            $('.status').css({color: 'red'}).text('Вы проиграли. Муа-ха-ха!');
            endgame(0);
        }
    });

    $('.scoreHsOk').click(function(){
        /* Обновляем запись в таблице рекордов */
        $.post('updatehs', {id: resId, name: $('#scorePopUp .scoreHsInput').val()});
        $('#overlay').hide();
        $('#overlay #scorePopUp').hide();
        $('#scorePopUp .scoreHsForm').hide();
    });

    /* Заканчивает игру и показывает статистику, учитывая победу или поражение в текущей игре */
    function endgame(result) {
        clearInterval(timerObj);
        $.get('endgame', {win: result}, function(stats){
            stats = JSON.parse(stats);
            console.log('Result ID = ' + stats.resId);
            resId = stats.resId;
            $('#wordStats .playedNum').text(stats.played);
            $('#wordStats .guessedNum').text(stats.guessed);
            $('#wordStats').show();
            if (result == 1) {
                /* Выводим всплывашку со счётом */
                $('#overlay').show(500);
                $('#overlay #scorePopUp').show(1000);
                $('#scorePopUp .scoreVal').text(stats.score);
                $('#scorePopUp .scoreHsForm').show();
            }
        });
    }

    // Обновляет таймер
    function refreshTimer(){
        timeCount++;
        var min = Math.floor(timeCount / 60);
        var sec = timeCount % 60;
        if (sec < 10) {
            sec = '0' + sec;
        }
        var timer = min + ':' + sec;
        $('.timeCount span').html(timer);
    }

    function startTimer() {
        timerObj = setInterval(function(){
            refreshTimer();
        }, 1000);
    }



})