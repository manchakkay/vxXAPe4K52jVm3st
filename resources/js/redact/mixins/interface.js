// Корневая примесь подсистемы интерфейса и взаимодействия
import { REDACT_Quill } from "./../../quill";
// Импортируем подмодули
import REDACT_interface$markup from "./interface/markup";
import REDACT_interface$handler from "./interface/handler";
import REDACT_interface$listener from "./interface/listener";

export default {
    mixins: [REDACT_interface$markup, REDACT_interface$handler, REDACT_interface$listener],
    mounted() {

    },
    methods: {
        // Добавить блок
        $_interface_appendBlock(type, pos = null) {
            this.REDACT.status.structure_updated = true;
            this.$_debug_log(
                "debug",
                "addBlock",
                "Adding new block with type - " + type
            );

            // Создаём новый блок
            let block = this.$_interface_createBlock(type);
            if (pos == null) {
                block.block_position = this.$_interface_nextBlockPos;
            } else {
                block.block_position = pos;
            }

            if (block.error == undefined) {
                // this.REDACT.structure.blocks.push(block);
                this.REDACT.structure.blocks.splice(pos, 0, block);
                this.$forceUpdate();
            }
        },
        // Удалить блок
        $_interface_removeBlock(block_id) {
            this.REDACT.status.structure_updated = true;
            this.$_debug_log(
                "debug",
                "removeBlock",
                "Removing block with id - " + block_id
            );

            // Получаем ссылку на объект блока
            let block_index = this.$_structure_getBlockIndexByID(block_id);
            let block = this.REDACT.structure.blocks[block_index];
            switch (block.block_type) {
                case "IMAGE01":
                    this.$_structure_deleteFromServer(block.data.image_id);
                    break;
                case "QUOTE01":
                    this.$_structure_deleteFromServer(block.data.author.image_id);
                    break;
                case "PERSON01":
                    this.$_structure_deleteFromServer(block.data.image_id);
                    break;
            }

            // Удаляем блок из массива
            this.REDACT.structure.blocks.splice(block_index, 1);
        },
        // Обновление всех блоков
        $_interface_updateBlocks() {
            this.$_debug_log("debug", "updateListeners", "starting checks");
            if (this.REDACT.status.init.listeners || this.REDACT.status.structure_updated) {
                this.REDACT.status.structure_updated = false;

                this.REDACT.structure.blocks.forEach((block, index) => {
                    let block_elem = document.querySelector(".block-wrapper[block-id=\"" + block.block_id + "\"]");
                    // Проверяем на обновлённость блока
                    if (!block_elem.hasAttribute("block-updated")) {
                        block_elem.setAttribute("block-updated", "");
                        switch (block.block_type) {
                            case "TITLE01":
                                let TITLE01_elem = block_elem.querySelector(".title");
                                // Подключаем слушатели к однострочным полям ввода
                                this.$_interface_listenersBlock("input", "span", block.block_type, block_elem);
                                // Импортируем данные в блок
                                this.$_interface_fillBlock(block, block_elem, {
                                    "title": TITLE01_elem,
                                });
                                break;
                            case "TABLE01":
                                let TABLE01_table = block_elem.querySelector("table");
                                // Подключаем слушатели к таблице и кнопкам управления
                                this.$_interface_listenersBlock("input", "table", block.block_type, block_elem);
                                this.$_interface_listenersBlock("click", "multiple-button", block.block_type, block_elem);
                                // Импортируем данные в блок
                                this.$_interface_fillBlock(block, block_elem, {
                                    "table": TABLE01_table,
                                });
                                break;
                            case "TEXT01":
                                // Подключаем quill к блоку
                                let TEXT01_quill_elem = block_elem.querySelector(".redact-quill");
                                let TEXT01_quill_obj = REDACT_Quill.create(TEXT01_quill_elem);
                                let TEXT01_quill_input = document.querySelector(
                                    "input[data-link]"
                                );
                                TEXT01_quill_input.dataset.link = "Ссылка на страницу";
                                TEXT01_quill_input.placeholder = "Ссылка на страницу";

                                // Подключаем слушатели к quill
                                this.$_interface_listenersBlock("text-change", "quill", block.block_type, block_elem, { "quill_elem": TEXT01_quill_elem, "quill_obj": TEXT01_quill_obj });
                                this.$_interface_listenersBlock("selection-change", "quill", block.block_type, block_elem, { "quill_elem": TEXT01_quill_elem, "quill_obj": TEXT01_quill_obj });

                                // Импортируем данные в блок
                                this.$_interface_fillBlock(block, block_elem, { "quill_obj": TEXT01_quill_obj });

                                break;
                            case "QUOTE01":
                                // Подключаем quill к блоку
                                let QUOTE01_quill_elem = block_elem.querySelector(".redact-quill");
                                let QUOTE01_quill_obj = REDACT_Quill.create(QUOTE01_quill_elem);
                                let QUOTE01_quill_input = document.querySelector(
                                    "input[data-link]"
                                );
                                QUOTE01_quill_input.dataset.link = "Ссылка на страницу";
                                QUOTE01_quill_input.placeholder = "Ссылка на страницу";

                                // Находим элемент с картинкой
                                let QUOTE01_preview_elem = block_elem.querySelector(".redact-image-preview");

                                // Находим элементы с текстом
                                let QUOTE01_author_name = block_elem.querySelector(".quote-author-name");
                                let QUOTE01_author_position = block_elem.querySelector(".quote-author-position");

                                // Подключаем слушатели к quill
                                this.$_interface_listenersBlock("text-change", "quill", block.block_type, block_elem, { "quill_elem": QUOTE01_quill_elem, "quill_obj": QUOTE01_quill_obj });
                                this.$_interface_listenersBlock("selection-change", "quill", block.block_type, block_elem, { "quill_elem": QUOTE01_quill_elem, "quill_obj": QUOTE01_quill_obj });
                                // Подключаем слушатели к input для изображений
                                this.$_interface_listenersBlock("change", "image", block.block_type, block_elem, { "preview": QUOTE01_preview_elem });
                                // Подключаем слушатели к однострочным полям ввода
                                this.$_interface_listenersBlock("input", "multiple-input", block.block_type, block_elem);

                                // Импортируем данные в блок
                                this.$_interface_fillBlock(block, block_elem, {
                                    "quill_obj": QUOTE01_quill_obj,
                                    "preview": QUOTE01_preview_elem,
                                    "name": QUOTE01_author_name,
                                    "position": QUOTE01_author_position,
                                });

                                break;
                            case "PERSON01":
                                // Подключаем quill к блоку
                                let PERSON01_quill_elem = block_elem.querySelector(".redact-quill");
                                let PERSON01_quill_obj = REDACT_Quill.create(PERSON01_quill_elem, 'no-align');
                                let PERSON01_quill_input = document.querySelector(
                                    "input[data-link]"
                                );
                                PERSON01_quill_input.dataset.link = "Ссылка на страницу";
                                PERSON01_quill_input.placeholder = "Ссылка на страницу";

                                // Находим элемент с картинкой
                                let PERSON01_preview_elem = block_elem.querySelector(".redact-image-preview");

                                // Находим элементы с текстом
                                let PERSON01_author_name = block_elem.querySelector(".person-name");

                                // Подключаем слушатели к quill
                                this.$_interface_listenersBlock("text-change", "quill", block.block_type, block_elem, { "quill_elem": PERSON01_quill_elem, "quill_obj": PERSON01_quill_obj });
                                this.$_interface_listenersBlock("selection-change", "quill", block.block_type, block_elem, { "quill_elem": PERSON01_quill_elem, "quill_obj": PERSON01_quill_obj });
                                // Подключаем слушатели к input для изображений
                                this.$_interface_listenersBlock("change", "image", block.block_type, block_elem, { "preview": PERSON01_preview_elem });
                                // Подключаем слушатели к однострочным полям ввода
                                this.$_interface_listenersBlock("input", "multiple-input", block.block_type, block_elem);

                                // Импортируем данные в блок
                                this.$_interface_fillBlock(block, block_elem, {
                                    "quill_obj": PERSON01_quill_obj,
                                    "preview": PERSON01_preview_elem,
                                    "name": PERSON01_author_name,
                                });

                                break;
                            case "IMAGE01":
                                // Находим элемент с картинкой
                                let IMAGE01_preview_elem = block_elem.querySelector(".redact-image-preview");
                                // Подключаем слушатели к input для изображений
                                this.$_interface_listenersBlock("change", "image", block.block_type, block_elem, { "preview": IMAGE01_preview_elem });

                                // Импортируем данные в блок
                                this.$_interface_fillBlock(block, block_elem, { "preview": IMAGE01_preview_elem });

                                break;
                        }
                    }
                });
            }
        },
    },
};