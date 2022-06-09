// Подмодуль для вывода данных

export default {
    methods: {
        // Экспорт информации из редактора
        $_structure_export(page_type, action) {
            let result = {};

            switch (action) {
                case "saveData":
                    this.$_debug_log("debug", "structure: export", "data");

                    result.files = this.$_structure_export__saveDataFiles(page_type);
                    result.json = this.$_structure_export__saveDataJSON(page_type);

                    break;
            }

            return result;
        },
        // Экспорт данные для полного сохранения
        $_structure_export__saveDataJSON(page_type) {
            let result = {};

            switch (page_type) {
                case "news":
                    result = JSON.stringify(this.REDACT.structure);
                    break;
                case "page":
                    result = JSON.stringify(this.REDACT.structure);
                    break;
            }

            return result;
        },
        // Экспорт файлов для полного сохранения
        $_structure_export__saveDataFiles(page_type) {
            let files_array = {};
            let files_meta = {};
            let image_id = 0;
            let file_name = "";

            this.REDACT.structure.blocks.forEach((block) => {
                let file_index = 0;
                switch (block.block_type) {
                    case "IMAGE01":
                        if (block.data.src != undefined && block.data.src != null && block.data.src instanceof File) {
                            // Определяем название файла
                            file_name = "$FILE$_" + image_id;
                            // Добавляем файл с объект с файлами
                            files_array[image_id] = block.data.src;
                            // Добавляем в объект с инф. о файлах блок
                            files_meta[block.block_id] = {};
                            // Добавляем ассоциируем картинку в блоке с отправляемой картинкой
                            files_meta[block.block_id][file_index++] = file_name;
                            // Заменяем файл на название файла
                            block.data.src = file_name;
                            block.data.image_id = "$ID" + file_name;

                            image_id++;
                        } else {
                            let index = this.REDACT.deleted_files.indexOf(block.data.src);
                            if (index !== -1) {
                                this.REDACT.deleted_files.splice(index, 1);
                            }
                        }
                        break;
                    case "QUOTE01":
                        if (block.data.author.src != undefined && block.data.author.src != null && block.data.author.src instanceof File) {
                            // Определяем название файла
                            file_name = "$FILE$_" + image_id;
                            // Добавляем файл с объект с файлами
                            files_array[image_id] = block.data.author.src;
                            // Добавляем в объект с инф. о файлах блок
                            files_meta[block.block_id] = {};
                            // Добавляем ассоциируем картинку в блоке с отправляемой картинкой
                            files_meta[block.block_id][file_index++] = file_name;
                            // Заменяем файл на название файла
                            block.data.author.src = file_name;
                            block.data.author.image_id = "$ID" + file_name;

                            image_id++;
                        } else {

                            let index = this.REDACT.deleted_files.indexOf(block.data.author.src);
                            if (index !== -1) {
                                this.REDACT.deleted_files.splice(index, 1);
                            }
                        }
                        break;
                    case "PERSON01":
                        if (block.data.src != undefined && block.data.src != null && block.data.src instanceof File) {
                            // Определяем название файла
                            file_name = "$FILE$_" + image_id;
                            // Добавляем файл с объект с файлами
                            files_array[image_id] = block.data.src;
                            // Добавляем в объект с инф. о файлах блок
                            files_meta[block.block_id] = {};
                            // Добавляем ассоциируем картинку в блоке с отправляемой картинкой
                            files_meta[block.block_id][file_index++] = file_name;
                            // Заменяем файл на название файла
                            block.data.src = file_name;
                            block.data.image_id = "$ID" + file_name;

                            image_id++;
                        } else {

                            let index = this.REDACT.deleted_files.indexOf(block.data.src);
                            if (index !== -1) {
                                this.REDACT.deleted_files.splice(index, 1);
                            }
                        }
                        break;
                }

            });

            return { "files_array": files_array, "files_meta": files_meta };
        },
        // Получение списка файлов на удаление
        $_structure_getDeletedFiles(flush) {
            let result = [];
            Array.from(this.REDACT.deleted_files).forEach(file => {
                result.push(file);
            });

            if (flush) {
                this.REDACT.deleted_files = [];
            }

            return JSON.stringify(result);
        },
    }
}; 