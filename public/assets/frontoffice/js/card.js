$( document ).ready(function() {
    $('.add-remove-card').on('click', function (e) {
        e.preventDefault();

        var $id = $($(this).data('element'))

        $.ajax({
            url: $(this).attr('href'),
            method: 'POST',
            success: function(response) {
                if(response.success) {
                    $id.remove();
                    if ($('.add-remove-card').length === 0) {
                        $('#tbody-table').append('<tr><td colspan="2">Votre panier est vide!</td></tr>');
                    }
                } else {
                    alert(response.message);
                }
            }
        });
    });
});