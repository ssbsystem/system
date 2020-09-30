import AutoScroll from "../AutoScroll.js";
import DataURLToBlob from "../objects/DataURLToBlob.js";

export default class Gallery {
    /**
     * Constructor
     * ------------------------------
     * **Values**
     * *Import*
     * *Export*
     * ------------------------------
     * **Events**
     * *Bind*
     * Name: <parentFrameId>_save - Save event
     * *Trigger*
     * Name: <parentFrameId>_child_loaded - Child loaded
     * Name: <parentFrameId>_save_end - End of the save event
     * ------------------------------
     * @param {JSON} plugin 
     * @param {String} frameId 
     * @param {String} parentFrameId 
     */
    constructor(plugin, frameId, parentFrameId) {
        Gallery.load(plugin, frameId, parentFrameId);
        this.events(plugin, parentFrameId, frameId);
    }

    /**
     * Load
     * @param {JSON} plugin 
     * @param {String} frameId 
     * @param {String} parentFrameId 
     */
    static load(plugin, frameId, parentFrameId) {
        document.getElementById(frameId).insertAdjacentHTML(
            'beforeend',
            this.getFrameHTML(frameId)
        );

        let changeData = {};
        changeData.PluginNumber = plugin.Number;
        localStorage.setItem(`${parentFrameId}_child_loaded`, JSON.stringify(changeData));
        $(`#${parentFrameId}`).trigger(`${parentFrameId}_child_loaded`);

        let offset = 0;
        Gallery.loadImages(frameId, parentFrameId, offset)

        AutoScroll.Integration(`${frameId}_cont`);
    }

    /**
     * Load Images
     * @param {String} frameId 
     * @param {String} parentFrameId 
     * @param {String} offset 
     */
    static loadImages(frameId, parentFrameId, offset) {
        let moduleFrameId = parentFrameId.split('_')[0];

        let uploadData = {};
        let className = 'GetImages';
        let changeItem = JSON.parse(localStorage.getItem(`${moduleFrameId}_data_details_id`));

        uploadData['newItemId'] = changeItem['Id'];
        uploadData['newItemColumn'] = changeItem['IdColumn'];
        uploadData['Offset'] = offset;

        $.ajax({
            type: "POST",
            url: "./php/Router.php",
            data: { 'Module': className, 'Data': uploadData },
            success: function (result) {
                console.log(JSON.stringify(result));

                Gallery.createContent(result, frameId, parentFrameId, offset);
            },
            dataType: 'json'
        });
    }

    /**
     * Create Content
     * @param {JSON} images 
     * @param {String} frameId 
     * @param {String} parentFrameId 
     * @param {Number} offset 
     */
    static createContent(data, frameId, parentFrameId, offset) {
        let images = data.Images;
        let state = data.State;

        if (images === undefined || images === null) {
            return;
        }

        for (const imageData of images) {
            document.getElementById(`${frameId}_cont`).insertAdjacentHTML(
                'beforeend',
                Gallery.getImage(frameId, imageData)
            );

            let uploadData = {};
            let className = 'GetOneImage';

            uploadData['ImageURL'] = imageData.URL;

            $.ajax({
                type: "POST",
                url: "./php/Router.php",
                data: { 'Module': className, 'Data': uploadData },
                success: function (result) {
                    let blobString = result.BlobString;
                    let blobFile = DataURLToBlob.Create(blobString);
                    let url = window.URL.createObjectURL(blobFile);

                    let imgId = imageData.IdNo;

                    document.getElementById(`${frameId}_${imgId}`).style = `background: url(${url}) no-repeat center center;`;
                },
                dataType: 'json'
            });
        }

        offset += 10;

        if (state === 'more') {
            document.getElementById(`${frameId}_cont`).insertAdjacentHTML(
                'beforeend',
                `<button id="${frameId}_more_img">More</button>`
            );

            document.getElementById(`${frameId}_more_img`).addEventListener(
                'click',
                function () {
                    Gallery.loadImages(frameId, parentFrameId, offset);
                    this.remove();
                }
            )
        }
    }

    /**
     * Get Image
     * @param {String} frameId 
     * @param {JSON} imageData 
     */
    static getImage(frameId, imageData) {
        let imgId = imageData.IdNo;
        let imgAlt = imageData.Basename;

        return `
            <div id=${frameId}_${imgId} class="gallery-image-content display-flex flex-column justify-content-center" alt="${imgAlt}">
                <p class="position-absolute">${imgAlt}</p>
            </div>
            `;
    }

    /**
     * events
     * @param {String} plugin 
     * @param {String} parentFrameId 
     * @param {String} frameId 
     */
    events(plugin, parentFrameId, frameId) {
        window.addEventListener('resize', function () {
            AutoScroll.Integration(`${frameId}_cont`);
        });

        $(`#${frameId}_add`).bind(`click`, function () {
            $(`#${frameId}_add_input`).click();
        });

        let changeData = {};
        changeData.Add = [];
        $(`#${frameId}_add_input`).bind('change', function () {
            let files = document.getElementById(`${frameId}_add_input`).files;
            let fLength = files.length;
            for (let i = 0; i < fLength; i++) {
                const file = files[i];
                let url = URL.createObjectURL(file);
                console.log(file);

                let reader = new FileReader();

                reader.onloadend = function () {
                    let imgObj = {};
                    imgObj.FileName = file.name;
                    imgObj.Source = reader.result;
                    changeData.Add.push(imgObj);

                    document.getElementById(`${frameId}_cont`).insertAdjacentHTML(
                        'beforeend',
                        `<div class="gallery-item-shell display-flex flex-column  justify-content-center">
                            <a href="${url}" target="_blank"><img class="gallery-item" src="${url}"></a><div>`
                    );

                    let moduleFrameId = parentFrameId.split('_')[0];
                    let detailsIdData = JSON.parse(localStorage.getItem(`${moduleFrameId}_data_details_id`));

                    let uploadData = {};
                    let className = 'InsertImage';
                    let formData = new FormData();

                    uploadData['FileToUpload'] = [];
                    uploadData['FileToUpload'].push(`Gallery_1`);
                    uploadData['EntryId'] = detailsIdData;

                    /** Image converter */
                    let fileName = file.name;
                    let imageURL = reader.result;
                    // Split the base64 string in data and contentType
                    let block = imageURL.split(";");
                    // Get the content type
                    let contentType = block[0].split(":")[1];
                    // get the real base64 content of the file
                    let realData = block[1].split(",")[1];
                    // Convert to blob
                    let blob = Gallery.b64toBlob(realData, contentType);

                    formData.append('Module', className);
                    formData.append(`Gallery_1`, blob, fileName);
                    console.log(uploadData);
                    formData.append('Data', JSON.stringify(uploadData));

                    $.ajax({
                        type: "POST",
                        url: "./php/Router.php",
                        data: formData,
                        contentType: false,
                        processData: false,
                        cache: false,
                        success: function (result) {
                            let tableResultData = {};
                            console.log(result);

                            for (const table in result[0]) {
                                if (result[0].hasOwnProperty(table)) {
                                    tableResultData = result[0][table];
                                }
                            }

                            //if (tableResultData['Result'] === 'S') {
                            if (true) {
                                $(`#${parentFrameId}`).trigger(`${parentFrameId}_save_end`);
                            } else {
                                Swal.fire({
                                    type: 'error',
                                    title: 'Sikertelen',
                                    text: 'A galéria feltöltése sikertelen volt!',
                                    heightAuto: false
                                });
                            }
                        },
                        dataType: 'json'
                    });
                }

                if (file) {
                    reader.readAsDataURL(file);
                }
            }
            $(`#${parentFrameId}`).trigger(`${parentFrameId}_child_loaded`);
        });
    }

    /**
     * getFrameHTML
     */
    static getFrameHTML(frameId) {
        return `
            <h2 class="new-obj-subtitle">Galéria</h2>
            <div id="${frameId}_cont" class="">
                <div class="upload-img-shell">
                    <div id="${frameId}_add" class="img-upload justify-content-center display-flex flex-column">
                        <div class="text-center">
                            <div>
                                <i class="img-upload-icon far fa-file-image"></i>
                            </div>
                            <div>
                                <h6 class="img-upload-text unselectable">Choose images</h6>
                            </div>
                        </div>
                    </div>
                    <input type="file" id="${frameId}_add_input" class="custom-input" multiple
                        accept="image/x-png,image/gif,image/jpeg"/>
                </div>
            </div>`
    }

    /**
     * Convert a base64 string in a Blob according to the data and contentType.
     * 
     * @param b64Data {String} Pure base64 string without contentType
     * @param contentType {String} the content type of the file i.e (image/jpeg - image/png - text/plain)
     * @param sliceSize {Int} SliceSize to process the byteCharacters
     * @see http://stackoverflow.com/questions/16245767/creating-a-blob-from-a-base64-string-in-javascript
     * @return Blob
     */
    static b64toBlob(b64Data, contentType, sliceSize) {
        contentType = contentType || '';
        sliceSize = sliceSize || 512;

        let byteCharacters = atob(b64Data);
        let byteArrays = [];

        for (let offset = 0; offset < byteCharacters.length; offset += sliceSize) {
            let slice = byteCharacters.slice(offset, offset + sliceSize);

            let byteNumbers = new Array(slice.length);
            for (let i = 0; i < slice.length; i++) {
                byteNumbers[i] = slice.charCodeAt(i);
            }

            let byteArray = new Uint8Array(byteNumbers);

            byteArrays.push(byteArray);
        }

        let blob = new Blob(byteArrays, { type: contentType });
        return blob;
    }
}