// Подмодуль для отрисовки интерфейса

export default {
    methods: {
        // Создать инстанцию блока определённого типа
        $_interface_createBlock(type) {
            let block = this.$_support_copyObject(this.REDACT.templates.block);
            block.block_id = this.$_interface_nextBlockID;

            // Добавляем тип блока
            if (this.REDACT.blocks.templates_set.includes(type.toUpperCase())) {
                block.block_type = type;
            } else {
                return { error: true };
            }

            return block;
        },
        // Импорт данных в блок
        $_interface_fillBlock(block, block_elem, data = {}) {
            switch (block.block_type) {
                case "TITLE01":
                    if (block.data.text != undefined) {
                        // Устанавливаем данные в объект
                        data.title.innerHTML = block.data.text;
                    } else {
                        // Если данных нет - добавляем шаблон в блок
                        block.data.text = "";
                    }
                    break;
                case "TEXT01":
                    if (block.data.delta != undefined) {
                        // Устанавливаем данные в объект
                        data.quill_obj.setContents(block.data.delta);
                    } else {
                        // Если данных нет - добавляем шаблон в блок
                        block.data = {
                            delta: { "ops": [{ "insert": "\n" }] },
                            html: "<p><br></p>"
                        };
                        data.quill_obj.setContents(block.data.delta);
                    }
                    break;
                case "TABLE01":
                    // Устанавливаем данные в объект
                    if (block.data.array != undefined) {
                        // Устанавливаем данные в объект
                        let tbody_elem = document.createElement('tbody');

                        block.data.array.forEach(row => {
                            let row_elem = document.createElement('tr');
                            row.forEach(cell => {
                                let cell_elem = document.createElement('td');
                                cell_elem.insertAdjacentHTML("beforeend", cell);
                                cell_elem.setAttribute('contenteditable', true);
                                row_elem.appendChild(cell_elem);
                            });

                            tbody_elem.appendChild(row_elem);
                        });

                        data.table.appendChild(tbody_elem);
                    } else {
                        // Если данных нет - добавляем шаблон в блок
                        block.data = {
                            array: []
                        };
                        data.table.innerHTML = "";
                    }
                    break;
                case "QUOTE01":
                    if (block.data.body != undefined && block.data.body.delta != undefined) {
                        // Устанавливаем данные в объект
                        data.quill_obj.setContents(block.data.body.delta);
                    } else {
                        // Если данных нет - добавляем шаблон в блок
                        block.data.body = {
                            delta: { "ops": [{ "insert": "\n" }] },
                            html: "<p><br></p>",
                        };
                        data.quill_obj.setContents(block.data.body.delta);
                    }

                    if (block.data.author == undefined) {
                        block.data.author = {
                            name: null,
                            position: null,
                            src: null,
                            image_id: null,
                        };
                    } else {
                        if (block.data.author.src != undefined) {
                            // Устанавливаем данные в объект
                            data.preview.src = this.$_support_parseFileURL(block.data.author.src);
                            block_elem.querySelector(".redact-image-wrapper").classList.remove("no-image");
                        } else {
                            // Если данных нет - добавляем шаблон в блок
                            block.data.author.src = null;
                            block_elem.querySelector(".redact-image-wrapper").classList.add("no-image");
                        }

                        // Устанавливаем данные в объект
                        if (block.data.author.name != undefined) {
                            data.name.innerText = block.data.author.name;
                        } else {
                            block.data.author.name = "";
                        }
                        if (block.data.author.position != undefined) {
                            data.position.innerText = block.data.author.position;
                        } else {
                            block.data.author.position = "";
                        }
                    }


                    break;
                case "PERSON01":
                    if (block.data.bio != undefined && block.data.bio.delta != undefined) {
                        // Устанавливаем данные в объект
                        data.quill_obj.setContents(block.data.bio.delta);
                    } else {
                        // Если данных нет - добавляем шаблон в блок
                        block.data.bio = {
                            delta: { "ops": [{ "insert": "\n" }] },
                            html: "<p><br></p>",
                        };
                        data.quill_obj.setContents(block.data.bio.delta);
                    }

                    if (block.data.name == undefined) {
                        block.data.name = null;
                    } else {
                        if (block.data.src != undefined) {
                            // Устанавливаем данные в объект
                            data.preview.src = this.$_support_parseFileURL(block.data.src);
                            block_elem.querySelector(".redact-image-wrapper").classList.remove("no-image");
                        } else {
                            // Если данных нет - добавляем шаблон в блок
                            block.data.src = null;
                            block.data.image_id = null;
                            block_elem.querySelector(".redact-image-wrapper").classList.add("no-image");
                        }

                        // Устанавливаем данные в объект
                        if (block.data.name != undefined) {
                            data.name.innerText = block.data.name;
                        } else {
                            block.data.name = "";
                        }
                    }


                    break;
                case "IMAGE01":
                    if (block.data.src != undefined) {
                        // Устанавливаем данные в объект
                        data.preview.src = this.$_support_parseFileURL(block.data.src);
                        block_elem.querySelector(".redact-image-wrapper").classList.remove("no-image");
                    } else {
                        // Если данных нет - добавляем шаблон в блок
                        block.data.src = null;
                        block.data.image_id = null;
                        block_elem.querySelector(".redact-image-wrapper").classList.add("no-image");
                    }
                    break;
            }

        },
        $_interface_updateFiles(block) {
            let block_elem = document.querySelector('.redact-wrapper .block-wrapper[block-id="' + block.block_id + '"]');
            switch (block.block_type) {
                case "IMAGE01":
                    block_elem.querySelector('.image .redact-image-preview').src = this.$_support_parseFileURL(block.data.src);
                    break;
                case "QUOTE01":
                    block_elem.querySelector('.quote-author-photo .redact-image-preview').src = this.$_support_parseFileURL(block.data.author.src);
                    break;
                case "PERSON01":
                    block_elem.querySelector('.person-photo .redact-image-preview').src = this.$_support_parseFileURL(block.data.src);
                    break;
            }
        },
    },

    computed: {
        // Получение позиции последнего блока
        $_interface_nextBlockPos() {
            let last_pos = 0;

            if (this.REDACT.structure.blocks.length > 0) {
                last_pos = Math.max.apply(Math, this.REDACT.structure.blocks.map(function (o) { return o.block_position; }));
                last_pos++;
            }

            return last_pos;
        },
        // Получение позиции последнего блока
        $_interface_nextBlockID() {
            let last_id = 0;

            if (this.REDACT.structure.blocks.length > 0) {
                last_id = Math.max.apply(Math, this.REDACT.structure.blocks.map(function (o) { return o.block_id; }));
                last_id++;
            }

            return last_id;
        },
        // Получения упорядоченных блоков
        $_interface_getBlocks() {
            if (this.REDACT.status.init.templates && this.REDACT.status.init.data) {
                let blocksSorted = [];
                for (var block in this.REDACT.structure.blocks) {
                    blocksSorted.push(
                        this.REDACT.structure.blocks[block]
                    );
                }

                blocksSorted.sort(function (a, b) {
                    return a.block_position - b.block_position;
                });
                return blocksSorted;
            } else {
                return [];
            }
        },
    }
};