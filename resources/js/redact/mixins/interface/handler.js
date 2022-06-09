// Подмодуль для обработки действий из интерфейса

export default {
    methods: {
        // Обработка ввода в текстовые строки
        $_interface_handle(event, type, block_type, data = {}) {
            this.$_debug_log("debug", "event: check", JSON.stringify(event));
            let elem, block;

            switch (type) {
                case "span":
                    elem = event.target;
                    block = elem.closest('.block-wrapper');
                    this.$_interface_handle__input(event, block_type, block, elem);
                    break;
                case "multiple-input":
                    elem = event.target;
                    block = elem.closest('.block-wrapper');
                    this.$_interface_handle__multipleInput(event, block_type, block, elem, data.class_list);
                    break;
                case "quill":
                    elem = data.element;
                    block = data.block;
                    this.$_interface_handle__quill(event, block_type, block, elem, data.quill_obj);
                    break;
                case "image":
                    elem = event.target;
                    block = elem.closest(".block-wrapper");
                    this.$_interface_handle__image(event, block_type, block, elem, data.preview);
                    break;
                case "table":
                    elem = event.target;
                    block = elem.closest('.block-wrapper');
                    this.$_interface_handle__table(event, block_type, block, elem);
                    break;
                case "multiple-button":
                    elem = event.target;
                    block = elem.closest('.block-wrapper');
                    this.$_interface_handle__multipleButton(event, block_type, block, elem, data.class_list);
                    break;
                default:
                    this.$_debug_log("debug", "event: wrong type found", type, event);
                    break;
            }
        },
        // Обработка однострочных текстовых полей
        $_interface_handle__input(event, block_type, block, elem) {
            // Находит ID блока
            let block_id = block.getAttribute("block-id");
            // Получаем ссылку на объект блока
            let block_obj = this.$_structure_getBlockByID(block_id);

            switch (block_type) {
                case "TITLE01":
                    // Записываем данные
                    block_obj.data.text = elem.innerText;
                    this.$_debug_log("debug", "listener: input", elem.innerText);
                    break;
            }
        },
        // Обработка многострочных текстовых полей
        $_interface_handle__multipleInput(event, block_type, block, elem, class_list) {
            // Находит ID блока
            let block_id = block.getAttribute("block-id");
            // Получаем ссылку на объект блока
            let block_obj = this.$_structure_getBlockByID(block_id);

            switch (block_type) {
                case "QUOTE01":
                    // Записываем данные
                    if (class_list.contains("quote-author-name")) {
                        block_obj.data.author.name = elem.innerText;
                        this.$_debug_log("debug", "listener: multipleInput-name", elem.innerText);
                    } else if (class_list.contains("quote-author-position")) {
                        block_obj.data.author.position = elem.innerText;
                        this.$_debug_log("debug", "listener: multipleInput-position", elem.innerText);
                    }

                    break;
                case "PERSON01":
                    // Записываем данные
                    if (class_list.contains("person-name")) {
                        block_obj.data.name = elem.innerText;
                        this.$_debug_log("debug", "listener: multipleInput-name", elem.innerText);
                    }

                    break;
            }
        },
        // Обработка текстовых полей с редактором Quill
        $_interface_handle__quill(delta, block_type, block, elem, quill_obj) {
            // Находит ID блока
            let block_id = block.getAttribute("block-id");
            // Получаем ссылку на объект блока
            let block_obj = this.$_structure_getBlockByID(block_id);

            switch (block_type) {
                case "TEXT01":
                    // Записываем данные
                    block_obj.data.delta = quill_obj.getContents();
                    block_obj.data.html = block.querySelector('.ql-editor').innerHTML;

                    break;
                case "QUOTE01":
                    // Записываем данные
                    block_obj.data.body.delta = quill_obj.getContents();
                    block_obj.data.body.html = block.querySelector('.ql-editor').innerHTML;

                    break;
                case "PERSON01":
                    // Записываем данные
                    block_obj.data.bio.delta = quill_obj.getContents();
                    block_obj.data.bio.html = block.querySelector('.ql-editor').innerHTML;

                    break;
            }
        },
        // Обработка одиночных изображений
        $_interface_handle__image(event, block_type, block, elem, preview_elem) {
            let target = event.target || event.srcElement;

            // Отмена загрузки / файл не выбран
            if (target.value.length == 0) {
                return;
            }
            // Находит ID блока
            let block_id = block.getAttribute("block-id");
            // Получаем ссылку на объект блока
            let block_obj = this.$_structure_getBlockByID(block_id);

            switch (block_type) {
                case "IMAGE01":
                    this.$_structure_deleteFromServer(block_obj.data.image_id);

                    // Добавление предпросмотра и сохранение файла
                    let IMAGE01_binary_data = [];
                    IMAGE01_binary_data.push(event.target.files[0]);
                    let IMAGE01_file_url = URL.createObjectURL(new Blob(IMAGE01_binary_data));
                    preview_elem.src = IMAGE01_file_url;
                    block.querySelector(".redact-image-wrapper").classList.remove("no-image");
                    block_obj.data.src = event.target.files[0];

                    break;

                case "QUOTE01":
                    this.$_structure_deleteFromServer(block_obj.data.author.image_id);

                    // Добавление предпросмотра и сохранение файла
                    let QUOTE01_binary_data = [];
                    QUOTE01_binary_data.push(event.target.files[0]);
                    let QUOTE01_file_url = URL.createObjectURL(new Blob(QUOTE01_binary_data));
                    preview_elem.src = QUOTE01_file_url;
                    block.querySelector(".redact-image-wrapper").classList.remove("no-image");
                    block_obj.data.author.src = event.target.files[0];

                    break;

                case "PERSON01":
                    this.$_structure_deleteFromServer(block_obj.data.image_id);

                    // Добавление предпросмотра и сохранение файла
                    let PERSON01_binary_data = [];
                    PERSON01_binary_data.push(event.target.files[0]);
                    let PERSON01_file_url = URL.createObjectURL(new Blob(PERSON01_binary_data));
                    preview_elem.src = PERSON01_file_url;
                    block.querySelector(".redact-image-wrapper").classList.remove("no-image");
                    block_obj.data.src = event.target.files[0];

                    break;
            }
        },
        // Обработка таблиц
        $_interface_handle__table(event, block_type, block, elem) {
            // Находит ID блока
            let block_id = block.getAttribute("block-id");
            // Получаем ссылку на объект блока
            let block_obj = this.$_structure_getBlockByID(block_id);

            switch (block_type) {
                case "TABLE01":
                    console.log(event);
                    // Записываем данные
                    block_obj.data.array[event.target.closest('tr').rowIndex][event.target.cellIndex] = event.target.innerHTML;
                    // block_obj.data.array = [];
                    // block.querySelectorAll("tr").forEach((row, row_index) => {
                    //     block_obj.data.array.push([]);
                    //     row.querySelectorAll("td").forEach((cell, cell_index) => {
                    //         block_obj.data.array[row_index].push(cell.innerHTML);
                    //     });
                    // });

                    this.$_debug_log("debug", "listener: table", event.target.innerHTML);

                    break;
            }
        },
        // Обработка множественных кнопок
        $_interface_handle__multipleButton(event, block_type, block, elem, class_list) {
            // Находит ID блока
            let block_id = block.getAttribute("block-id");
            // Получаем ссылку на объект блока
            let block_obj = this.$_structure_getBlockByID(block_id);
            // Получаем таблицу
            let TABLEXX_table_elem = block.querySelector(".redact-table");
            console.log(TABLEXX_table_elem);


            switch (block_type) {
                case "TABLE01":
                    // Записываем данные
                    if (class_list.contains("row")) {
                        if (class_list.contains("add")) {
                            // Вставляем до 48 строк
                            if (TABLEXX_table_elem.rows.length < 48)
                                TABLEXX_table_elem.insertRow(-1);
                            // Если первая строка - добавляем клетку
                            if (TABLEXX_table_elem.rows.length == 1) {
                                TABLEXX_table_elem.rows[0].insertCell(-1);
                            } else {
                                for (let i = 0; i < TABLEXX_table_elem.rows[0].cells.length; i++) {
                                    TABLEXX_table_elem.rows[TABLEXX_table_elem.rows.length - 1].insertCell(-1);
                                }
                            }

                            // Добавляем пустые данные
                            for (let i = 0; i < TABLEXX_table_elem.rows[TABLEXX_table_elem.rows.length - 1].cells.length; i++) {
                                TABLEXX_table_elem.rows[TABLEXX_table_elem.rows.length - 1].cells[i].innerText = "";
                            }

                            this.$_debug_log("debug", "listener: multipleButton-row-add", elem.value);
                        } else if (class_list.contains("del")) {
                            // Удаляем строки до нуля
                            if (TABLEXX_table_elem.rows.length > 0)
                                TABLEXX_table_elem.deleteRow(-1);
                            this.$_debug_log("debug", "listener: multipleButton-row-delete", elem.value);
                        }

                    } else if (class_list.contains("col")) {
                        if (class_list.contains("add")) {
                            // Если нет строк - добавляем одну
                            if (TABLEXX_table_elem.rows.length == 0) {
                                TABLEXX_table_elem.insertRow(-1);
                            }
                            // Вставляем до 12 столбцов
                            for (let i = 0; i < TABLEXX_table_elem.rows.length; i++) {
                                if (TABLEXX_table_elem.rows[i].cells.length < 12) {
                                    TABLEXX_table_elem.rows[i].insertCell(-1);
                                }
                            }

                            // Добавляем пустые данные
                            for (let i = 0; i < TABLEXX_table_elem.rows.length; i++) {
                                TABLEXX_table_elem.rows[i].cells[TABLEXX_table_elem.rows[0].cells.length - 1].innerText = "";
                            }
                            this.$_debug_log("debug", "listener: multipleButton-col-add", elem.value);
                        } else if (class_list.contains("del")) {
                            for (let i = 0; i < TABLEXX_table_elem.rows.length; i++) {
                                if (TABLEXX_table_elem.rows[i].cells.length > 0) {
                                    TABLEXX_table_elem.rows[i].deleteCell(-1);
                                }
                            }
                            // Если не осталось столбцов - удаляем строки
                            if (TABLEXX_table_elem.rows[0].cells.length == 0) {
                                for (let i = 0; i < TABLEXX_table_elem.rows.length; i++) {
                                    TABLEXX_table_elem.deleteRow(-1);
                                }
                            }

                            this.$_debug_log("debug", "listener: multipleButton-col-delete", elem.value);
                        }
                    }

                    TABLEXX_table_elem.querySelectorAll('td').forEach((elem) => {
                        elem.setAttribute('contenteditable', true);
                    });

                    // Записываем данные
                    block_obj.data.array = [];
                    block.querySelectorAll("tr").forEach((row, row_index) => {
                        block_obj.data.array.push([]);
                        row.querySelectorAll("td").forEach((cell, cell_index) => {
                            block_obj.data.array[row_index].push(cell.innerHTML);
                        });
                    });

                    break;
            }
        }
    }
};