$(document).ready(function () {
    var $action = $('.action');
    $action.on('click', '.enabled', function() {
        if (confirm("Вы действительно хотите отключить отображение этого альбома?")) {
            var id = $(this).parent().siblings('.id').text(),
                $this = $(this);
            $.ajax({
                url: '/admin/albums/' + id + '/disabled/',
                method: 'POST',
                success: function(data) {
                    if (data.success) {
                        $this.attr({
                            'class': 'disabled',
                            'title': 'Включить отображение'
                        });
                    } else {
                        alert('Что то пошло не так! Попробуйте снова!');
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
                method: 'POST',
                success: function(data) {
                    if (data.success) {
                        $this.attr({
                            'class': 'enabled',
                            'title': 'Отключить отображение'
                        });
                    } else {
                        alert('Что то пошло не так! Попробуйте снова!');
                    }
                }
            });
        }

    });

    $('.delete', '.action').click(function() {
        if (confirm("Вы действительно хотите удалить этот альбом и все фотограффии в нем?")) {
            var id = $(this).parent().siblings('.id').text();
            $.ajax({
                url: '/admin/albums/' + id + '/delete/',
                method: 'POST',
                success: function(data) {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Что то пошло не так! Попробуйте снова!');
                    }
                }
            });
        }
    });

    $('.edit', '.action').click(function() {
        var id = $(this).parent().siblings('.id').text();
        document.location.href = '/admin/albums/' + id + '/edit/';
    });
});