;
(function ($) {
    /**
     * @class
     * @param options
     * @returns {jQuery}
     */
    $.fn.uploaderImageList = function (options) {
        options = options || {};
        var settings = $.extend({
            /**
             * Список uploaderThumbnail
             */
            images: [],

            /**
             * Адрес конря API серверной части загрузчика
             * @property {string} apiURI - Адрес конря API
             */
            apiURI: '',

            imageURL: function (filename) {
                return filename;
            },
            /** флаг поддержки браузером основных функций загрузчика */
            supported: true
        }, options);

        function init() {
            this
                .addClass('imageList')
                .add(this.images)
                .addSubstitute();
        }

        $.extend(this, {
            /**
             * Добавление одного или нескольких uploaderThumbnail
             * @param filename string|string[]
             * @returns {*}
             */
            add: function (filename) {
                /**
                 * Добавление uploaderThumbnail
                 * @param filename string Имя файла
                 * @returns {*}
                 */
                function addImage(filename) {
                    var _this = this;
                    var $image = $('<div>')
                        .uploaderThumbnail({
                            deleteCallback: function () {
                                _this.removeImage(this);
                            },
                            setDefaultCallback: function () {
                                _this.defaultImage(this);
                            },
                            clickCallback: function () {
                                _this.selectImage(this);
                            },
                            imageURL: this.imageURL,
                            apiURI: this.apiURI,
                            supported: _this.supported
                        });
                    var isFirst = !_this.images.length;
                    _this.images.push($image);
                    $image.insertBefore($('.substitute'));
                    $image.addClass('loading');
                    if (navigator.userAgent.search(/Android|Mac OS/) > -1 && $image.isImageURLLocal(filename)) {
                        _this.clearExifData(filename, function (filename) {
                            $image.filename(filename, function(){
                                if (isFirst) {
                                    _this.defaultImage($image);
                                    _this.selectImage($image);
                                }
                            });
                        });
                    } else {
                        $image.filename(filename, function(){
                            if (isFirst) {
                                _this.defaultImage($image);
                                _this.selectImage($image);
                            }
                        });
                    }

                    return this;
                }

                /**
                 * Добавление нескольких uploaderThumbnail
                 * @param filenames string[] Имена файлов
                 * @returns {*}
                 */
                function addImages(filenames) {
                    var _this = this;
                    $.each(filenames, function () {
                        addImage.call(_this, this);
                    });
                    return this;
                }

                return addImages.call(this, [].concat(filename));
            },
            clearExifData: function(dataUrl, onComplete) {
                var img = new Image();
                $(img).one('load', function() {
                    EXIF.getData(this);

                    var $canvas = $('<canvas>'),
                        ctx = $canvas[0].getContext('2d');
                    if ($.inArray(this.exifdata.Orientation, [3, 6, 8]) > -1) {
                        $canvas.attr({width: this.height, height: this.width});
                    } else {
                        $canvas.attr({width: this.width, height: this.height});
                    }

                    switch (this.exifdata.Orientation) {
                        case 2:
                            ctx.transform(-1, 0, 0, 1, this.width, 0);
                            break;
                        case 3:
                            ctx.transform(-1, 0, 0, -1, this.width, this.height);
                            break;
                        case 4:
                            ctx.transform(1, 0, 0, -1, 0, this.height);
                            break;
                        case 5:
                            ctx.transform(0, 1, 1, 0, 0, 0);
                            break;
                        case 6:
                            ctx.transform(0, 1, -1, 0, this.height, 0);
                            break;
                        case 7:
                            ctx.transform(0, -1, -1, 0, this.height, this.width);
                            break;
                        case 8:
                            ctx.transform(0, -1, 1, 0, 0, this.width);
                            break;
                        default:
                            onComplete(dataUrl);
                            return;
                    }
                    ctx.drawImage(img, 0, 0);
                    ctx.transform(1, 0, 0, 1, 0, 0);

                    onComplete($canvas[0].toDataURL('image/jpeg', 1));
                });
                img.src = dataUrl;
            },
            selectImage: function ($image) {
            },
            defaultImage: function ($image) {
            },
            /**
             * Получение списка имен файлов
             * @param {boolean} [fullPath = false] true - Полный путь до файла<br/>
             *                                     false - Только имя
             * @returns {string[]}
             */
            getImageFilenames: function (fullPath) {
                fullPath = fullPath || false;
                var photos = [];
                $(this.images).each(function() {
                    var filename = this.filename();
                    if (!fullPath && !this.isImageURLLocal(filename)) {
                        filename = filename.match(/(^|\/)([a-f0-9_]+\.jpg)$/, '')[2];
                    }
                    if (this.defaultImage()) {
                        photos.unshift(filename);
                    } else {
                        photos.push(filename);
                    }
                });
                return photos;
            },
            /**
             * Получение списка измененных изображений
             * @returns {uploaderThumbnail[]}
             */
            getModifiedImages: function () {
                return $.makeArray($(this.images).filter(function () {
                    return this.isModified();
                }));
            },
            /**
             * Получение индекса uploaderThumbnail внутри this.images
             * @param $searchImage Искомое uploaderThumbnail
             * @returns {number}
             */
            getIndex: function ($searchImage) {
                var imageIndex = -1;
                $.each(this.images, function (index, $image) {
                    if ($searchImage == $image) {
                        imageIndex = index;
                    }
                });
                return imageIndex;
            },
            /**
             * Удаление uploaderThumbnail из this.images и DOM
             * @param $image uploaderThumbnail
             * @returns {*}
             */
            removeImage: function ($image) {
                var isDeletedDefaultImage = $image.defaultImage(),
                    index = this.getIndex($image);
                this.removeImageByIndex(index);
                if (isDeletedDefaultImage && this.images.length) {
                    this.defaultImage(this.images[0]);
                }
                if (!$('.substitute', this).size()) {
                    this.addSubstitute();
                }
                return this;
            },

            addSubstitute: function() {
                var $substitute = $('<div>').addClass('substitute').click(function () {
                    $('#files').click();
                });

                this.append($substitute);
            },
            /**
             * Удаление uploaderThumbnail из this.images и DOM по индексу
             * @param index Индекс uploaderThumbnail
             * @returns {*}
             */
            removeImageByIndex: function (index) {
                if (index >= 0) {
                    this.images[index].remove();
                    this.images.splice(index, 1);
                }
                return this;
            },
            /**
             * Очистка списка изображений
             * @returns {*}
             */
            empty: function () {
                $.each(this.images, function (index, $image) {
                    $image.remove();
                });
                this.images = [];
                return this;
            }
        }, settings);
        init.call(this);
        return this;
    };
})(jQuery);