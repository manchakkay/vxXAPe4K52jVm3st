// Корневая примесь подсистемы структуры и данных

// Импортируем подмодули
import REDACT_structure$export from "./structure/export";
import REDACT_structure$import from "./structure/import";
import REDACT_structure$template from "./structure/template";

export default {
    mixins: [
        REDACT_structure$export,
        REDACT_structure$import,
        REDACT_structure$template
    ],
};