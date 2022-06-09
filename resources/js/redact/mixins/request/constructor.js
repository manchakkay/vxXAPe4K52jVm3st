// Подмодуль для формирования запросов

// Импортируем библиотеки
import axios from "axios";

export default {
    methods: {
        $_request_create(r_method, r_headers, r_parameters) {
            // Готовим переменные для запроса
            let r_form = new FormData();
            let config = {
                headers: r_headers,
            };

            // Добавляем информацию
            r_form.append("_method", r_method);

            // Добавляем параметры
            for (let [param_key, param_value] of Object.entries(r_parameters)) {
                r_form.append(param_key, param_value);
            }

            // Создаём инстанцию запроса
            let r_instance = axios.create(config);

            // Возвращаем запрос и форму
            return {
                instance: r_instance,
                form: r_form,
            };
        }
    }
};