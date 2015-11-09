$(document).ready(function () {
    var $action = $('.action');
    $action.on('click', '.enabled', function() {
        if (confirm("Вы действительно хотите отключить отображение этого альбома?")) {
            var id = $(this).parent().siblings('.id').text(),
                $this = $(this);
            $.ajax({
                url: '/admin/albums/' + id + '/disabled/',
                success: function(data) {
                    if (data.success) {
                        $this.attr({
                            'class': 'disabled',
                            'title': 'Включить отображение'
                        });
                    }
                }
            });
        }
    });

    $action.on('click', '.disabled', function() {
        if (confirm("Вы действительно хотите включить отображение этого альбома?")) {
            var id = $(this).parent().siblings('.id').text(),
                $this = $(this);
            $.ajax({
                url: '/admin/albums/' + id + '/enabled/',
                success: function(data) {
                    if (data.success) {
                        $this.attr('class', 'enabled');
                    }
                }
            });
        }

    });

    $('.edit', '.action').click(function() {

    });

    $('.delete', '.action').click(function() {
        if (confirm("Вы действительно хотите удалить этот альбом?")) {

        }
    });
});