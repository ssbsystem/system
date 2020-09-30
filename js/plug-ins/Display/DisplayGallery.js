import DetailsDesigns from "../../designs/DetailsDesigns.js";
import AutoScroll from "../AutoScroll.js";
import DataURLToBlob from "../objects/DataURLToBlob.js";

export default class DisplayGallery {
    /**
     * Constructor
     * ------------------------------
     * **Events**
     *   <parentFrameId>_child_loaded
     * ------------------------------
     * @param {JSON} plugin 
     * @param {String} parentFrameId 
     */
    constructor(plugin, frameId, parentFrameId) {
        //Details.create(plugin, frameId, parentFrameId);
        this.events(plugin, frameId, parentFrameId);

        let changeData = {};
        changeData.PluginNumber = plugin.Number;
        localStorage.setItem(`${parentFrameId}_child_loaded`, JSON.stringify(changeData));
        $(`#${parentFrameId}`).trigger(`${parentFrameId}_child_loaded`);
    }

    /**
     * Create
     * @param {JSON} plugin 
     * @param {String} frameId 
     * @param {String} parentFrameId 
     * @param {String} titleFrameId 
     */
    static create(plugin, frameId, parentFrameId, titleFrameId) {
        //Tab title
        document.getElementById(titleFrameId).insertAdjacentHTML(
            'beforeend',
            DetailsDesigns.getSimpleTitleFrame(frameId, parentFrameId, plugin.Data['1'].Title)
        )
        document.getElementById(`${frameId}_tab`).addEventListener(
            'click',
            function () {
                $(`.${parentFrameId}_tab`).removeClass('btn-detail-menu-active');
                $(`.${parentFrameId}_content`).hide();
                $(`#${frameId}`).show();
                $(`#${frameId}_tab`).addClass('btn-detail-menu-active');
            }
        )

        let offset = 0;
        DisplayGallery.loadImages(frameId, parentFrameId, offset);
    }

    /**
     * Create Content
     * @param {JSON} data 
     * @param {String} frameId 
     * @param {String} parentFrameId 
     * @param {String} offset 
     */
    static createContent(data, frameId, parentFrameId, offset) {
        let frameElement = document.getElementById(frameId);
        let images = data.Images;
        let state = data.State;

        if (images === undefined || images === null) {
            return;
        }

        for (const imageData of images) {
            //step number & name
            frameElement.insertAdjacentHTML(
                'beforeend',
                DisplayGallery.getImage(frameId, imageData)
            )

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
            frameElement.insertAdjacentHTML(
                'beforeend',
                `<button id="${frameId}_more_img">More</button>`
            );

            document.getElementById(`${frameId}_more_img`).addEventListener(
                'click',
                function () {
                    DisplayGallery.loadImages(frameId, parentFrameId, offset);
                    this.remove();
                }
            )
        }
    }

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
     * Events
     * @param {JSON} plugin 
     * @param {String} frameId 
     * @param {String} parentFrameId 
     */
    events(plugin, frameId, parentFrameId) {
        $(`#${parentFrameId}`).bind(`${parentFrameId}_change_details_co_${plugin.Number}`, function (e) {
            // Retrieve the data from storage
            let changeData = JSON.parse(localStorage.getItem(`${parentFrameId}_change_details_co_${plugin.Number}`));
            let titleFrameId = changeData.TitleFrameId;

            DisplayGallery.create(changeData.Plugin, frameId, parentFrameId, titleFrameId);
            AutoScroll.Integration(`${parentFrameId}_content`);
        });

        $(`#${parentFrameId}`).bind(`${parentFrameId}_change_details_co`, function () {
            let changeData = {};
            changeData.PluginNumber = plugin.Number;
            localStorage.setItem(`${parentFrameId}_child_loaded`, JSON.stringify(changeData));
            $(`#${parentFrameId}`).trigger(`${parentFrameId}_child_loaded`);
        });
    }

    /**
     * Load Images
     * @param {String} frameId 
     * @param {String} parentFrameId 
     * @param {Number} offset 
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

                DisplayGallery.createContent(result, frameId, parentFrameId, offset);
            },
            dataType: 'json'
        });
    }
}