// Подмодуль для отправки запросов

export default {
    methods: {
        $_request_send(request, action, target = "") {
            request.instance
                .post(target, request.form)
                .then((response) => {
                    if (response.data.successful == undefined || response.data.successful == true) {
                        this.$_request_handleResponse(response, action);
                    } else {
                        throw JSON.stringify(response.data);
                    }

                    return true;
                })
                .catch((error) => {
                    this.$_request_handleError(error);
                });
        },
    }
};