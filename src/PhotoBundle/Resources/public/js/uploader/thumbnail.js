;
(function ($) {
    /**
     * @class
     * @param options
     * @returns {jQuery}
     */
    $.fn.uploaderThumbnail = function (options) {
        var isDefault = false,
            filename = '',
            $image,
            imageSrc,
            matrix,
            isModified = false,
            initialMatrix;
        var settings = $.extend({
                deleteCallback: function () {
                },
                setDefaultCallback: function () {
                },
                /**
                 * Адрес конря API серверной части загрузчика
                 * @property {string} apiURI - Адрес конря API
                 */
                apiURI: '',
                imageURL: function (filename) {
                    return filename;
                },
                supported: true

            }, options
        );

        function init() {
            this.addClass('image');
            createImage.call(this);
            createControls.call(this);
            addEvents.call(this);
        }

        function initOldBrowser() {
            this.addClass('image');
            this.css({overflow: 'hidden'});
            createImage.call(this);
            addEvents.call(this);
        }

        function createImage() {
            if (this.supported) {
                var $block = $('<div>').css({overflow: 'hidden', width: 0, height: 0});
                $image = $('<img>');
                $block.append($image);
                this.append($block);
            } else {
                $image = $('<img>');
                this.append($image);
            }

        }

        function createControls() {
            this
                .append($('<div></div>').addClass('hint--bottom').attr('data-hint', 'Удалить')
                .append($('<div></div>').addClass('delete')));
            this.defaultImage(isDefault);
        }

        function addEvents() {
            var _this = this;
            $(this).click(function () {
                _this.clickCallback();
            });
            $('.delete', this).click(function () {
                _this.deleteCallback.call(_this);
            });
            $('.default', this).click(function () {
                _this.setDefaultCallback.call(_this);
            });
        }

        $.extend(this, {
            /**
             * Установка/чтение имени файла изображения
             * @param {string} [newFilename] Новое имя файла
             * @param [onComplete]
             * @returns {*} Если задано newFilename - возвращает изображение, иначе имя файла
             */
            filename: function (newFilename, onComplete) {
                var _this = this;
                if (typeof newFilename == 'undefined') {
                    return filename.toString();
                }
                filename = newFilename;
                if (this.isImageURLLocal(filename)) {
                    isModified = true;
                } else {
                    filename = this.imageURL(filename);
                }
                if (!imageSrc) {
                    imageSrc = filename;
                }
                return preview.prototype.setImageURL($image, filename, function () {
                    if (!initialMatrix) {
                        initialMatrix = previewMedium.getDefaultMatrix($image, false);
                        _this.matrix(initialMatrix);
                        _this.fit();
                    }
                    onComplete && onComplete();
                }, function() {
                    _this.removeClass('loading').addClass('errorLoading');
                    _this.unbind('click');
                    $('.default', _this).unbind('click');
                    $('.default span', _this).html('');
                });
            },
            image: function ($newImage) {
                if (typeof $newImage == 'undefined') {
                    return $image;
                }
                $image.remove();
                $image = $newImage;
                this.append($image);
                return $image;
            },
            /**
             * Установка.чтение матрицы преобразований изображения
             * @param {Array} [newMatrix]
             * @returns {*} Если задано newMatrix - возвращает изображение, иначе матрицу
             */
            matrix: function (newMatrix) {
                if (typeof newMatrix == 'undefined') {
                    return matrix;
                }
                if (matrix) {
                    isModified = true;
                }
                matrix = newMatrix;
                return $image;
            },

            isModified: function () {
                return isModified;
            },
            initialMatrix: function () {
                return initialMatrix;
            },
            /**
             * Установка/чтение флага 'по умолчанию'
             * ВНИМАНИЕ!!! НЕ ИСПОЛЬЗОВАТЬ!!! ТОЛЬКО ЧЕРЕЗ uploaderThumbnail.defaultImage
             * @param state
             * @returns {*}
             */
            defaultImage: function (state) {
                if (typeof state == 'undefined') {
                    return isDefault;
                }
                isDefault = !!state;
                $('.default', this).toggle(!isDefault);
                $('.isDefault', this).toggle(isDefault);
                return this;
            },
            fit: function () {
                var _this = this;
                if (typeof window.HTMLCanvasElement != 'undefined') {
                    previewSmall.build(this.filename(), this.matrix(), false, previewSmall.scaleTo(previewMedium), function ($canvas) {
                        _this.image($canvas);
                        _this.removeClass('loading');
                    });
                } else {
                    var image = _this.image(),
                        width = image.width() / previewLarge.scaleTo(previewSmall),
                        height = image.height() / previewLarge.scaleTo(previewSmall);
                    image.width(width);
                    image.height(height);
                    image.css({
                        'margin-left': ((previewSmall.width - width) / 2) + 'px',
                        'margin-top': ((previewSmall.height - height) / 2) + 'px'
                    });
                    _this.removeClass('loading');
                }
            },
            isImageURLLocal: function (src) {
                return !!src.match(/^data/) || !src;
            },
            send: function ($canvas, matrix, onComplete, onError) {
                var _this = this,
                    xhr = new XMLHttpRequest();

                xhr.onload = function () {
                    var response = JSON.parse(this.response);
                    if (response.success) {
                        _this.filename(response.fileName);
                        onComplete && onComplete.call(this);
                    } else {
                        alert(response.errorMessage.join('\n'));
                        onError && onError.call(this);
                    }
                };

                xhr.onerror = function () {
                    alert('Ошибка связи с сервером');
                };

                function getTypeImage(dataType) {
                    return dataType.match(/\/(.*)$/)[1];
                }

                function sendFileOnServer(binaryData, type) {
                    var boundaryString = '1BEF0A57BE110FD467A',
                        boundary = '--' + boundaryString,
                        requestBody = boundary + '\r\n';

                    xhr.open('POST', _this.apiURI, true);

                    requestBody += 'Content-Disposition: form-data; name="albumID"\r\n\r\n' + activeAlbumID + '\r\n' + boundary + '\r\n';
                    requestBody +=
                        'Content-Disposition: form-data; name="uploadedImages"; filename="1.' + getTypeImage(type) + '"' + '\r\n'
                            + 'Content-Type: ' + type + '\r\n'
                            + '\r\n'
                            + binaryData // бинарное содержимое файла
                            + '\r\n'
                            + boundary;

                    xhr.setRequestHeader("Content-type", 'multipart/form-data; boundary="' + boundaryString + '"');


                    try {
                        if (typeof XMLHttpRequest.prototype.sendAsBinary == 'undefined') {
                            XMLHttpRequest.prototype.sendAsBinary = function (text) {
                                var data = new ArrayBuffer(text.length);
                                var ui8a = new Uint8Array(data, 0);
                                for (var i = 0; i < text.length; i++) ui8a[i] = (text.charCodeAt(i) & 0xff);
                                this.send(ui8a);
                            }
                        }
                        xhr.sendAsBinary(requestBody);
                    } catch (e) {
                        xhr.send(requestBody);
                    }
                }

                function sendBlob(blob) {
                    var reader = new FileReader(), result;

                    if (typeof reader.readAsBinaryString !== 'undefined') {
                        reader.readAsBinaryString(blob);
                        reader.onloadend = (function () {
                            sendFileOnServer(reader.result, blob.type, blob.size);
                        });
                    } else {
                        reader.readAsDataURL(blob);
                        reader.onloadend = (function () {
                            result = base64.decode(reader.result.replace(/^(.*?base64,)/, ''));
                            sendFileOnServer(result, blob.type, blob.size);
                        });
                    }
                }

                function sendDataUrl(dataUrl) {
                    var binaryString = base64.decode(dataUrl.replace(/^(.*?base64,)/, '')),
                        type = dataUrl.match(/^.*?:(.*?);/)[1];

                    sendFileOnServer(binaryString, type, binaryString.length);
                }

                try {
                    $canvas[0].toBlob(function (blob) {
                        sendBlob(blob);
                    }, "image/jpeg", 1);
                } catch (e) {
                    var dataUrl = $canvas[0].toDataURL();
                    sendDataUrl(dataUrl);
                }
            },
            save: function (onComplete, onError) {
                var _this = this;
                previewLarge.build(this.filename(), this.matrix(), false, previewLarge.scaleTo(previewMedium), function ($canvas) {
                    _this.send($canvas, this.newMatrix, function () {
                        onComplete && onComplete.call(this);
                        isModified = false;
                        initialMatrix = _this.matrix();
                    }, onError);
                });
            }
        }, settings);
        if (this.supported) {
            init.call(this);
        } else {
            initOldBrowser.call(this);
        }
        return this;
    };
})(jQuery);