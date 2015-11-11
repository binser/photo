function mimeFromArrayBuffer(dataURL) {
    var signature = new DataView(dataURL).getUint32(0, false).toString(16),
        mimeType = "";
    switch (true) {
        case signature == '89504e47':
            mimeType = "image/png";
            break;
        case signature == '47494638':
            mimeType = "image/gif";
            break;
        case signature.slice(0, 2) == '424d':
            mimeType = "image/bmp";
            break;
        case signature == 'ffd8ffe0':
        case signature == 'ffd8ffe1':
        case signature == 'ffd8ffe2':
        case signature == 'ffd8ffe3':
        case signature == 'ffd8ffe8':
            mimeType = "image/jpeg";
            break;
    }
    return mimeType;
}

function setMimeTypeInRawDataURL(dataURL, mimeType) {
    return dataURL.replace(/^data:base64/, 'data:' + mimeType + ';base64');
}