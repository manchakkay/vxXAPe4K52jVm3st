// Подмодуль для контроля слушателей в интерфейсе

export default {
    updated() {
        this.$nextTick(function () {
            if (this.REDACT.status.init.listeners) {
                this.$_interface_updateBlocks();
            } else {
                this.REDACT.status.init.listeners = true;
                this.REDACT.status.structure_updated = true;
                this.$_interface_updateBlocks();
                this.lastHash = hash(this.REDACT);
            }
        });
    },
    methods: {
        // Создание и обновление группы слушателей
        $_interface_listenersBlock(event, type, block_type, block_elem, data = {}) {
            switch (type) {
                case "quill":
                    // Создаём новый слушатель
                    data.quill_obj.on(event, (e) =>
                        this.$_interface_handle(e, type, block_type, { "quill_obj": data.quill_obj, "element": data.quill_elem, "block": block_elem }));
                    break;
                case "multiple-input":
                    // Создаём слушатель для нескольких полей ввода
                    let inputs_array = block_elem.querySelectorAll('span[contenteditable]');
                    inputs_array.forEach((input_elem) => {
                        input_elem.addEventListener("input", (e) =>
                            this.$_interface_handle(e, type, block_type, { "class_list": input_elem.classList }));
                        input_elem.addEventListener("paste", (e) => {
                            e.preventDefault();
                            document.execCommand(
                                "inserttext",
                                false,
                                e.clipboardData.getData("text/plain")
                            );
                        });
                    });
                    break;
                case "span":
                    // Создаём слушатель для одного поля ввода
                    let input_elem = block_elem.querySelector('span[contenteditable]');
                    input_elem.addEventListener("input", (e) =>
                        this.$_interface_handle(e, type, block_type));
                    input_elem.addEventListener("paste", (e) => {
                        e.preventDefault();
                        document.execCommand(
                            "inserttext",
                            false,
                            e.clipboardData.getData("text/plain")
                        );
                    });
                    break;
                case "image":
                    // Создаём слушатель для загруженных картинок
                    block_elem.querySelector('input[type="file"]').addEventListener("change", (e) =>
                        this.$_interface_handle(e, type, block_type, { "preview": data.preview }));
                    data.preview.onload = function () {
                        URL.revokeObjectURL(data.preview.src);
                    };
                    break;
                case "table":
                    // Создаём слушатель для таблицы
                    let table_elem = block_elem.querySelector('table');
                    table_elem.addEventListener("input", (e) =>
                        this.$_interface_handle(e, type, block_type));
                    table_elem.addEventListener("paste", (e) => {
                        e.preventDefault();
                        document.execCommand(
                            "inserttext",
                            false,
                            e.clipboardData.getData("text/plain")
                        );
                    });
                    break;
                case "multiple-button":
                    // Создаём слушатели для нескольких кнопок
                    let button_array = block_elem.querySelectorAll('button');
                    button_array.forEach((button_elem) => {
                        button_elem.addEventListener("click", (e) =>
                            this.$_interface_handle(e, type, block_type, { "class_list": button_elem.classList }));
                    });
                    break;
            }
        },
    }
};