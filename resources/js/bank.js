window.getBanks = function () {
    return Lang.messages[Lang.getLocale() + "." + "banks"];
}

window.loadBankIcon = function (cardNo, frame) {
    axios.get('https://ccdcapi.alipay.com/validateAndCacheCardInfo.json', {
        params: {
            _input_charset: 'utf-8',
            cardBinCheck: 'true',
            cardNo: cardNo
        }
    }).then(function (response) {
        if (response.data.validated) {
            frame.innerHTML = `<img src="https://apimg.alipay.com/combo.png?d=cashier&t=${response.data.bank}" alt="${response.data.bank}">`;
        }
    });
}

window.getBankName = function (cardNo, input) {
    axios.get('https://ccdcapi.alipay.com/validateAndCacheCardInfo.json', {
        params: {
            _input_charset: 'utf-8',
            cardBinCheck: 'true',
            cardNo: cardNo
        }
    }).then(function (response) {
        if (response.data.validated) {
            let bankCode = response.data.bank;
            if (typeof getBanks()[bankCode] === "string") {
                input.val(getBanks()[bankCode]);
            } else {
                input.val(bankCode);
            }
        } else {
            input.val('');
        }
    });
}