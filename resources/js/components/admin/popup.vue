<template>
    <div
        v-for="(popup, popup_identifier) in this.popups.list"
        :key="popup_identifier"
        v-show="popup.active"
        :class="['popup', popup_identifier]"
    >
        <!-- Окно -->
        <div class="pp-wrapper">
            <!-- Фон окна -->

            <div
                class="pp-background"
                @click="this.toggle(popup_identifier, false)"
            ></div>
            <div
                class="pp-body"
                :style="{
                    width: popup.width ?? '540px',
                    height: popup.height ?? 'fit-content',
                }"
            >
                <div class="pp-content">
                    <!-- Верхняя панель -->
                    <div class="pp-topbar">
                        <div class="pp-title" v-if="popup.title">
                            {{ popup.title }}
                        </div>
                        <div
                            class="pp-close icon-wrapper"
                            @click="this.toggle(popup_identifier, false)"
                        >
                            <slot name="svg-button-close"></slot>
                        </div>
                    </div>

                    <!-- Форма с полями -->
                    <form
                        class="pp-form"
                        onkeydown="return event.key != 'Enter'"
                        v-if="popup.fields"
                    >
                        <slot name="csrf"></slot>

                        <div
                            v-for="(field, field_identifier) in popup.fields"
                            :key="field_identifier"
                            :elem-id="field_identifier"
                            class="fld-wrapper"
                            :data-hidden="
                                field.type == 'hidden' ? 'true' : 'false'
                            "
                        >
                            <!-- Поле ввода -->
                            <div v-if="field.type == 'field'" class="field">
                                <div class="fld-topline">
                                    <span class="fld-title">
                                        {{ field.data.title }}
                                        <span
                                            class="fld-required"
                                            v-if="field.data.required"
                                            >*
                                        </span>
                                    </span>

                                    <div class="fld-topline-right">
                                        <div class="fld-tooltip">
                                            <span>Ошибка</span>
                                            <div class="fld-tooltip-box">
                                                {{ field.data.errors }}
                                            </div>
                                        </div>

                                        <span class="fld-limit">
                                            {{ field.data.limit.value }}
                                        </span>
                                    </div>
                                </div>

                                <div class="fld-content">
                                    <textarea
                                        v-if="field.data.rows > 1"
                                        class="fld-area"
                                        :name="field_identifier"
                                        :placeholder="field.data.placeholder"
                                        :rows="field.data.rows"
                                        v-model="field.data.value"
                                        :limit="
                                            JSON.stringify(field.data.limit)
                                        "
                                        :context="
                                            JSON.stringify(field.data.context)
                                        "
                                        @input="
                                            this.fieldSetter(
                                                popup_identifier,
                                                field_identifier,
                                                $event
                                            )
                                        "
                                    ></textarea>
                                    <span
                                        v-if="
                                            field.data.rows == 1 &&
                                            field.data.prefix
                                        "
                                        class="fld-prefix"
                                    >
                                        {{ field.data.prefix }}
                                    </span>
                                    <input
                                        v-if="field.data.rows == 1"
                                        class="fld-area"
                                        :name="field_identifier"
                                        :placeholder="field.data.placeholder"
                                        v-model="field.data.value"
                                        :limit="
                                            JSON.stringify(field.data.limit)
                                        "
                                        :context="
                                            JSON.stringify(field.data.context)
                                        "
                                        @input="
                                            this.fieldSetter(
                                                popup_identifier,
                                                field_identifier,
                                                $event
                                            )
                                        "
                                    />
                                    <span
                                        v-if="
                                            field.data.rows == 1 &&
                                            field.data.suffix
                                        "
                                        class="fld-suffix"
                                    >
                                        {{ field.data.suffix }}
                                    </span>
                                </div>
                            </div>

                            <!-- Поле загрузки файла -->
                            <div
                                v-else-if="field.type == 'file'"
                                class="field file"
                            >
                                <div class="fld-topline">
                                    <span class="fld-title">
                                        {{ field.data.title }}
                                        <span
                                            v-if="field.data.required"
                                            class="fld-required"
                                            >*
                                        </span>
                                    </span>
                                    <div class="fld-topline-right">
                                        <div class="fld-tooltip">
                                            <span>Ошибка</span>
                                            <div class="fld-tooltip-box"></div>
                                        </div>
                                    </div>
                                    <span class="fld-limit">
                                        {{ field.data.limit.value }}
                                    </span>
                                </div>

                                <div
                                    :class="[
                                        'fld-file-content',
                                        {
                                            image: field.file_type == 'image',
                                            any: field.file_type == 'any',
                                        },
                                    ]"
                                >
                                    <img
                                        loading="lazy"
                                        class="fld-file-preview"
                                        :src="field.data.src"
                                        v-if="field.file_type == 'image'"
                                    />
                                    <span
                                        class="fld-file-name"
                                        v-if="field.file_type == 'any'"
                                    ></span>
                                    <div
                                        :class="[
                                            'fld-actions',
                                            {
                                                alone:
                                                    field.file_deletable ==
                                                    false,
                                            },
                                        ]"
                                    >
                                        <label class="fld-file-input">
                                            <slot
                                                name="svg-button-upload"
                                            ></slot>
                                            Загрузить файл
                                            <input
                                                type="file"
                                                accept=".png,.jpg,.jpeg"
                                                :name="field_identifier"
                                                @change="
                                                    this.fileSetter(
                                                        popup_identifier,
                                                        field_identifier,
                                                        $event
                                                    )
                                                "
                                                v-if="
                                                    field.file_type == 'image'
                                                "
                                            />
                                            <input
                                                type="file"
                                                accept="*"
                                                :name="field_identifier"
                                                @change="
                                                    this.fileSetter(
                                                        popup_identifier,
                                                        field_identifier,
                                                        $event
                                                    )
                                                "
                                                v-if="field.file_type == 'any'"
                                            />
                                        </label>
                                        <div
                                            class="fld-file-remove"
                                            @click="this.fileRemove($event)"
                                            v-if="
                                                field.file_deletable ==
                                                    undefined ||
                                                field.file_deletable == true ||
                                                field.file_deletable == null
                                            "
                                        >
                                            <slot
                                                name="svg-button-delete"
                                            ></slot>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Список для выбора -->
                            <div
                                v-else-if="
                                    field.type == 'select' &&
                                    Object.keys(field.data.list).length > 0
                                "
                                class="field select"
                            >
                                <div class="fld-topline">
                                    <span class="fld-title">
                                        {{ field.data.title }}
                                        <span
                                            v-if="field.data.required"
                                            class="fld-required"
                                            >*
                                        </span>
                                    </span>
                                    <div class="fld-topline-right">
                                        <div class="fld-tooltip">
                                            <span>Ошибка</span>
                                            <div class="fld-tooltip-box"></div>
                                        </div>
                                    </div>
                                </div>
                                <select
                                    class="fld-select"
                                    :name="field_identifier"
                                >
                                    <option value="0" selected="selected">
                                        Без категории
                                    </option>
                                    <option
                                        v-for="list_option in field.data.list"
                                        :key="list_option.id"
                                        class="fld-select-option"
                                        :value="list_option.id"
                                    >
                                        {{ list_option.content_title }}
                                    </option>
                                </select>
                            </div>

                            <!-- Скрытое поле -->
                            <div
                                v-else-if="
                                    field.type == 'hidden' &&
                                    Object.keys(field.data) != undefined
                                "
                                class="field hidden"
                            >
                                <input
                                    type="hidden"
                                    :name="field_identifier"
                                    :value="field.data.value"
                                />
                            </div>

                            <!-- Nestable загрузчик -->
                            <div
                                v-else-if="
                                    field.type == 'draggable-image' &&
                                    Object.keys(field.data) != undefined
                                "
                                class="field draggable-image"
                            >
                                <div class="fld-topline">
                                    <span class="fld-title">
                                        {{ field.data.title }}
                                        <span
                                            v-if="field.data.required"
                                            class="fld-required"
                                            >*
                                        </span>
                                    </span>
                                    <div class="fld-topline-right">
                                        <div class="fld-tooltip">
                                            <span>Ошибка</span>
                                            <div class="fld-tooltip-box"></div>
                                        </div>
                                        <span class="fld-limit">
                                            {{ field.data.limit.value }}
                                        </span>
                                    </div>
                                </div>
                                <draggable
                                    class="fld-draggable-list"
                                    :list="field.data.list"
                                    :forceFallback="true"
                                    animation="400"
                                    scrollSpeed="0"
                                    handle=".fld-draggable-image-preview"
                                >
                                    <div
                                        v-for="(item, item_index) in field.data
                                            .list"
                                        :key="item_index"
                                        :class="[
                                            'fld-draggable-item',
                                            { empty: item.preview == null },
                                        ]"
                                    >
                                        <div
                                            class="fld-draggable-image-wrapper"
                                        >
                                            <img
                                                class="
                                                    fld-draggable-image-preview
                                                "
                                                :src="item.preview"
                                            />
                                            <div
                                                class="fld-draggable-item-index"
                                            >
                                                {{ item_index + 1 }}
                                            </div>
                                            <div
                                                class="
                                                    fld-draggable-item-delete
                                                "
                                                v-on:click="
                                                    this.draggableDelete(
                                                        popup_identifier,
                                                        field_identifier,
                                                        item_index
                                                    )
                                                "
                                            >
                                                <slot
                                                    name="svg-button-delete"
                                                ></slot>
                                            </div>
                                            <label
                                                class="
                                                    fld-draggable-image-upload
                                                "
                                            >
                                                <slot
                                                    name="svg-button-upload"
                                                ></slot>
                                                <span
                                                    >Нажмите, для загрузки
                                                    файла</span
                                                >
                                                <input
                                                    type="file"
                                                    accept=".png,.jpg,.jpeg"
                                                    v-bind:name="
                                                        item.preview != null
                                                            ? 'cgp_image_' +
                                                              item_index
                                                            : ''
                                                    "
                                                    @change="
                                                        this.draggableSetter(
                                                            popup_identifier,
                                                            field_identifier,
                                                            item_index,
                                                            $event
                                                        )
                                                    "
                                                />
                                            </label>
                                        </div>
                                    </div>
                                </draggable>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Кнопки действия -->
                <div class="pp-actions" v-if="popup.actions">
                    <div
                        v-for="action in popup.actions"
                        :key="action.action"
                        :class="['pp-action', action.class]"
                        @click="this.click(action.action, $event)"
                    >
                        {{ action.text }}
                        <slot
                            v-if="action.sendable"
                            name="svg-button-loading"
                        ></slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { VueDraggableNext } from "vue-draggable-next";
// Общие примеси
import Mixin_AsyncScripts from "./async_scripts";
import Mixin_Request from "./popup/mixins/request";
import Mixin_File from "./popup/mixins/file";
import Mixin_Field from "./popup/mixins/field";
import Mixin_Draggable from "./popup/mixins/draggable";
import Mixin_Handler from "./popup/mixins/handler";
// Примеси отдельных видов попапов
window.slugify = require("slugify");
window.string_formatter = require("js-string-formatter");

export default {
    props: ["setup"],
    components: {
        draggable: VueDraggableNext,
    },
    mixins: [
        Mixin_AsyncScripts,
        Mixin_Request,
        Mixin_File,
        Mixin_Field,
        Mixin_Draggable,
        Mixin_Handler,
    ],

    data: function () {
        return {
            popups: {
                settings: {},
                list: {},
            },
        };
    },

    beforeMount() {
        // Получаем входные параметры
        let setup = JSON.parse(this.setup);
        // Инициализируем настройки
        this.popups.settings = setup.settings;
        this.popups.list = setup.list;

        for (let [popup_identifier, popup_obj] of Object.entries(
            this.popups.list
        )) {
            this.loadJS("/js/async_scripts/" + popup_identifier + ".js", () => {
                popup_obj.handler = eval("$_popup_" + popup_identifier);
            });
        }

        for (let [popup_identifier, popup_obj] of Object.entries(
            this.popups.list
        )) {
            for (let [field_identifier, field] of Object.entries(
                popup_obj.fields
            )) {
                if (field.type == "draggable-image") {
                    field.data.list = [];
                    this.draggableUploader(popup_identifier, field_identifier);
                }
            }
        }

        // this.popups.list[popup_identifier].handler = this.cn_fileSetter;
    },

    methods: {
        // Обработка кнопок
        click: function (string) {
            eval(string);
        },
        // Переключить состояние попапа
        toggle: function (name, state = null, data_index) {
            if (this.popups.list[name] != undefined) {
                if (state != null) {
                    this.popups.list[name].active = state;
                } else {
                    this.popups.list[name].active =
                        !this.popups.list[popup_name].active;
                }
            }

            let active = false;

            for (let [key, popup] of Object.entries(this.popups.list)) {
                if (this.popups.list[name].active) {
                    active = true;
                }
            }

            console.log(this.popups.list[name]);

            if (active) {
                if (this.popups.list[name].editable) {
                    let result = this.popups.list[name].handler.toggleInit(
                        window.environment.paginated_data.data[data_index]
                    );
                    // Установка новых значений в зависимых полях
                    if (result.dependencies && result.dependencies != null) {
                        for (let [
                            dep_identifier,
                            dep_newdata,
                        ] of Object.entries(result.dependencies)) {
                            for (let [dep_key, dep_value] of Object.entries(
                                dep_newdata
                            )) {
                                this.popups.list[name].fields[
                                    dep_identifier
                                ].data[dep_key] = dep_value;
                            }
                        }
                    }
                }
                document.querySelector("html").classList.add("popup_active");
            } else {
                document.querySelector("html").classList.remove("popup_active");
            }
        },
        // Активное ли состояние попапа
        isActive: function (name) {
            return (
                this.popups.list[name] != undefined &&
                this.popups.list[name].active === true
            );
        },
    },
};
</script>
