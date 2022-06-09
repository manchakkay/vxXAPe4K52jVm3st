<template>
    <div>
        <div class="nestable-wrapper">
            <div class="list">
                <h2>Структура сайта</h2>
                <vue-nestable
                    :value="nestableItems"
                    group="cross"
                    @input="nestableItems = $event"
                    @change="this.handle_drop"
                >
                    <template v-slot="item">
                        <vue-nestable-handle>
                            <div class="title">
                                <button @click="this.toggle_expand($event)">
                                    -
                                </button>
                                {{ item.item.content_title }}
                            </div>
                            <!-- <div class="desc">
                            {{ item.item.id }}
                        </div> -->
                        </vue-nestable-handle>
                    </template>
                </vue-nestable>
            </div>
            <div class="list cache">
                <h2>Независимые страницы</h2>
                <vue-nestable
                    :value="nestableCache"
                    :maxDepth="1"
                    group="cross"
                    :threshold="2"
                    @input="nestableCache = $event"
                >
                    <template v-slot="item">
                        <vue-nestable-handle>
                            {{ item.item.content_title }}
                        </vue-nestable-handle>
                    </template>
                </vue-nestable>
            </div>
        </div>
        <div class="nestable-actions">
            <button
                @click="this.save_structure()"
                :class="{ disabled: this.is_pending_request }"
            >
                Сохранить
                <slot
                    v-if="this.is_pending_request"
                    name="svg-button-loading"
                ></slot>
            </button>
            <span class="error-message" v-if="this.is_error_response">
                Произошла ошибка, попробуйте ещё раз
            </span>
        </div>
    </div>
</template>

<script>
import { VueNestable, VueNestableHandle } from "vue3-nestable";
export default {
    components: {
        VueNestable,
        VueNestableHandle,
    },
    props: {
        pagesStructure: {
            type: String,
            default: [],
        },
        pagesCache: {
            type: String,
            default: [],
        },
    },
    methods: {
        toggle_expand: function (e) {
            let parent = e.target.closest(".nestable-item");
            let list = parent.querySelector(".nestable-list");
            let button = parent.querySelector(".nestable-item-content button");

            if (list && button) {
                if (list.classList.contains("col")) {
                    // Скрыт
                    list.classList.remove("col");
                    parent.classList.remove("parent-col");
                    button.textContent = "-";
                    button.classList.remove("button-col");
                } else {
                    // Не скрыт
                    list.classList.add("col");
                    parent.classList.add("parent-col");
                    button.textContent = "+";
                    button.classList.add("button-col");
                }
            }
        },
        handle_drop: function (v, o) {
            let index_items = this.nestableItems.findIndex((i) => {
                return this.recursive_search(i, v.id);
            });
            let index_cache = this.nestableCache.findIndex((i) => {
                return i.id === v.id;
            });

            if (o.pathTo != null && o.pathTo.length == 0) {
                // Перемещение в запасные

                // Если нет в кэше
                if (index_cache == -1) {
                    let all_items = this.recursive_expand(v, true);
                    this.nestableCache.push(...all_items);

                    // Если есть в обычных
                    if (index_items != -1) {
                        if (this.nestableItems[index_items].id === v.id) {
                            // Если уровень первый - вырезаем целиком
                            this.nestableItems.splice(index_items, 1);
                        } else {
                            // Иначе ищем часть и вырезаем её
                            this.nestableItems[index_items] =
                                this.recursive_splice(
                                    this.nestableItems[index_items],
                                    v.id
                                );
                        }
                    }
                }
            } else if (o.pathTo == null) {
                // Перемещение туда же где был
            } else {
                // Перемещение в активные

                // Если нет в обычных
                if (index_items == -1) {
                    // Если есть в кэше
                    if (index_cache != -1) {
                        this.nestableCache.splice(index_cache, 1);
                    }
                }
            }
        },
        recursive_splice: function (value, comp) {
            // Ищем детей
            if (
                value.children &&
                value.children != undefined &&
                value.children.length > 0
            ) {
                // Проходим по детям
                value.children.forEach((child, index) => {
                    if (child.id == comp) {
                        // Если нашли, вырезаем и возвращаемся
                        value.children.splice(index, 1);
                        return value;
                    } else {
                        // Если не нашли, ищем глубже
                        value.children[index] = this.recursive_splice(
                            child,
                            comp
                        );
                    }
                });
            }

            // Возвращаем результат
            return value;
        },
        recursive_expand: function (value, is_first = false) {
            let result = [];
            if (
                value.children &&
                value.children != undefined &&
                value.children.length > 0
            ) {
                value.children.forEach((child) => {
                    let from_child = this.recursive_expand(child);
                    from_child.forEach((child_value) => {
                        result.push(child_value);
                    });
                });

                value.children = [];
            }

            result.push(value);
            if (is_first) {
                result = result.reverse();
            }

            return result;
        },
        recursive_search: function (value, comp) {
            let result = false;

            if (value.id == comp) {
                // Найден ID
                result = true;
            } else if (
                value.children &&
                value.children != undefined &&
                value.children.length > 0
            ) {
                // Не найден, есть дети
                value.children.forEach((child) => {
                    if (this.recursive_search(child, comp)) {
                        result = true;
                    }
                });
            } else {
                // Не найден, конец
                result = false;
            }

            return result;
        },
        process_structure: function () {
            let cache = [];
            this.nestableCache.forEach((page, index) => {
                // id, parent_id, sort
                let child = {
                    i: page.id,
                };
                cache.push(child);
            });

            let structure = this.process_recursive(this.nestableItems, null);

            return { c: cache, s: structure };
        },
        process_recursive: function (structure, parent_id = null) {
            let result = [];

            structure.forEach((page, index) => {
                if (page.children != null && page.children.length > 0) {
                    let children_pages = this.process_recursive(
                        page.children,
                        page.id
                    );
                    children_pages.forEach((child) => {
                        // id, parent_id, sort
                        let child_rs = {
                            i: child.i,
                            p: child.p,
                            s: child.s,
                        };
                        result.push(child_rs);
                    });
                }

                // id, parent_id, sort
                let page_filled = {
                    i: page.id,
                    p: parent_id,
                    s: index,
                };

                result.push(page_filled);
            });
            return result;
        },
        save_structure: function () {
            let r_headers = { "X-CSRF-TOKEN": this.csrf };

            // Готовим переменные для запроса
            let r_form = new FormData();
            let config = {
                headers: r_headers,
            };
            let data = this.process_structure();
            console.log(data);

            // Добавляем информацию
            r_form.append("_method", "POST");
            let r_parameters = {
                action: "saveStructure",
                s: JSON.stringify(data.s),
                c: JSON.stringify(data.c),
            };

            // Добавляем параметры
            for (let [param_key, param_value] of Object.entries(r_parameters)) {
                r_form.append(param_key, param_value);
            }

            // Создаём инстанцию запроса
            let r_instance = axios.create(config);
            this.is_pending_request = true;
            this.is_error_response = false;

            r_instance
                .post("", r_form)
                .then((response) => {
                    if (
                        response.data.successful == undefined ||
                        response.data.successful == true
                    ) {
                        console.log("Success");
                    } else {
                        throw JSON.stringify(response.data);
                    }

                    this.is_pending_request = false;
                    return true;
                })
                .catch((error) => {
                    this.is_error_response = true;
                    this.is_pending_request = false;
                    console.warn("Error");
                    console.log(error);
                });
        },
    },
    data: () => ({
        nestableItems: [],
        nestableCache: [],
        csrf: document.querySelector('meta[name="csrf-token"]').content,
        is_pending_request: false,
        is_error_response: false,
    }),
    mounted() {
        console.log(JSON.parse(this.pagesCache));
        this.nestableCache = JSON.parse(this.pagesCache);
        console.log(JSON.parse(this.pagesStructure));
        this.nestableItems = JSON.parse(this.pagesStructure);

        this.nestableCache.forEach((elem, index) => {
            if (!this.nestableCache[index].children) {
                this.nestableCache[index].children = [];
            }
        });
    },
};
</script>