export default {
    methods: {
        // Обработка успешного ответа
        handleResponse(target, response, handler) {
            target.classList.remove("disabled");
            location.reload();
        },
        // Обработка ответа с ошибкой
        handleError(target, error, handler) {
            target.classList.remove("disabled");
            // Если ошибка валидации
            if (error.response && error.response.status === 422) {
                let pp = target.closest('.popup');

                for (let [elem_id, elem_errors] of Object.entries(error.response.data.errors)) {
                    try {
                        let elem = pp.querySelector('.fld-wrapper[elem-id="' + elem_id + '"]');
                        let errors_html = "";

                        elem.querySelector('.fld-tooltip').classList.add('active');

                        elem_errors.forEach((error_code) => {

                            if (errors_html.length != 0) {
                                errors_html = errors_html.concat("<br>");
                            }

                            let error = this.translateError(error_code, elem_id);
                            errors_html = errors_html.concat(error);
                        });

                        elem.querySelector('.fld-tooltip-box').innerHTML = errors_html;
                    }
                    catch (e) {
                        //
                    }
                }
            } else {
                if (error.response) {
                    // Ошибка в полученном запросе
                    console.error("response: request error");
                    console.log("data", JSON.stringify(error.response.data, null, 4));
                    console.log("status", error.response.status);
                    console.log("headers", error.response.headers);
                } else if (error.request) {
                    // Запрос отправлен, но ответ не получен
                    console.error("response: no answer", error.request);
                } else {
                    // При настройке запроса произошла ошибка
                    console.error("response: initialization error", error.message);
                }
            }
        },
        // Перевод ошибок полей в текст
        translateError(error_code, context = null) {
            let resultMessage = null;

            switch (error_code) {
                case "required":
                    resultMessage = "Поле обязательно для заполнения";

                    break;
                case "wrong_type":
                    if (context == "cn_title" || context == "cn_link") {
                        resultMessage = "Допустим только формат текстовой строки";
                    } else if (context == "cn_image") {
                        resultMessage = "Необходимо загрузить файл с изображением";
                    } else if (context == "cn_categ") {
                        resultMessage = "Необходимо выбрать существующую категорию";
                    } else {
                        resultMessage = "Проверьте правильность формата";
                    }
                    break;
                case "too_big":
                    if (context == "cn_title" || context == "cn_link") {
                        resultMessage = "Длина строки больше допустимого";
                    } else if (context == "cn_image") {
                        resultMessage = "Размер файла больше допустимого";
                    } else {
                        resultMessage = "Превышен максимальный размер";
                    }

                    break;
                case "repeat":
                    if (context == "cn_link") {
                        resultMessage = "Адрес уже занят, попробуйте другой";
                    } else {
                        resultMessage = "Такой элемент уже существует";
                    }

                    break;
                case "wrong_syntax":
                    if (context == "cn_link") {
                        resultMessage = "Разрешена строчная латиница, цифры и дефисы, дефисы запрещены в начале и конце ссылки";
                    } else {
                        resultMessage = "Проверьте правильность написания";
                    }

                    break;
                case "wrong_format":
                    if (context == "cn_image") {
                        resultMessage = "Данный формат изображения не поддерживается";
                    } else {
                        resultMessage = "Неверный формат";
                    }

                    break;
                case "not_found":
                    if (context == "cn_categ") {
                        resultMessage = "Выбранная категория не найдена, перезагрузите страницу";
                    } else {
                        resultMessage = "Такая опция не найдена, перезагрузите страницу";
                    }
            }

            return resultMessage;
        },
    }

};
