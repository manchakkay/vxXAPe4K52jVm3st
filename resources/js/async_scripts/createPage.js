var $_popup_createPage = {
    elements: {},
    fieldSetter: function (field_identifier, event, value) {
        let result = {
            value: value,
            dependencies: {},
        };

        if (field_identifier === "cp_title") {
            let title = {
                "data": {
                    "limit": JSON.parse(this.elements.fields.cp_title.querySelector('.fld-area').getAttribute("limit"))
                },
                "input": this.elements.fields.cp_title.querySelector('.fld-area'),
                "limit": this.elements.fields.cp_title.querySelector('.fld-limit'),
                "tooltip": this.elements.fields.cp_title.querySelector('.fld-tooltip')
            };

            let link = {
                "data": {
                    "limit": JSON.parse(this.elements.fields.cp_link.querySelector('.fld-area').getAttribute("limit")),
                    "context": JSON.parse(this.elements.fields.cp_link.querySelector('.fld-area').getAttribute("context"))
                },
                "limit": this.elements.fields.cp_link.querySelector('.fld-limit'),
                "tooltip": this.elements.fields.cp_link.querySelector('.fld-tooltip'),
            };

            if (link.data.context.mode == "auto") {
                result.dependencies.cp_link = { value: null };
                let link_value = slugify(value, { lower: true, strict: true });

                if (link.data.limit.type == "max") {
                    result.dependencies.cp_link.limit = link.data.limit;
                    if (link_value.length >= link.data.limit.max) {
                        link_value = string_formatter.limit(link_value, link.data.limit.max, "before_char", { char: "-" });
                    }

                    result.dependencies.cp_link.limit.value = link_value.length + " / " + link.data.limit.max;

                }

                result.dependencies.cp_link.value = link_value;
                link.tooltip.classList.remove('active');
            }

            title.tooltip.classList.remove('active');
            result.value = value;

            return result;

        } else if (field_identifier === "cp_link") {

            let link = {
                "data": {
                    "limit": JSON.parse(this.elements.fields.cp_link.querySelector('.fld-area').getAttribute("limit")),
                    "context": JSON.parse(this.elements.fields.cp_link.querySelector('.fld-area').getAttribute("context"))
                },
                "input": this.elements.fields.cp_link.querySelector('.fld-area'),
                "limit": this.elements.fields.cp_link.querySelector('.fld-limit'),
                "tooltip": this.elements.fields.cp_link.querySelector('.fld-tooltip'),
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

$_popup_createPage.elements = {
    'popup': document.querySelector('.popup.createPage'),
    'fields': {
        'cp_title': document.querySelector('.popup.createPage .fld-wrapper[elem-id="cp_title"]'),
        'cp_link': document.querySelector('.popup.createPage .fld-wrapper[elem-id="cp_link"]'),
    }
};