export default {
    methods: {
        draggableDelete: function (popup_identifier, field_identifier, item_index) {
            this.popups.list[popup_identifier].fields[field_identifier].data.list =
                this.popups.list[popup_identifier].fields[field_identifier].data.list.filter(
                    (
                        value,
                        arrIndex
                    ) => {
                        return (
                            item_index !==
                            arrIndex
                        );
                    }
                );

            if (this.popups.list[popup_identifier].fields[field_identifier].data.list.length == 0) {
                this.draggableUploader(popup_identifier, field_identifier);
            }

            return true;
        },
        draggableUploader: function (popup_identifier, field_identifier) {
            this.popups.list[popup_identifier].fields[field_identifier].data.list.push(
                {
                    preview: null,
                }
            );
        },
        draggableSetter: function (popup_identifier, field_identifier, item_index, event) {
            if (event.target.value.length != 0) {
                let preview_binary_data = [];
                preview_binary_data.push(event.target.files[0]);

                this.popups.list[popup_identifier].fields[field_identifier].data.list[item_index] = {
                    preview: URL.createObjectURL(new Blob(preview_binary_data)),
                };

                event.target.closest('.fld-draggable-item').querySelector('.fld-draggable-image-preview').src = this.popups.list[popup_identifier].fields[field_identifier].data.list[item_index].preview;
                this.draggableUploader(popup_identifier, field_identifier);
            }
        },
    },
};
