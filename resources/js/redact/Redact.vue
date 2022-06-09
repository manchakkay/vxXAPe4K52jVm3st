<script>
// Импортируем сторонние библиотеки
import { VueDraggableNext } from "vue-draggable-next";
import { VTooltip, VClosePopper, Dropdown, Tooltip, Menu } from "floating-vue";
import "floating-vue/dist/style.css";
window.hash = require("object-hash");

// Импортируем модули
import REDACT_debug from "./mixins/debug";
import REDACT_structure from "./mixins/structure";
import REDACT_interface from "./mixins/interface";
import REDACT_request from "./mixins/request";
import REDACT_support from "./mixins/support";

export default {
    mixins: [
        REDACT_debug,
        REDACT_structure,
        REDACT_interface,
        REDACT_request,
        REDACT_support,
    ],
    components: {
        draggable: VueDraggableNext,
        VDropdown: Dropdown,
        VTooltip: Tooltip,
        VMenu: Menu,
    },
    directives: {
        tooltip: VTooltip,
        "close-popper": VClosePopper,
    },
    props: ["environment"],
    computed: {
        is_saving() {
            return this.REDACT.status.saving == true;
        },
        is_publishing() {
            return this.REDACT.status.publishing == true;
        },
    },
    data() {
        return {
            // Хэш данных
            lastHash: null,
            // Данные редактора
            REDACT: {
                config: {
                    visible: false,
                },
                status: {
                    header: false,
                    active: false,
                    saving: true,
                    publishing: false,
                    message: false,
                    init: {
                        csrf: false,
                        templates: false,
                        data: false,
                        listeners: false,
                    },
                    structure_updated: false,
                    loading_progress: "0%",
                },
                vars: {},
                deleted_files: [],
                structure: {},
                templates: {},
                blocks: {},
                tools_left_1: [
                    {
                        drag: false,
                        name: "Назад",
                        icon: "back",
                        action: this.leaveRedact,
                    },
                ],
                tools_left_2: [
                    {
                        drag: true,
                        type: "TITLE01",
                        name: "Заголовок",
                        icon: "title",
                        add_name: "блок с заголовком",
                    },
                    {
                        drag: true,
                        type: "TEXT01",
                        name: "Текст",
                        icon: "text",
                        add_name: "блок текста",
                    },
                    {
                        drag: true,
                        type: "QUOTE01",
                        name: "Цитата",
                        icon: "quote",
                        add_name: "цитату",
                    },
                    {
                        drag: true,
                        type: "NOTE01",
                        name: "Заметка",
                        icon: "note",
                        add_name: "блок с заметкой",
                    },
                    {
                        drag: true,
                        type: "RULE01",
                        name: "Разделитель",
                        icon: "rule",
                        add_name: "разделитель",
                    },
                    {
                        drag: true,
                        type: "IMAGE01",
                        name: "Изображение",
                        icon: "image",
                        add_name: "изображение",
                    },
                    {
                        drag: true,
                        type: "TABLE01",
                        name: "Таблица",
                        icon: "table",
                        add_name: "таблицу",
                    },
                    {
                        drag: true,
                        type: "PERSON01",
                        name: "Сотрудник",
                        icon: "person",
                        add_name: "карточку сотрудника",
                    },
                    {
                        drag: true,
                        type: "HTML01",
                        name: "HTML-код",
                        icon: "code",
                        add_name: "виджет с кодом",
                    },
                    {
                        type: "VIDEO01",
                        icon: "video",
                        drag: false,
                    },
                    {
                        type: "FILE01",
                        icon: "file",
                        drag: false,
                    },
                    {
                        type: "LINK01",
                        icon: "link",
                        drag: false,
                    },
                ],
                tools_right_1: [
                    {
                        drag: false,
                        name: "Сохранить",
                        icon: "save",
                        disabled_if: this.is_saving,
                        action: this.saveData,
                    },
                ],
                tools_right_2: [
                    {
                        drag: false,
                        name: "Опубликовать",
                        icon: "preview",
                        disabled_if: this.is_publishing,
                        action: this.publishPage,
                    },
                ],
            },
        };
    },
    mounted() {
        this.initialize(this.environment.type);
        window.getDataHash = () => {
            return hash(this.REDACT);
        };
        window.lastDataHash = () => {
            return this.lastHash;
        };
    },
    methods: {
        leaveRedact() {
            // history.back();
            window.location=document.referrer;
        },
        publishPage() {
            if (this.REDACT.status.publishing) {
                return;
            } else {
                this.REDACT.status.publishing = true;
            }

            // Инициализируем запрос
            let headers = {
                "Content-Type": "multipart/form-data",
                "X-CSRF-TOKEN": this.csrf,
            };
            let request_data = {
                action: "publish",
                page_type: this.environment.type,
            };

            let request = this.$_request_create("PATCH", headers, request_data);

            // Отправка запроса
            this.$_request_send(request, "publish");
        },
        // Инициализация редактора
        initialize(page_type = "all") {
            this.$_debug_log("info", "initialization", "started");
            // Загружаем данные
            this.$_initializeData(page_type);
        },
        // Инициализация данных редактора
        $_initializeData(page_type) {
            if (!this.REDACT.status.init.csrf) {
                this.$_debug_log("warn", "initialization: data", "error");
                return;
            }

            // Инициализируем запрос
            let headers = { "X-CSRF-TOKEN": this.csrf };
            let request = this.$_request_create("GET", headers, {
                action: "loadData",
                page_type: page_type,
            });

            // Отправка запроса
            this.$_request_send(request, "loadData");
        },
        // Инициализация шаблонов блоков редактора
        $_initializeTemplates(page_type) {
            if (!this.REDACT.status.init.data) {
                this.$_debug_log("warn", "initialization: templates", "error");
                return;
            }

            // Инициализируем запрос
            let headers = { "X-CSRF-TOKEN": this.csrf };
            let request = this.$_request_create("GET", headers, {
                action: "loadBlocks",
                page_type: page_type,
            });

            // Отправка запроса
            this.$_request_send(request, "loadBlocks");
        },
        // Активация редактора
        $_activate() {
            // Активация флагов редактора
            this.$_debug_log("info", "initialization", "success");

            // Скрытие или показ шапки в зависимости от контента страницы
            this.REDACT.status.header = this.REDACT.structure.blocks.length > 0;

            // Сохранение в сессии, что страница посещена
            sessionStorage.setItem("page_visited", true);
            let loading_timeout = 800;

            // Проверка - перезагружена ли страница
            if (
                sessionStorage.getItem("is_reloaded") ||
                (window.performance.navigation &&
                    window.performance.navigation.type === 1) ||
                window.performance
                    .getEntriesByType("navigation")
                    .map((nav) => nav.type)
                    .includes("reload")
            ) {
                loading_timeout = 200;
            }

            setTimeout(() => {
                this.REDACT.status.active = true;
                this.REDACT.status.saving = false;
            }, loading_timeout);
        },

        // Сохранение всех данных редактора
        saveData() {
            if (this.REDACT.status.saving) {
                return;
            } else {
                this.REDACT.status.saving = true;
            }

            let data = this.$_structure_export(
                this.environment.type,
                "saveData"
            );

            // Инициализируем запрос
            let headers = {
                "Content-Type": "multipart/form-data",
                "X-CSRF-TOKEN": this.csrf,
            };
            let request_data = {
                action: "saveData",
                page_type: this.environment.type,
                data: data.json,
                files_meta: JSON.stringify(data.files.files_meta),
                deleted_files: this.$_structure_getDeletedFiles(true),
            };

            // Добавляем файлы
            this.$_debug_log(
                "debug",
                "files_array",
                JSON.stringify(data.files.files_meta)
            );
            for (let [key, value] of Object.entries(data.files.files_array)) {
                request_data["$FILE$_" + key] = value;
            }

            let request = this.$_request_create("POST", headers, request_data);

            // Отправка запроса
            this.$_request_send(request, "saveData");
        },
        startDrag({ originalEvent }) {
            if (originalEvent.altKey) {
                let block_id = originalEvent.path[0]
                    .closest(".block-wrapper")
                    .getAttribute("block-id");
                let block_index = this.$_structure_getBlockIndexByID(block_id);
                let new_block = JSON.parse(
                    JSON.stringify(this.REDACT.structure.blocks[block_index])
                );

                new_block.block_id = this.$_interface_nextBlockID;

                this.REDACT.structure.blocks.splice(block_index, 0, new_block);

                this.REDACT.status.structure_updated = true;
                this.$forceUpdate();
            }

            this.toggleDragging(true);
        },
        endDrag() {
            this.toggleDragging(false);
        },
        placeTool(e) {
            console.log(e);
            console.log("Tool: place");
            if (e.clone.classList.contains("drag")) {
                let obj_key = "tools_left_1";

                if (e.from.classList.contains("left-1")) {
                    obj_key = "tools_left_1";
                } else if (e.from.classList.contains("left-2")) {
                    obj_key = "tools_left_2";
                } else if (e.from.classList.contains("right-1")) {
                    obj_key = "tools_right_1";
                } else if (e.from.classList.contains("right-2")) {
                    obj_key = "tools_right_2";
                }

                if (e.to.classList.contains("redact-blocks")) {
                    let block_type = this.REDACT[obj_key][e.oldIndex].type;
                    let block_pos = e.newIndex;
                    this.$_interface_appendBlock(block_type, block_pos);
                }
            }
            this.toggleDragging(false);
            this.toggleDragging(false, "dragging-tool");
        },
        activateTool() {
            console.log("Tool: activate");
            this.toggleDragging(true);
            this.toggleDragging(true, "dragging-tool");
        },
        cloneTool() {
            console.log("Tool: clone");
        },
        checkTool(e) {
            console.log(e);
            if (
                e.from == e.to &&
                e.draggedContext.index != e.draggedContext.futureIndex
            ) {
                return false;
            } else {
                return true;
            }
        },
        toggleDragging(state = "inverse", cls = "dragging") {
            if (state == true) {
                document.querySelector("html").classList.add(cls);
            } else if (state == false) {
                document.querySelector("html").classList.remove(cls);
            } else {
                if (document.querySelector("html").classList.contains(cls)) {
                    document.querySelector("html").classList.remove(cls);
                } else {
                    document.querySelector("html").classList.add(cls);
                }
            }
        },
    },
};
</script>



<template>
    <div
        class="redact-wrapper"
        :class="[{ loading: !this.REDACT.status.active }]"
    >
        <div
            class="redact-loading"
            :class="[{ active: !this.REDACT.status.active }]"
        >
            <div class="loading-back"></div>
            <span class="loading-text"> ФБКИ ИГУ</span>
            <p class="loading-desc">Редактор страниц</p>
        </div>
        <div class="redact-interaction-area" v-if="this.REDACT.status.active">
            <div
                :class="[
                    'pending-bar',
                    {
                        saving: this.REDACT.status.saving,
                        publishing: this.REDACT.status.publishing,
                    },
                ]"
            ></div>
            <div
                :class="[
                    'popup-message',
                    {
                        active: this.REDACT.status.message,
                    },
                ]"
            >
                {{
                    this.REDACT.vars.message != undefined &&
                    this.REDACT.vars.message != ""
                        ? this.REDACT.vars.message
                        : ""
                }}
            </div>

            <!-- Left 1 -->
            <draggable
                class="redact-tools left-1"
                :list="this.REDACT.tools_left_1"
                :forceFallback="true"
                animation="250"
                scrollSpeed="0"
                :group="{ name: 'blocks', pull: 'clone', put: false }"
                :clone="this.cloneTool"
                :move="this.checkTool"
                handle=".drag"
                @start="this.activateTool"
                @end="this.placeTool"
            >
                <div
                    v-for="(tool, tool_index) in this.REDACT.tools_left_1"
                    :key="tool_index"
                    :class="['tool-wrapper', { drag: tool.drag }]"
                    @click="tool.action"
                    v-tooltip.right="{
                        content: tool.name,
                        distance: 12,
                        menu: {
                            delay: {
                                show: 0,
                                hide: 400,
                            },
                        },
                    }"
                >
                    <img
                        loading="eager"
                        class="icon tool-icon-idle"
                        :src="
                            '/assets/icons/admin/redact/redact-icon-' +
                            tool.icon +
                            '-idle.svg'
                        "
                    />
                    <img
                        loading="lazy"
                        class="icon tool-icon-active"
                        :src="
                            '/assets/icons/admin/redact/redact-icon-' +
                            tool.icon +
                            '-active.svg'
                        "
                    />
                </div>
            </draggable>
            <!-- Left 2 -->
            <draggable
                class="redact-tools left-2"
                :list="this.REDACT.tools_left_2"
                :forceFallback="true"
                animation="250"
                scrollSpeed="0"
                :group="{ name: 'blocks', pull: 'clone', put: false }"
                :clone="this.cloneTool"
                :move="this.checkTool"
                handle=".drag"
                @start="this.activateTool"
                @end="this.placeTool"
            >
                <div
                    v-for="(tool, tool_index) in this.REDACT.tools_left_2"
                    v-show="
                        this.REDACT.blocks.templates_set.includes(tool.type)
                    "
                    :key="tool_index"
                    :class="['tool-wrapper', { drag: tool.drag }]"
                    @click="tool.action"
                    v-tooltip.right="{
                        content: tool.name,
                        distance: 12,
                        menu: {
                            delay: {
                                show: 0,
                                hide: 400,
                            },
                        },
                    }"
                >
                    <div class="tool-text">
                        <b>Добавить {{ tool.add_name }}</b>
                        <div class="sub">
                            для отмены перетащите выбранный блок в корзину снизу
                        </div>
                    </div>
                    <img
                        loading="eager"
                        class="icon tool-icon-idle"
                        :src="
                            '/assets/icons/admin/redact/redact-icon-' +
                            tool.icon +
                            '-idle.svg'
                        "
                    />
                    <img
                        loading="lazy"
                        class="icon tool-icon-active"
                        :src="
                            '/assets/icons/admin/redact/redact-icon-' +
                            tool.icon +
                            '-active.svg'
                        "
                    />
                </div>
            </draggable>
            <!-- Trash -->
            <draggable
                class="redact-tools trash"
                :list="this.REDACT.trash"
                :forceFallback="true"
                animation="250"
                scrollSpeed="0"
                :group="{ name: 'trash', pull: false, put: () => true }"
                handle=".trash"
            >
                <div class="trash-wrapper">
                    <img
                        loading="lazy"
                        class="icon trash-icon"
                        src="/assets/icons/admin/button_delete.svg"
                    />
                </div>
            </draggable>
            <!-- Content -->
            <div class="redact-content">
                <details
                    v-if="this.environment.type == 'news'"
                    :open="this.REDACT.status.header ? true : false"
                >
                    <summary
                        class="button"
                        @click="
                            (event) => {
                                event.preventDefault();
                                this.REDACT.status.header =
                                    !this.REDACT.status.header;
                            }
                        "
                    >
                        <span v-if="this.REDACT.status.header"
                            >Скрыть шапку</span
                        >
                        <span v-else>Показать шапку</span>
                    </summary>
                    <div class="redact-meta">
                        <slot name="category"></slot>
                        <slot name="date"></slot>
                    </div>
                    <slot name="title"></slot>
                    <slot name="description"></slot>
                    <slot name="thumbnail"></slot>
                </details>
                <div class="redact-root">
                    <!-- Hint -->
                    <div
                        v-if="this.REDACT.structure.blocks.length == 0"
                        class="no-blocks"
                    >
                        <div class="art">
                            <svg
                                width="181"
                                height="145"
                                viewBox="0 0 181 145"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <rect
                                    opacity="0.5"
                                    x="1.5"
                                    y="28.5"
                                    width="115"
                                    height="115"
                                    rx="14.5"
                                    stroke="#969CA2"
                                    stroke-width="3"
                                    stroke-dasharray="6 6"
                                />
                            </svg>

                            <div id="block-anim"></div>

                            <svg
                                width="181"
                                height="145"
                                viewBox="0 0 181 145"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M137 93.6619V136.68C137 142.971 145.19 145.399 148.619 140.125L155.267 129.897C156.646 127.776 159.157 126.681 161.649 127.114L173.19 129.122C179.361 130.195 183.166 122.601 178.612 118.3L147.659 89.0672C143.629 85.2606 137 88.118 137 93.6619Z"
                                    fill="#717982"
                                />
                            </svg>
                        </div>
                        <span>
                            Перетащите в поле блоки из панели слева, чтобы
                            добавить содержимое
                        </span>
                    </div>
                    <!-- Blocks -->
                    <draggable
                        class="redact-blocks"
                        handle=".redact-drag-handle"
                        :list="this.REDACT.structure.blocks"
                        :scrollSensitivity="100"
                        :forceFallback="true"
                        group="blocks"
                        animation="250"
                        scrollSpeed="40"
                        @start="this.startDrag"
                        @end="this.endDrag"
                        @change="this.$_structure_updatePos"
                    >
                        <div
                            class="block-wrapper"
                            v-for="(block, block_index) in this.REDACT.structure
                                .blocks"
                            :key="block.block_id"
                            :block-id="block.block_id"
                            :block-type="block.block_type"
                        >
                            <!-- <div class="block-name">{{ block.block_type }}</div> -->
                            <div class="block-controls">
                                <div class="block-control redact-drag-handle">
                                    <img
                                        loading="lazy"
                                        class="icon icon-24"
                                        src="/assets/icons/admin/button_handle.svg"
                                    />
                                </div>
                                <div
                                    class="block-control block-delete"
                                    @click="
                                        this.$_interface_removeBlock(
                                            block.block_id
                                        )
                                    "
                                >
                                    <img
                                        loading="lazy"
                                        class="icon icon-24"
                                        src="/assets/icons/admin/button_delete.svg"
                                    />
                                </div>
                            </div>
                            <div
                                class="block-container"
                                v-html="
                                    REDACT.blocks.templates[block.block_type]
                                "
                            ></div>
                        </div>
                    </draggable>
                </div>
            </div>
            <!-- Right 1 -->
            <draggable
                class="redact-tools right-1"
                :list="this.REDACT.tools_right_1"
                :forceFallback="true"
                animation="250"
                scrollSpeed="0"
                :group="{ name: 'blocks', pull: 'clone', put: false }"
                :clone="this.cloneTool"
                :move="this.checkTool"
                handle=".drag"
                @start="this.activateTool"
                @end="this.placeTool"
            >
                <div
                    v-for="(tool, tool_index) in this.REDACT.tools_right_1"
                    :key="tool_index"
                    :class="[
                        'tool-wrapper',
                        { drag: tool.drag, disabled: tool.disabled_if == true },
                    ]"
                    v-tooltip.left="{
                        content: tool.name,
                        distance: 12,
                        menu: {
                            delay: {
                                show: 0,
                                hide: 400,
                            },
                        },
                    }"
                    @click="tool.action"
                >
                    <img
                        loading="eager"
                        class="icon tool-icon-idle"
                        :src="
                            '/assets/icons/admin/redact/redact-icon-' +
                            tool.icon +
                            '-idle.svg'
                        "
                    />
                    <img
                        loading="lazy"
                        class="icon tool-icon-active"
                        :src="
                            '/assets/icons/admin/redact/redact-icon-' +
                            tool.icon +
                            '-active.svg'
                        "
                    />
                </div>
            </draggable>
            <!-- Right 2 -->
            <draggable
                class="redact-tools right-2"
                :list="this.REDACT.tools_right_2"
                :forceFallback="true"
                animation="250"
                scrollSpeed="0"
                :group="{ name: 'blocks', pull: 'clone', put: false }"
                :clone="this.cloneTool"
                :move="this.checkTool"
                handle=".drag"
                @start="this.activateTool"
                @end="this.placeTool"
            >
                <div
                    v-for="(tool, tool_index) in this.REDACT.tools_right_2"
                    :key="tool_index"
                    :class="[
                        'tool-wrapper',
                        { drag: tool.drag, disabled: tool.disabled_if == true },
                    ]"
                    v-tooltip.left="{
                        content: tool.name,
                        distance: 12,
                        menu: {
                            delay: {
                                show: 0,
                                hide: 400,
                            },
                        },
                    }"
                    @click="tool.action"
                >
                    <img
                        loading="eager"
                        class="icon tool-icon-idle"
                        :src="
                            '/assets/icons/admin/redact/redact-icon-' +
                            tool.icon +
                            '-idle.svg'
                        "
                    />
                    <img
                        loading="lazy"
                        class="icon tool-icon-active"
                        :src="
                            '/assets/icons/admin/redact/redact-icon-' +
                            tool.icon +
                            '-active.svg'
                        "
                    />
                </div>
            </draggable>
        </div>
    </div>
</template>


