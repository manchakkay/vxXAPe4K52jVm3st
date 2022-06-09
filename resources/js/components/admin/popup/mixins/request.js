export default {
    methods: {
        sendRequest: function (target_selector, destination, method, key, data = {}) {
            let target = document.querySelector(target_selector);
            if (target.classList.contains("disabled")) {
                return;
            }
            target.classList.add("disabled");

            let form = target.closest('.pp-body').querySelector('.pp-form');
            let form_data = new FormData(form);

            form_data.append("_method", method);
            form_data.append("action", key);

            axios
                .post(
                    destination,    
                    form_data
                )
                .then(response => {
                    this.handleResponse(target, response);

                    return true;
                })
                .catch(error => {
                    this.handleError(target, error);
                });
        },
    }

};