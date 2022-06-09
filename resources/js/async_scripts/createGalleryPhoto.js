var $_popup_createGalleryPhoto = {
    elements: {},
    fileSetter: function (field_identifier, event) {
        if (field_identifier === "cgp_list") {
            let tooltip = this.elements.fields.cgp_list.querySelector('.fld-tooltip');

            tooltip.classList.remove('active');
        }
    },
    fieldSetter: function (field_identifier, event, value) {
        return {
            value: value
        };
    },
};

$_popup_createGalleryPhoto.elements = {
    'popup': document.querySelector('.popup.createGalleryPhoto'),
    'fields': {
        'cgp_list': document.querySelector('.popup.createGalleryPhoto .fld-wrapper[elem-id="cgp_list"]'),
    }
};
