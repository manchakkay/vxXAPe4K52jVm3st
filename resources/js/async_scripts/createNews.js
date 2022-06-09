var $_popup_createNews = {
    elements: {},
    fileSetter: function (field_identifier, event) {
        if (field_identifier === "cn_image") {
            let preview = this.elements.fields.cn_image.querySelector('.fld-file-preview');
            let tooltip = this.elements.fields.cn_image.querySelector('.fld-tooltip');

            // Отсоединяем старую ссылку
            if (preview.src != " " && preview.src != null && preview.src != "") {
                try {
                    URL.revokeObjectURL(preview.src);
                } catch (e) {
                    console.debug("INFO: cn_image_fileSetter | URL object not revoked");
                }
            }

            // Присоединяем новую ссылку
            try {
                let preview_binary_data = [];
                preview_binary_data.push(event.target.files[0]);
                preview.src = URL.createObjectURL(new Blob(preview_binary_data));

            } catch (e) {
                console.info("INFO: cn_image_fileSetter | URL object not created");
            }

            tooltip.classList.remove('active');
        }
    },
    fieldSetter: function (field_identifier, event, value) {
        let result = {
            value: value,
            dependencies: {},
        };

        if (field_identifier === "cn_title") {
            let title = {
                "data": {
                    "limit": JSON.parse(this.elements.fields.cn_title.querySelector('.fld-area').getAttribute("limit"))
                },
                "input": this.elements.fields.cn_title.querySelector('.fld-area'),
                "limit": this.elements.fields.cn_title.querySelector('.fld-limit'),
                "tooltip": this.elements.fields.cn_title.querySelector('.fld-tooltip')
            };

            let link = {
                "data": {
                    "limit": JSON.parse(this.elements.fields.cn_link.querySelector('.fld-area').getAttribute("limit")),
                    "context": JSON.parse(this.elements.fields.cn_link.querySelector('.fld-area').getAttribute("context"))
                },
                "limit": this.elements.fields.cn_link.querySelector('.fld-limit'),
                "tooltip": this.elements.fields.cn_link.querySelector('.fld-tooltip'),
            };

            if (link.data.context.mode == "auto") {
                result.dependencies.cn_link = { value: null };
                let link_value = slugify(value, { lower: true, strict: true });

                if (link.data.limit.type == "max") {
                    result.dependencies.cn_link.limit = link.data.limit;
                    if (link_value.length >= link.data.limit.max) {
                        link_value = string_formatter.limit(link_value, link.data.limit.max, "before_char", { char: "-" });
                    }

                    result.dependencies.cn_link.limit.value = link_value.length + " / " + link.data.limit.max;

                }

                result.dependencies.cn_link.value = link_value;
                link.tooltip.classList.remove('active');
            }

            title.tooltip.classList.remove('active');
            result.value = value;

            return result;

        } else if (field_identifier === "cn_link") {

            let link = {
                "data": {
                    "limit": JSON.parse(this.elements.fields.cn_link.querySelector('.fld-area').getAttribute("limit")),
                    "context": JSON.parse(this.elements.fields.cn_link.querySelector('.fld-area').getAttribute("context"))
                },
                "input": this.elements.fields.cn_link.querySelector('.fld-area'),
                "limit": this.elements.fields.cn_link.querySelector('.fld-limit'),
                "tooltip": this.elements.fields.cn_link.querySelector('.fld-tooltip'),
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

$_popup_createNews.elements = {
    'popup': document.querySelector('.popup.createNews'),
    'fields': {
        'cn_title': document.querySelector('.popup.createNews .fld-wrapper[elem-id="cn_title"]'),
        'cn_link': document.querySelector('.popup.createNews .fld-wrapper[elem-id="cn_link"]'),
        'cn_image': document.querySelector('.popup.createNews .fld-wrapper[elem-id="cn_image"]'),
    }
};
