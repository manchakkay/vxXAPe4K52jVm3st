export default {
    methods: {
        // Динамическая загрузка скриптов
        loadJS: function (file, callback) {
            let script_elem = document.createElement("script");
            script_elem.async = true;
            script_elem.src = file;

            if (callback) {
                script_elem.onreadystatechange = () => {
                    if (
                        script_elem.readyState === "loaded" ||
                        script_elem.readyState === "complete"
                    ) {
                        // no need to be notified again
                        script_elem.onreadystatechange = null;
                        // notify user
                        callback(script_elem);
                    }
                };

                // other browsers
                script_elem.onload = () => {
                    callback(script_elem);
                };
            }

            document.documentElement.firstChild.appendChild(script_elem);
        },
    }
}