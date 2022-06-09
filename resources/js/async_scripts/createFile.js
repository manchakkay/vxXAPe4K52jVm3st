var $_popup_createFile = {
    elements: {},
    fileSetter: function (field_identifier, event) {
        if (field_identifier === "cf_file") {
            let filename = this.elements.fields.cf_file.querySelector('.fld-file-name');
            let tooltip = this.elements.fields.cf_file.querySelector('.fld-tooltip');

            name = event.target.files[0].name;
            console.log(name);
            filename.textContent = name;

            tooltip.classList.remove('active');
        }
    },
    fieldSetter: function (field_identifier, event, value) {
        return;
    },
};

$_popup_createFile.elements = {
    'popup': document.querySelector('.popup.createFile'),
    'fields': {
        'cf_file': document.querySelector('.popup.createFile .fld-wrapper[elem-id="cf_file"]'),
    }
};
