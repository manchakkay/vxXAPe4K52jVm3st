var $_popup_editPage = {
    elements: {},
    toggleInit: function (instance) {
        let result = {
            dependencies: {},
        };

        let data = {
            "ep_title_value": instance.content_title,
            "ep_link_value": instance.slug,
            "ep_id_value": instance.id,
        };
        for (let [key, value] of Object.entries(data)) {
            if (key == 'ep_title_value') {
                let title_field = this.elements.fields.ep_title.querySelector('.fld-area');
                let title_limit = this.elements.fields.ep_title.querySelector('.fld-limit');
                let title_limit_data = JSON.parse(title_field.getAttribute("limit"));

                title_limit.textContent = value.length + " / " + title_limit_data.max;

                // Возврат значения
                result.dependencies.ep_title = { value: value };
            } else if (key == 'ep_link_value') {
                let link_field = this.elements.fields.ep_link.querySelector('.fld-area');
                let link_limit = this.elements.fields.ep_link.querySelector('.fld-limit');
                let link_limit_data = JSON.parse(link_field.getAttribute("limit"));

                link_limit.textContent = value.length + " / " + link_limit_data.max;

                // Возврат значения
                result.dependencies.ep_link = { value: value };
            } else if (key == 'ep_id_value') {
                // Возврат значения
                result.dependencies.ep_id = { value: value };
            }
        }

        return result;
    },
    fieldSetter: function (field_identifier, event, value) {
        let result = {
            value: value,
            dependencies: {},
        };

        if (field_identifier === "ep_title") {
            let title = {
                "data": {
                    "limit": JSON.parse(this.elements.fields.ep_title.querySelector('.fld-area').getAttribute("limit"))
                },
                "input": this.elements.fields.ep_title.querySelector('.fld-area'),
                "limit": this.elements.fields.ep_title.querySelector('.fld-limit'),
                "tooltip": this.elements.fields.ep_title.querySelector('.fld-tooltip')
            };

            let link = {
                "data": {
                    "limit": JSON.parse(this.elements.fields.ep_link.querySelector('.fld-area').getAttribute("limit")),
                    "context": JSON.parse(this.elements.fields.ep_link.querySelector('.fld-area').getAttribute("context"))
                },
                "limit": this.elements.fields.ep_link.querySelector('.fld-limit'),
                "tooltip": this.elements.fields.ep_link.querySelector('.fld-tooltip'),
            };

            if (link.data.context.mode == "auto") {
                result.dependencies.ep_link = { value: null };
                let link_value = slugify(value, { lower: true, strict: true });

                if (link.data.limit.type == "max") {
                    result.dependencies.ep_link.limit = link.data.limit;
                    if (link_value.length >= link.data.limit.max) {
                        link_value = string_formatter.limit(link_value, link.data.limit.max, "before_char", { char: "-" });
                    }

                    result.dependencies.ep_link.limit.value = link_value.length + " / " + link.data.limit.max;

                }

                result.dependencies.ep_link.value = link_value;
                link.tooltip.classList.remove('active');
            }

            title.tooltip.classList.remove('active');
            result.value = value;

            return result;

        } else if (field_identifier === "ep_link") {

            let link = {
                "data": {
                    "limit": JSON.parse(this.elements.fields.ep_link.querySelector('.fld-area').getAttribute("limit")),
                    "context": JSON.parse(this.elements.fields.ep_link.querySelector('.fld-area').getAttribute("context"))
                },
                "input": this.elements.fields.ep_link.querySelector('.fld-area'),
                "limit": this.elements.fields.ep_link.querySelector('.fld-limit'),
                "tooltip": this.elements.fields.ep_link.querySelector('.fld-tooltip'),
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

$_popup_editPage.elements = {
    'popup': document.querySelector('.popup.editPage'),
    'fields': {
        'ep_title': document.querySelector('.popup.editPage .fld-wrapper[elem-id="ep_title"]'),
        'ep_link': document.querySelector('.popup.editPage .fld-wrapper[elem-id="ep_link"]'),
        'ep_id': document.querySelector('.popup.editPage .fld-wrapper[elem-id="ep_id"]'),
    }
};
