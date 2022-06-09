// Подмодуль с шаблонами для повторяющихся данных

export default {
    beforeMount() {
        // Шаблон для страницы
        this.REDACT.templates.structure = {
            meta: {
                template: "",
                title: "",
                description: "",
                thumbnail: "",
            },
            blocks: []
        };

        // Шаблон для блока
        this.REDACT.templates.block = {
            block_id: 0,
            block_position: 0,
            block_type: "EMPTY",
            data: {},
        };
    },
    methods: {

    }
};