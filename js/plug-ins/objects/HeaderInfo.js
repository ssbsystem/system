/**
 * Header Info
 */
export default class HeaderInfo {
    /**
     * Create
     * @param {String} text 
     * @param {String} type s: success, i: info, e: error
     */
    static Create(text, type) {
        $("#page_header.success-back").removeClass("success-back");
        $("#page_header.info-back").removeClass("info-back");
        $("#page_header.info-error").removeClass("error-back");

        switch (type) {
            case 's':
                $("#page_header").addClass("success-back");
                break;
            case 'i':
                $("#page_header").addClass("info-back");
                break;
            case 'e':
                $("#page_header").addClass("error-back");
                break;
        }

        $("#page_header_title").text(text);
        $(`#page_header`).show();
    }

    /**
     * End
     */
    static End(text, state) {
        HeaderInfo.Create(text, state);

        setTimeout(function () {
            $(`#page_header`).hide();
        }, 2000);
    }
}