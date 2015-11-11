;
/**
 * Copyright ЗАО "ИД "Пронто-центр", 2015
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 *
 * @summary jQuery плагин для загрузки и изменения изображений.
 * @description jQuery плагин для загрузки и изменения изображений.
 * @version 1.0 beta
 * @file uploader.js
 * @author Ховаев Андрей, Биндасов Сергей
 * @overview Этот плагин реализует загрузчик изображений для с функционалом их модификации
 * (масштабирование, поворот с шагом 90&deg;).
 * <br />
 * <br />
 * @copyright ЗАО "ИД "Пронто-центр", 2015
 * @license Apache, Version 2.0 ({@link http://www.apache.org/licenses/LICENSE-2.0})
 * @requires jQuery
 */

/**
 * See the jQuery Library ({@link http://api.jquery.com}) for full details.
 *
 * @name jQuery
 * @constructor
 * @class
 * See the jQuery Library ({@link http://api.jquery.com}) for full details.
 * This just documents the function and classes that are added to jQuery
 * by this plugin.
 * @see {@link http://api.jquery.com}
 */

/**
 * See the jQuery Library ({@link http://api.jquery.com}) for full details.<br />
 * See <b>{@link fn}</b> for all added methods.
 *
 * @name fn
 * @alias jQuery.prototype
 * @memberOf jQuery
 * @property {object} fn - See the jQuery Library ({@link http://api.jquery.com})
 *                         for full details. This just documents the function and
 *                         classes that are added to jQuery by this plugin.
 * @see {@link http://api.jquery.com} for jQuery property fn
 * and {@link fn} for all functions, which are added to the fn property
 */
(function ($) {
    /**
     * @class
     * @param {Object} options
     * @returns {jQuery}
     */
    $.fn.imageUploader = function (options) {
        var $imageList,
            $form,
            $this = this;
        /**
         * Адрес конря API серверной части загрузчика
         * @property {string} apiURI - Адрес конря API
         */
        this.apiURI = '';
        /**
         * Папка, для сохранение изображений
         * @property {string} preset - Папка
         */
        this.preset = 'ads';
        /**
         * Максимальное количество фотографий
         * @property {int} [maxImages = 8] - Допустимое количество фотографий
         */
        this.maxImages = 8;
        /**
         * Правило формирования пути файла
         * @param {string} filename - Имя файла
         * @returns {string}
         */
        this.imageURL = function (filename) {
            return filename;
        };

        /**
         * Проверяет поддержку функций загрузки файлов
         * @function
         * @returns {boolean} true - полный функционал,<br/>
         *                    false - только прсмотр
         */
        var isSupported = function () {
            return typeof FileReader != 'undefined';
        };

        /**
         * Обработчик события при успешном завершении всей операции сохранения
         * @callback saveCompleteCallback
         */

        /**
         * Обработчик события при успешном завершении каждого изображения
         * @callback saveProgressCallback
         * @param {int} savedCount Количество сохраненных на данный момент файлов
         * @param {int} totalCount Общее количество сохраняемых файлов
         */

        /**
         * Обработчик события при ошибке сохранения
         * @callback saveErrorCallback
         */

        /**
         * Сохранение измененных и новых изображений
         * @param {Object} options - обработчики событий
         * @param {saveCompleteCallback} [options.onComplete = null] При успешном завершении всей операции сохранения
         * @param {saveProgressCallback} [options.onProgress = null] При успешном завершении каждого изображения
         * @param {saveErrorCallback} [options.onError = null] При ошибке сохранения
         */
        function save(options) {
            var onProgress = options.onProgress,
                onComplete = options.onComplete,
                onError = options.onError;
            var $modifiedImages = $imageList.getModifiedImages(),
                _this = this,
                savedCount = 0, totalLength = $modifiedImages.length;
            if (totalLength) {
                $.each($modifiedImages, function () {
                    this.save(function () {
                        savedCount++;
                        onProgress && onProgress.call(_this, savedCount, totalLength);
                        if (savedCount == totalLength) {
                            onComplete && onComplete.call(_this);
                        }
                    }, function () {
                        onError && onError.call(_this);
                    });
                })
            } else {
                onComplete && onComplete.call(_this);
            }
        }

        function createUploadControls() {
            var $files = $('<input id="files" type="file" accept= "image/*" multiple="true">'),
                $this = this,
                filesProcessed,
                errorLog,
                fileCount;

            $files.appendTo($this);
            var onLoadEndCallback = function (file, $this) {
                return function (e) {
                    var dataURL = e.target.result;
                    if (typeof file.verifiedType != 'undefined' && file.verifiedType) {
                        dataURL = setMimeTypeInRawDataURL(dataURL, file.verifiedType);
                    }
                    if (dataURL.match(/^data:image/)) {
                        $this.add(dataURL);
                    } else {
                        errorLog.push(file.name);
                    }
                    if ((fileCount == ++filesProcessed) && errorLog.length) {
                        alert([].concat(['Файл(ы):', errorLog.join('\n'), '', 'имеют недопустимый формат.']).join('\n'));
                    }
                };
            };

            $files.click(function () {
                $(this).val('');
            });

            $files.change(function () {
                filesProcessed = 0;
                errorLog = [];
                fileCount = this.files.length;
                if (fileCount) {
                    for (var i = 0, f; f = this.files[i]; i++) {
                        if (f.type) {
                            var readerDataURL = new FileReader();
                            readerDataURL.onloadend = (function (file) {
                                return onLoadEndCallback(file, $this);
                            })(f);
                            readerDataURL.readAsDataURL(f);
                        } else {
                            var readerArrayBuffer = new FileReader();
                            readerArrayBuffer.onloadend = (function (file) {
                                return function (e) {
                                    file.verifiedType = mimeFromArrayBuffer(e.target.result);
                                    var reader = new FileReader();
                                    reader.onloadend = (function (file) {
                                        return onLoadEndCallback(file, $this);
                                    })(file);
                                    reader.readAsDataURL(file);
                                }
                            })(f);
                            readerArrayBuffer.readAsArrayBuffer(f);
                        }
                    }
                }
            });

            function getRequirementsSrc() {
                if (location.host.match(/\.by/)) {
                    return '/помощь/293/61/'
                }
                return '/помощь/335/61/';
            }

            var $browseFile, $imageRequirements;
            $imageRequirements = $('<div>').addClass('requirements');
            $imageRequirements.html('<a href="' + getRequirementsSrc() + '" target="_blank">Требования к изображениям</a>');

            $browseFile = $('<div>').attr({id: 'download'}).text('Загрузить');
            $browseFile.click(function () {
                $files.click();
            });

            if (this.maxImages > 1) {
                var $imageRestrictions = $('<div>').addClass('restrictions');
                $imageRestrictions.text('Максимально 8 фото');
                this.append($imageRestrictions).append($imageRequirements);

                $browseFile.css({'display': 'none'});
                $this.append($browseFile);
            } else {
                var $oneImageBlock = $('<div></div>').addClass('oneImageBlock');
                $oneImageBlock.append($browseFile).append($imageRequirements);
                this.append($oneImageBlock);
            }
        }

        /**
         * Инициализация плагина
         */
        function init() {
            $imageList = $('<div></div>')
                .uploaderImageList({
                    maxImages: this.maxImages,
                    imageURL: this.imageURL,
                    apiURI: this.apiURI,
                    preset: this.preset
                });

            this.append($form);
            this.append($imageList);
            createUploadControls.call(this);
        }

        /**
         * Иницализация в режиме чтения
         */
        function initOldBrowser() {
            this.append($imageList);

            this.append($('<div>').addClass('warning').html('<div class="heading">Уважаемый пользователь!</div><div class="text">Ваш браузер устарел. Для корректной загрузки фото обновите версию браузера.</div>'));

        }

        /**
         * Добавление изображений к списку
         * @param {string|string[]} filename Имя файла/dataURL или массив из них
         * @returns {imageUploader}
         */
        this.add = function (filename) {
            filename && $imageList.add(filename);
            return this;
        };

        /**
         * Применение изменений в выбранном изображении и сохранение всех несохраненных изменений
         * @param {Object} options - обработчики событий
         * @param {saveCompleteCallback} [options.onComplete = null] При успешном завершении всей операции сохранения
         * @param {saveProgressCallback} [options.onProgress = null] При успешном завершении каждого изображения
         * @param {saveErrorCallback} [options.onError = null] При ошибке сохранения
         */
        this.save = function (options) {
            save.apply(this, arguments);
        };

        /**
         * Получение списка имен файлов изображение
         * @param {boolean} [fullPath = false] true - Полный путь до файла<br/>
         *                                     false - Только имя
         * @returns {string[]} Список имен файлов изображений. Главное изображение - первое в списке.
         */

        this.getImageFilenames = function (fullPath) {
            return $imageList.getImageFilenames(fullPath);
        };

        /**
         * Установка главного изображения
         * @param {uploaderThumbnail} $image
         */
        this.defaultImage = function ($image) {
            $imageList.defaultImage($image);
        };

        $.extend(this, options);
        if (isSupported()) {
            init.call(this);
        } else {
            initOldBrowser.call(this);
        }
        return this;
    };
})(jQuery);