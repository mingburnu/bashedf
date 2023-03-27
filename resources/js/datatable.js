let datatable = {};

window.setDatatable = function (t) {
    datatable = t;
}

window.getDatatable = function () {
    return datatable;
}

window.getDatatableI18n = function () {
    return Lang.messages[Lang.getLocale() + "." + "datatable"];
}

window.refresh = function () {
    Swal.fire({
        title: '',
        html: '<i class="fas fa-sync fa-pulse fa-spin"></i>',
        type: 'info',
        showConfirmButton: false,
        allowOutsideClick: false
    });
    datatable.ajax.reload(null, false);
}

window.targetThs = function (ths) {
    ths.forEach(function (th, i) {
        th.targets = i;
    });

    return ths;
}

window.getEventRow = function (e) {
    for (let i = 0; i < getDatatable().rows().eq(0).length; i++) {
        if (getDatatable().row(i).data().id === e.id) {
            return getDatatable().row(i);
        }
    }
    return getDatatable().row(-1);
}

window.switchDetail = function (element, callback = null) {
    let tr = element.parentElement.parentElement,
        row = getDatatable().row($(tr));

    if (row.child.isShown()) {
        row.child.hide();
        tr.classList.remove(['shown']);
        element.classList.replace('fa-minus-circle', 'fa-plus-circle');
    } else {
        row.child(composeDetail(row)).show();
        tr.classList.add(['shown']);
        element.classList.replace('fa-plus-circle', 'fa-minus-circle');
        callback != null ? callback(element) : null;
    }
}

window.loadIconByDTable = function (element) {
    let tr = element.parentElement.parentElement,
        row = getDatatable().row($(tr));

    if (row.data().id !== undefined && document.getElementById(`${row.data().id}_icon`) != null) {
        loadBankIcon(row.data().account_number, document.getElementById(`${row.data().id}_icon`));
    }
}

window.composeDetail = function (row) {
    let html = '';
    for (let [key, value] of row.data().detail) {
        html += '<div class="form-group form-row">' +
            `<span class="col-sm-2">${key}</span>` +
            `<div class="col-sm-10"><p>${value}</p></div>` +
            '</div>';
    }
    return html;
}

window.refreshDetail = function (row) {
    if (row.child.isShown()) {
        row.child(composeDetail(row)).show();
    }
}

window.composeDataTableSchema = function (schema) {
    return $.extend({
        processing: true,
        serverSide: true,
        autoWidth: true,
        ordering: false,
        language: getDatatableI18n()
    }, schema);
}