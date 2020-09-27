
/**
 * Data URL To Blob
 */
export default class DataURLToBlob {
    /**
     * Create
     * @param {String} blobString 
     * return @param {Blob}  
     */
    static Create(blobString) {
        let BASE64_MARKER = ';base64,';
        if (blobString.indexOf(BASE64_MARKER) == -1) {
            let parts = blobString.split(',');
            let contentType = parts[0].split(':')[1];
            let raw = decodeURIComponent(parts[1]);
            return new Blob([raw], { type: contentType });
        }
        let parts = blobString.split(BASE64_MARKER);
        let contentType = parts[0].split(':')[1];
        let raw = window.atob(parts[1]);
        let rawLength = raw.length;
        let uInt8Array = new Uint8Array(rawLength);
        for (let i = 0; i < rawLength; ++i) {
            uInt8Array[i] = raw.charCodeAt(i);
        }
        return new Blob([uInt8Array], { type: contentType });
    }
}