$('document').ready(function () {
    imagesUploader = $('.imageUploader').imageUploader(
        {
            imageURL: function (filename) {
                return '/images/uploaded/800x800/' + filename;
            },
            apiURI: '/app_dev.php/admin/photos/uploader/'
        }
    ).add([]);

    $('#saveButton').click(function() {
        imagesUploader.save({
            onComplete: function () {
                if (imagesUploader.getImageFilenames(true).length) {
                    alert('Фотографии успешно сохрнены!');
                    window.location.reload();
                }
            },
            onError: function () {
                console.log('error');
            }
        });
    });

    $('.delete').click(function() {
        if (confirm("Вы действительно хотите удалить эту фотограффию?")) {
            var $image = $(this).closest('.image'),
                photoId = $image.find('img').attr('photo_id');
            $image.remove();
            $.ajax({
                url: '/admin/photos/' + photoId + '/delete/',
                method: 'POST',
                success: function(data) {
                    if (!data.success) {
                        alert('Что то пошло не так! Попробуйте снова!');
                    }
                }
            });
        }
    });
});
