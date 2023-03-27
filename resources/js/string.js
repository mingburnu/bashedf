import * as uuid from 'uuid';

window.convertNullProperty = function (obj, s) {
    let propNames = Object.getOwnPropertyNames(obj);
    for (let i = 0; i < propNames.length; i++) {
        let propName = propNames[i];
        if (obj[propName] === null || obj[propName] === undefined) {
            obj[propName] = s;
        }
    }
}

window.uuid = uuid;