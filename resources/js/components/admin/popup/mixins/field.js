export default {
    methods: {
        fieldSetter: function (popup_identifier, field_identifier, event) {
            let field_obj = this.popups.list[popup_identifier].fields[field_identifier];
            let value = event.target.value;

            // Общее форматирование
            if (field_obj.data.setter_options.includes('NO_DOUBLE_SPACES')) {
                value = value.replace(/ +(?= )/g, '');
            }
            if (field_obj.data.setter_options.includes('NO_TABS')) {
                value = value.replace(/[\t]/gm, "");
            }
            if (field_obj.data.setter_options.includes('NO_NEW_LINES')) {
                value = value.replace(/[\n\r]/gm, "");
            }
            if (field_obj.data.setter_options.includes('LINK_MASK')) {
                value = value.replace(/[\s]/gm, "-");
                value = value.replace(/[^a-z0-9-]/gm, "");
            }

            if (field_obj.data.limit.type == "max") {
                if (value.length >= field_obj.data.limit.max) {
                    value = string_formatter.limit(value, field_obj.data.limit.max);
                }

                field_obj.data.limit.value = value.length + " / " + field_obj.data.limit.max;
            }

            if (this.popups.list[popup_identifier].handler != undefined) {
                // Частное форматирование
                let result = this.popups.list[popup_identifier].handler.fieldSetter(field_identifier, event, value);

                // Установка нового значения
                this.popups.list[popup_identifier].fields[field_identifier].data.value = result.value;
                // Установка новых значений в зависимых полях
                if (result.dependencies && result.dependencies != null) {
                    for (let [dep_identifier, dep_newdata] of Object.entries(result.dependencies)) {
                        for (let [dep_key, dep_value] of Object.entries(dep_newdata)) {
                            this.popups.list[popup_identifier].fields[dep_identifier].data[dep_key] = dep_value;
                        }
                    }
                }
            } else {
                this.popups.list[popup_identifier].fields[field_identifier].data.value = value;
            }

        },
    }
};