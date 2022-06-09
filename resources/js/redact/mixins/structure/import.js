// Подмодуль для ввода данных

export default {
    methods: {
        // Импорт информации в редактор
        $_structure_import(page_type, action, data) {
            switch (action) {
                case "loadData":
                    this.$_debug_log("debug", "structure: import", "data");
                    this.$_structure_import__loadData(page_type, data.instance);
                    break;
                case "loadBlocks":
                    this.$_debug_log("debug", "structure: import", "templates");
                    this.$_structure_import__loadBlocks(page_type, data);
                    break;
                case "updateFiles":
                    this.$_debug_log("debug", "structure: import", "files!");
                    this.$_structure_import__updateFiles(page_type, data);
                    break;
            }
        },
        // Импорт данных
        $_structure_import__loadData(page_type, data) {
            this.REDACT.status.structure_updated = true;

            switch (page_type) {
                case "news":
                    // Загрузка первоначальной структуры
                    if (data == null || data == undefined || data.content_json == null || data.content_json == undefined) {
                        this.REDACT.structure = this.$_support_copyObject(this.REDACT.templates.structure);
                    } else {
                        this.REDACT.structure = JSON.parse(data.content_json);
                    }
                    break;

                case "page":
                    // Загрузка первоначальной структуры
                    if (data == null || data == undefined || data.content_json == null || data.content_json == undefined) {
                        this.REDACT.structure = this.$_support_copyObject(this.REDACT.templates.structure);
                    } else {
                        this.REDACT.structure = JSON.parse(data.content_json);
                    }
                    break;
            }

            this.REDACT.status.init.data = true;
            this.$_structure_updatePos();
            this.$_initializeTemplates(page_type);
        },
        // Импорт шаблонов блоков
        $_structure_import__loadBlocks(page_type, data) {
            this.REDACT.status.structure_updated = true;

            switch (page_type) {
                case "news":
                    // Загрузка шаблонов для блоков
                    if (data == null || data == undefined || data.templates_set == null || data.templates_set == undefined || data.templates == null || data.templates == undefined) {
                        this.$_debug_log("error", "initredactTemplates", "Block templates empty or not defined");
                    } else {
                        this.REDACT.blocks = data;
                    }
                    break;

                case "page":
                    // Загрузка шаблонов для блоков
                    if (data == null || data == undefined || data.templates_set == null || data.templates_set == undefined || data.templates == null || data.templates == undefined) {
                        this.$_debug_log("error", "initredactTemplates", "Block templates empty or not defined");
                    } else {
                        this.REDACT.blocks = data;
                    }
                    break;
            }

            this.REDACT.status.init.templates = true;
            this.$_activate();
        },
        // Обновление ссылок на файлы
        $_structure_import__updateFiles(page_type, data) {
            this.REDACT.status.structure_updated = true;
            this.$_debug_log("debug", "structure: import", page_type);

            switch (page_type) {
                case "news":

                    for (let [block_id, block_files] of Object.entries(JSON.parse(data.meta))) {
                        let block = this.$_structure_getBlockByID(block_id);
                        // Замена идентификаторов изображений на ссылки
                        switch (block.block_type) {
                            case "IMAGE01":
                                block.data.src = data.files[block_files[0]].src;
                                block.data.image_id = data.files[block_files[0]].image_id;
                                break;
                            case "QUOTE01":
                                block.data.author.src = data.files[block_files[0]].src;
                                block.data.author.image_id = data.files[block_files[0]].image_id;
                                break;
                            case "PERSON01":
                                block.data.src = data.files[block_files[0]].src;
                                block.data.image_id = data.files[block_files[0]].image_id;
                                break;
                        }
                        this.$_interface_updateFiles(block);
                    }

                    break;

                case "page":
                    for (let [block_id, block_files] of Object.entries(JSON.parse(data.meta))) {
                        let block = this.$_structure_getBlockByID(block_id);
                        // Замена идентификаторов изображений на ссылки
                        switch (block.block_type) {
                            case "IMAGE01":
                                block.data.src = data.files[block_files[0]].src;
                                block.data.image_id = data.files[block_files[0]].image_id;
                                break;
                            case "QUOTE01":
                                block.data.author.src = data.files[block_files[0]].src;
                                block.data.author.image_id = data.files[block_files[0]].image_id;
                                break;
                            case "PERSON01":
                                block.data.src = data.files[block_files[0]].src;
                                block.data.image_id = data.files[block_files[0]].image_id;
                                break;
                        }
                        this.$_interface_updateFiles(block);
                    }
                    break;
            }
            this.lastHash = hash(this.REDACT);
        },

        // Поиск блока в структуре по block_id
        $_structure_getBlockByID(id) {
            let result = this.REDACT.structure.blocks.find(obj => {
                return obj.block_id == id;
            });

            return result;
        },
        // Поиск индекса блока в структуре по block_id
        $_structure_getBlockIndexByID(id) {
            let result = this.REDACT.structure.blocks.findIndex(obj => {
                return obj.block_id == id;
            });

            return result;
        },
        // Добавление файлов в список на удаление
        $_structure_deleteFromServer(file_id) {
            if (typeof file_id === 'number' && file_id > 0 && !this.REDACT.deleted_files.includes(file_id)) {
                this.REDACT.deleted_files.push(file_id);
            }
        },
        // Обновление позиций после перещения
        $_structure_updatePos() {
            this.REDACT.structure.blocks.forEach((element, index) => {
                element.block_position = index;
            });
        }

    }
};