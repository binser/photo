$(document).ready(function () {
    $('.enabled', '.action').click(function() {
        if (confirm("Вы действительно хотит отключит отображение этого альбома?")) {
            var id = $(this).parent().siblings('.id').text();
            $.ajax(
                '/admin/albums/' + id + '/delete/'
            );
            console.log(id);
        }
    });

    $('.disabled', '.action').click(function() {

    });

    $('.edit', '.action').click(function() {

    });

    $('.delete', '.action').click(function() {

    });
});