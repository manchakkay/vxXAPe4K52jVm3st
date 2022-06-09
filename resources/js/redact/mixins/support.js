// Корневая примесь сопровождающих функций

export default {
    methods: {
        // Безопасное копирование объектов
        $_support_copyObject(source) {
            return JSON.parse(JSON.stringify(Object.assign({}, source)));
        },
        // Обработка неполных ссылок на файлы
        $_support_parseFileURL(source) {
            return source;
        },
    },
};