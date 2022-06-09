var $_popup_editNews = {
    elements: {},
    toggleInit: function (instance) {
        let result = {
            dependencies: {},
        };

        let data = {
            "en_image_src": (instance.thumbnail != null) ? instance.thumbnail.url : '',
            "en_title_value": instance.content_title,
            "en_desc_value": (instance.content_description != null ? instance.content_description : ''),
            "en_categ_value": (instance.category != null ? instance.category.id : 0),
            "en_link_value": instance.slug,
            "en_id_value": instance.id,
        };
        for (let [key, value] of Object.entries(data)) {
            if (key == 'en_image_src') {
                this.fileSetter('en_image', null, value);
            } else if (key == 'en_title_value') {
                let title_field = this.elements.fields.en_title.querySelector('.fld-area');
                let title_limit = this.elements.fields.en_title.querySelector('.fld-limit');
                let title_limit_data = JSON.parse(title_field.getAttribute("limit"));

                title_limit.textContent = value.length + " / " + title_limit_data.max;

                // Возврат значения
                result.dependencies.en_title = { value: value };
            } else if (key == 'en_desc_value') {
                let desc_field = this.elements.fields.en_desc.querySelector('.fld-area');
                let desc_limit = this.elements.fields.en_desc.querySelector('.fld-limit');
                let desc_limit_data = JSON.parse(desc_field.getAttribute("limit"));

                desc_limit.textContent = value.length + " / " + desc_limit_data.max;

                // Возврат значения
                result.dependencies.en_desc = { value: value };
            } else if (key == 'en_link_value') {
                let link_field = this.elements.fields.en_link.querySelector('.fld-area');
                let link_limit = this.elements.fields.en_link.querySelector('.fld-limit');
                let link_limit_data = JSON.parse(link_field.getAttribute("limit"));

                link_limit.textContent = value.length + " / " + link_limit_data.max;

                // Возврат значения
                result.dependencies.en_link = { value: value };
            } else if (key == 'en_categ_value' && this.elements.fields.en_categ.querySelector('.fld-select') != null) {
                setTimeout(() => {
                    this.elements.fields.en_categ.querySelector('.fld-select').value = value;
                }, 300);
            } else if (key == 'en_id_value') {
                // Возврат значения
                result.dependencies.en_id = { value: value };
            }
        }

        return result;
    },
    fileSetter: function (field_identifier, event, url = false) {
        if (field_identifier === "en_image") {
            let preview = this.elements.fields.en_image.querySelector('.fld-file-preview');
            let tooltip = this.elements.fields.en_image.querySelector('.fld-tooltip');

            // Отсоединяем старую ссылку
            if (preview.src != " " && preview.src != null && preview.src != "") {
                try {
                    URL.revokeObjectURL(preview.src);
                } catch (e) {
                    console.debug("INFO: en_image_fileSetter | URL object not revoked");
                }
            }

            // Присоединяем новую ссылку
            try {
                if (!url) {
                    let preview_binary_data = [];
                    preview_binary_data.push(event.target.files[0]);
                    preview.src = URL.createObjectURL(new Blob(preview_binary_data));
                } else {
                    preview.src = url;
                }

            } catch (e) {
                console.info("INFO: en_image_fileSetter | URL object not created");
            }

            tooltip.classList.remove('active');
        }
    },
    fieldSetter: function (field_identifier, event, value) {
        let result = {
            value: value,
            dependencies: {},
        };

        if (field_identifier === "en_title") {
            let title = {
                "data": {
                    "limit": JSON.parse(this.elements.fields.en_title.querySelector('.fld-area').getAttribute("limit"))
                },
                "input": this.elements.fields.en_title.querySelector('.fld-area'),
                "limit": this.elements.fields.en_title.querySelector('.fld-limit'),
                "tooltip": this.elements.fields.en_title.querySelector('.fld-tooltip')
            };

            let link = {
                "data": {
                    "limit": JSON.parse(this.elements.fields.en_link.querySelector('.fld-area').getAttribute("limit")),
                    "context": JSON.parse(this.elements.fields.en_link.querySelector('.fld-area').getAttribute("context"))
                },
                "limit": this.elements.fields.en_link.querySelector('.fld-limit'),
                "tooltip": this.elements.fields.en_link.querySelector('.fld-tooltip'),
            };

            if (link.data.context.mode == "auto") {
                result.dependencies.en_link = { value: null };
                let link_value = slugify(value, { lower: true, strict: true });

                if (link.data.limit.type == "max") {
                    result.dependencies.en_link.limit = link.data.limit;
                    if (link_value.length >= link.data.limit.max) {
                        link_value = string_formatter.limit(link_value, link.data.limit.max, "before_char", { char: "-" });
                    }

                    result.dependencies.en_link.limit.value = link_value.length + " / " + link.data.limit.max;

                }

                result.dependencies.en_link.value = link_value;
                link.tooltip.classList.remove('active');
            }

            title.tooltip.classList.remove('active');
            result.value = value;

            return result;

        } else if (field_identifier === "en_link") {

            let link = {
                "data": {
                    "limit": JSON.parse(this.elements.fields.en_link.querySelector('.fld-area').getAttribute("limit")),
                    "context": JSON.parse(this.elements.fields.en_link.querySelector('.fld-area').getAttribute("context"))
                },
                "input": this.elements.fields.en_link.querySelector('.fld-area'),
                "limit": this.elements.fields.en_link.querySelector('.fld-limit'),
                "tooltip": this.elements.fields.en_link.querySelector('.fld-tooltip'),
            };

            if (value.length > 0) {
                link.data.context.mode = "manual";
            } else {
                link.data.context.mode = "auto";
            }
            link.input.setAttribute("context", JSON.stringify(link.data.context));

            link.tooltip.classList.remove('active');
            result.value = value;

            return result;
        }

        return result;

    },
};

$_popup_editNews.elements = {
    'popup': document.querySelector('.popup.editNews'),
    'fields': {
        'en_title': document.querySelector('.popup.editNews .fld-wrapper[elem-id="en_title"]'),
        'en_desc': document.querySelector('.popup.editNews .fld-wrapper[elem-id="en_desc"]'),
        'en_link': document.querySelector('.popup.editNews .fld-wrapper[elem-id="en_link"]'),
        'en_image': document.querySelector('.popup.editNews .fld-wrapper[elem-id="en_image"]'),
        'en_categ': document.querySelector('.popup.editNews .fld-wrapper[elem-id="en_categ"]'),
        'en_id': document.querySelector('.popup.editNews .fld-wrapper[elem-id="en_id"]'),
    }
};
