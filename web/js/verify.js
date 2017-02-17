$(function(){
    var maxId = $('#maxId').val();

    $('.verifyWord').click(function(){
        $.get('verifyword', {word: $(this).closest('tr').data('word')});
        $(this).closest('tr').remove();
    });

    $('.deleteWord').click(function(){
        $.get('deleteword', {word: $(this).closest('tr').data('word')});
        $(this).closest('tr').remove();
    });

    $('.verifyAll').click(function(){
        $.get('verifyall', {maxId: maxId}, function(){
           location.reload();
        });
    });

    $('.deleteAll').click(function(){
        $.get('deleteall', {maxId: maxId}, function(){
            location.reload();
        });
    });
})