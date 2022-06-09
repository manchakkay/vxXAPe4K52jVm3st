// Подмодуль для обработки ответов на запросы

export default {
    methods: {
        $_request_handleError(error, action) {
            this.$_debug_log("warning", action, "response: error");

            if (error.response) {
                // Ошибка в полученном запросе
                this.$_debug_log("warning", action, "response: request error");
                this.$_debug_log("debug", action + ": data", JSON.stringify(error.response.data, null, 4));
                this.$_debug_log("debug", action + ": status", error.response.status);
                this.$_debug_log("debug", action + ": headers", error.response.headers);
            } else if (error.request) {
                // Запрос отправлен, но ответ не получен
                this.$_debug_log("warning", action + ": response: no answer", error.request);
            } else {
                // При настройке запроса произошла ошибка
                this.$_debug_log("warning", action + ": response: initialization error", error.message);
            }

            switch (action) {
                case "saveData":
                    this.REDACT.status.saving = false;
                    break;
            }

            return false;
        },
        $_request_handleResponse(response, action) {
            this.$_debug_log("debug", action, "response: success");
            let page_type = response.config.data.get("page_type");

            switch (action) {
                case "loadData":
                case "loadBlocks":
                    // Загрузка структуры
                    this.$_structure_import(page_type, action, response.data[0]);
                    break;
                case "saveData":
                    if (response.data[0].is_files_uploaded) {
                        this.$_structure_import(page_type, "updateFiles", { "meta": response.data[0].files_meta, "files": response.data[0].files_uploaded });
                    }

                    this.REDACT.status.saving = false;
                    break;
                case "publish":
                    this.REDACT.status.message = true;
                    this.REDACT.vars.message = 'Страница будет опубликована в ближайшее время.';
                    this.REDACT.status.publishing = false;
                    setTimeout(() => {
                        this.REDACT.status.message = false;
                    }, 4000);
                    break;
                default:
                    break;
            }

            this.lastHash = hash(this.REDACT);
            return true;
        }
    }
};