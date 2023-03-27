let dtRange = {
    start_date_time: '',
    end_date_time: ''
};

window.setDtRange = function (startDateTime = '', endDateTime = '') {
    dtRange = {
        start_date_time: startDateTime,
        end_date_time: endDateTime
    };
}

window.getDtRange = function () {
    return dtRange;
}

window.bindDtRange = function () {
    $('body').on('click', 'a.paginate_button:not(.current):not(.disabled)', function () {
        let startDateTime = $('input#startDateTime').val(),
            endDateTime = $('input#endDateTime').val();
        setDtRange(startDateTime, endDateTime);
    }).on('change', 'select[name="users-table_length"]', function () {
        let startDateTime = $('input#startDateTime').val(),
            endDateTime = $('input#endDateTime').val();
        setDtRange(startDateTime, endDateTime);
    }).on('change input', "input[type='search']", function () {
        let startDateTime = $('input#startDateTime').val(),
            endDateTime = $('input#endDateTime').val();
        setDtRange(startDateTime, endDateTime);
    }).on('change input', 'input#startDateTime', function () {
        let startDateTime = $('input#startDateTime').val(),
            endDateTime = $('input#endDateTime').val();
        setDtRange(startDateTime, endDateTime);
        getDatatable().ajax.reload();
    }).on('change input', 'input#endDateTime', function () {
        let startDateTime = $('input#startDateTime').val(),
            endDateTime = $('input#endDateTime').val();
        setDtRange(startDateTime, endDateTime);
        getDatatable().ajax.reload();
    });
}

window.getPeriodDate = function (type) {
    let today = new Date(), startDate, endDate;
    switch (type) {
        case 'today':
            startDate = today.getFullYear() + "-" + fillZero(today.getMonth() + 1) + "-" + fillZero(today.getDate());
            let tomorrow = new Date(today.setDate(today.getDate() + 1));
            endDate = tomorrow.getFullYear() + "-" + fillZero(tomorrow.getMonth() + 1) + "-" + fillZero(tomorrow.getDate());
            break;
        case 'yesterday':
            endDate = today.getFullYear() + "-" + fillZero(today.getMonth() + 1) + "-" + fillZero(today.getDate());
            let yesterday = new Date(today.setDate(today.getDate() - 1));
            startDate = yesterday.getFullYear() + "-" + fillZero(yesterday.getMonth() + 1) + "-" + fillZero(yesterday.getDate());
            break;
        case 'week':
            let firstOfCurrentWeek = new Date(new Date(today.setDate(today.getDate() - today.getDay() + 1)).toUTCString());
            startDate = firstOfCurrentWeek.getFullYear() + "-" + fillZero(firstOfCurrentWeek.getMonth() + 1) + "-" + fillZero(firstOfCurrentWeek.getDate());
            let firstOfNextWeek = new Date(firstOfCurrentWeek.setDate(firstOfCurrentWeek.getDate() + 7));
            endDate = firstOfNextWeek.getFullYear() + "-" + fillZero(firstOfNextWeek.getMonth() + 1) + "-" + fillZero(firstOfNextWeek.getDate());
            break;
        case 'month':
            let firstOfCurrentMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            startDate = firstOfCurrentMonth.getFullYear() + "-" + fillZero(firstOfCurrentMonth.getMonth() + 1) + "-" + fillZero(firstOfCurrentMonth.getDate());
            let firstOfNextMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
            endDate = firstOfNextMonth.getFullYear() + "-" + fillZero(firstOfNextMonth.getMonth() + 1) + "-" + fillZero(firstOfNextMonth.getDate());
            break;
        default:
            startDate = '';
            endDate = '';
            break;
    }

    if (startDate.length === 0) {
        dateTrigger('', '');
    } else {
        dateTrigger(startDate + ' 00:00:00', endDate + ' 00:00:00');
    }

}

window.fillZero = function (int) {
    if (int.toString().length === 1) {
        return "0" + int;
    } else {
        return int;
    }
}

window.dateTrigger = function (startDateTime, endDateTime) {
    $('input#startDateTime').val(startDateTime);
    $('input#endDateTime').val(endDateTime);
    filterByDtRange();
}

window.refreshWithDtRange = function () {
    Swal.fire({
        title: '',
        html: '<i class="fas fa-sync fa-pulse fa-spin"></i>',
        type: 'info',
        showConfirmButton: false,
        allowOutsideClick: false
    });
    let startDateTime = $('input#startDateTime').val(),
        endDateTime = $('input#endDateTime').val();
    setDtRange(startDateTime, endDateTime);
    getDatatable().ajax.reload(null, false);
}

window.filterByDtRange = function () {
    Swal.fire({
        title: '',
        html: '<i class="fas fa-sync fa-pulse fa-spin"></i>',
        type: 'info',
        showConfirmButton: false,
        allowOutsideClick: false
    });
    let startDateTime = $('input#startDateTime').val(),
        endDateTime = $('input#endDateTime').val();
    setDtRange(moment(startDateTime).utc().format('YYYY-MM-DD HH:mm:ss'), moment(endDateTime).utc().format('YYYY-MM-DD HH:mm:ss'));
    getDatatable().ajax.reload();
}