// Корневая примесь подсистемы запросов

// Импортируем подмодули
import REDACT_request$constructor from "./request/constructor";
import REDACT_request$handler from "./request/handler";
import REDACT_request$sender from "./request/sender";

export default {
    mixins: [REDACT_request$constructor, REDACT_request$handler, REDACT_request$sender],
    beforeMount() {
        this.$_request_defineCSRF();
    },
    methods: {
        // Определение CSRF ключа
        $_request_defineCSRF() {
            this.csrf = document.querySelector('meta[name="csrf-token"]').content;
            this.REDACT.status.init.csrf = true;
        }
    }
};