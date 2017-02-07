var ONBOARD = {
    ajax: function (url, callback) {
        var request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (request.readyState === 4) {
                if (request.status === 200) {
                    callback(request);
                }
            }
        }
        request.open('GET', url);
        request.send();
    }
}
