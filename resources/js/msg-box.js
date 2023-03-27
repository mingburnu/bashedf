window.listMessages = function (obj) {
    let s = "";
    for (let k in obj) {
        if (obj.hasOwnProperty(k)) {
            if (typeof obj[k] === "object") {
                s = s + listMessages(obj[k])
            } else {
                if (typeof obj[k] === 'string' && obj[k].startsWith('data:image') && obj[k].indexOf(';base64,') !== -1) {
                    let image = new Image(),
                        tmp = document.createElement("div");
                    image.src = obj[k];
                    image.classList.add('m-auto');
                    tmp.appendChild(image);

                    s = s + `<p>${tmp.innerHTML}</p>`;
                } else {
                    s = s + `<p>${obj[k]}</p>`;
                }
            }
        }
    }

    return s;
}

window.fireSuccessBox = function (response) {
    let messages = response.data.messages === undefined ? '' : listMessages(response.data.messages);
    Swal.fire(Lang.get('message.success'), messages, 'success');
}

window.fireErrorBox = function (error) {
    if (error.response.status === 422) {
        Swal.fire(Lang.get('message.fail'), listMessages(error.response.data.errors), 'error');
    } else {
        Swal.fire(error.response.status.toString(), error.response.statusText, 'error');
    }
}

window.getEventBtn = function (element) {
    if (element === null || element.classList.contains('btn')) {
        return element;
    } else {
        return getEventBtn(element.parentElement)
    }
}

window.composePopupBoxSchema = function (schema) {
    return $.extend({
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonText: Lang.get('ui.confirm'),
        cancelButtonText: Lang.get('ui.cancel'),
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
    }, schema);
}