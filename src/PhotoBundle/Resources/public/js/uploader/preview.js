var preview = function (width, height) {
    this.width = width;
    this.height = height;
};

/***
 * @param image
 * @param src
 * @param [onComplete]
 * @param [onError]
 * @returns {*|HTMLElement}
 */
preview.prototype.setImageURL = function (image, src, onComplete, onError) {
    onComplete && $(image).one('load', onComplete);
    onError && $(image).one('error', onError);
    $(image).attr('src', src);
    return $(image);
};

preview.prototype.scaleTo = function (preview) {
    return this.width / preview.width;
};

/**
 * Построение начальной матрицы преобразования изображения
 * @param {HTMLImageElement} image Изображение
 * @param {boolean} isMatrixCorrect Нормальная ли матрица или с поправкой для panzoom
 */
preview.prototype.getDefaultMatrix = function (image, isMatrixCorrect) {
    function round(val, precision) {
        var precisionValue = Math.pow(10, precision);
        return Math.round(val * precisionValue) / precisionValue;
    }

    var imageWidth = $(image)[0].width,
        imageHeight = $(image)[0].height,
        centerX = 0,
        centerY = 0,
        scale = Math.min(Math.min(this.width / imageWidth, this.height / imageHeight), 1);
    if (!isMatrixCorrect) {
        centerX = ((scale - 1) * imageWidth) / 2;
        centerY = ((scale - 1) * imageHeight) / 2;
    }
    imageWidth = imageWidth * scale;
    imageHeight = imageHeight * scale;
    return [round(scale, 6), 0, 0, round(scale, 6), round((this.width - imageWidth) / 2 + centerX, 6), round((this.height - imageHeight) / 2 + centerY, 6)];
};

preview.prototype.build = function (src, matrix, isMatrixCorrect, finalScale, onComplete) {
    var image = new Image(), _this = this;
    this.setImageURL(image, src, function () {
        var $canvas;
        if (!matrix) {
            matrix = _this.getDefaultMatrix(image, true);
        }
        var offsetX = 0, offsetY = 0, cx = +matrix[4], cy = +matrix[5];
        if (!isMatrixCorrect) {
            offsetX = (image.width / 2) + (+matrix[4]);
            offsetY = ((image.height / 2) + (+matrix[5]));
            var scx = image.width / 2, scy = image.height / 2;
            cx = -(matrix[0] * scx + matrix[2] * scy);
            cy = -(matrix[1] * scx + matrix[3] * scy);
        }
        _this.newMatrix = [matrix[0], matrix[1], matrix[2], matrix[3], cx + offsetX, cy + offsetY];
        $canvas = _this.render({
            finalScale: finalScale,
            matrix: _this.newMatrix,
            image: image
        });
        onComplete.call(_this, $canvas);
    });
};

preview.prototype.getBinaryData = function (src, matrix, isMatrixCorrect, finalScale, onComplete) {
    var _this = this;
    this.build(src, matrix, isMatrixCorrect, finalScale, function ($canvas) {
        onComplete.call(_this, base64decode($canvas[0].toDataURL('image/jpeg', 1).replace(/^data:[a-z]*;,/, '')));
    });
};

preview.prototype.render = function (options) {
    var $canvas = $('<canvas>').attr({width: this.width, height: this.height}),
        ctx = $canvas[0].getContext('2d');

    ctx.setTransform(options.finalScale, 0, 0, options.finalScale, 0, 0);
    ctx.transform.apply(ctx, options.matrix);

    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, options.image.width, options.image.height);

    ctx.drawImage(options.image, 0, 0);
    ctx.transform(1, 0, 0, 1, 0, 0);

    return $canvas;
};

var previewSmall = new preview(200, 200);

var previewMedium = new preview(200, 200);

var previewLarge = new preview(800, 800);

previewLarge.render = function (options) {
    function getScale(matrix) {
        return Math.max(Math.abs(matrix[0]), Math.abs(matrix[1]));
    }
    var scale = getScale(options.matrix),
        isVertical = Math.abs(options.matrix[1]),
        frameWidth = this.width,
        frameHeight = this.height,
        imageWidth = isVertical ? options.image.height : options.image.width,
        imageHeight = isVertical ? options.image.width : options.image.height;
    if (scale * imageWidth * options.finalScale < frameWidth) {
        options.matrix[4] -= (frameWidth/options.finalScale - scale * imageWidth)/2;
        frameWidth = scale * imageWidth * options.finalScale;
    }
    if (scale * imageHeight * options.finalScale < frameHeight) {
        options.matrix[5] -= (frameHeight/options.finalScale - scale * imageHeight)/2;
        frameHeight = scale * imageHeight * options.finalScale;
    }
    var $canvas = $('<canvas>').attr({width: frameWidth, height: frameHeight}),
        ctx = $canvas[0].getContext('2d');
    ctx.setTransform(options.finalScale, 0, 0, options.finalScale, 0, 0);
    ctx.transform.apply(ctx, options.matrix);

    ctx.fillStyle = '#ffffff';
    ctx.fillRect(0, 0, imageWidth, imageHeight);

    ctx.drawImage(options.image, 0, 0);
    ctx.setTransform(1, 0, 0, 1, 0, 0);
    return $canvas;
};
