// Примесь для отладки работы редактора

export default {
    methods: {
        $_debug_log: function (type, key, message) {

            switch (type) {
                case "info":
                    console.log("%c● <%c" + key + "%c>: %s", "color: blue", "color: black", "color: blue", message);
                    break;

                case "debug":
                    console.log("%c● <%c" + key + "%c>: %s", "color: green", "color: black", "color: green", message);
                    break;

                case "warning":
                    console.trace();
                    console.warn("%c▲ <%c" + key + "%c>: %s", "color: orange", "color: black", "color: orange", message);
                    break;

                case "error":
                    console.trace();
                    console.error("%c● <%c" + key + "%c>: %s", "color: red", "color: black", "color: red", message);
                    break;

                case "alert":
                    console.trace();
                    console.error("%c■ <%c" + key + "%c>: %s", "color: red; font-weight: bold; text-decoration: underline", "color: black", "color: red", message);
                    break;
            }
        }
    }
};