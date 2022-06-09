export default {
    methods: {
        fileSetter: function (popup_identifier, field_identifier, event) {
            this.popups.list[popup_identifier].handler.fileSetter(field_identifier, event);
        },
        fileRemove: function (event) {
            let preview = event.target.closest('.fld-file-content').querySelector('.fld-file-preview');
            let input = event.target.closest('.fld-file-content').querySelector('.fld-file-input input');
            let fileTooltip = event.target.closest('.fld-wrapper').querySelector('.fld-tooltip');

            input.value = "";

            if (preview.src != " " && preview.src != null && preview.src != "") {
                try {
                    URL.revokeObjectURL(preview.src);
                } catch (e) {
                    console.info("WARN: fileRemove | URL object not revoked");
                }
            }
            preview.src = " ";

            fileTooltip.classList.remove('active');
        },
    },
};
