$( document ).ready(function() {
    $('.add-remove-card').on('click', function (e) {
        e.preventDefault();

        $.ajax({
            url: $(this).attr('href'),
            method: 'POST',
            success: function(response) {
                if(response.success) {
                    console.log($(this).data('element'))
                    $($(this).data('element')).remove();
                } else {
                    alert(response.message);
                }
            }
        });
    });
});