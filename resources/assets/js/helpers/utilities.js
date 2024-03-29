import moment from 'moment';

export default {
  addClass(el, className) {
    if (el.classList) el.classList.add(className)
    else el.className += ' ' + className
  },
  hasClass(el, className) {
    const hasClass = el.classList
      ? el.classList.contains(className)
      : new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className)

    return hasClass
  },
   destroyDatatable(tableID){
    let table = $(tableID).DataTable({
        destroy: true,
        retrieve: true,
        autoWidth: false,
        // processing:true,
        paging: true
    });
    table.destroy();
  },
  disabledAfterToday(date) {
    const today = new Date();
    return date > new Date(today);
  },
 datatable(tableID){
    let table = $(tableID).DataTable({
        destroy: true,
        retrieve: true,
        autoWidth: false,
        // processing:true,
        paging: true,
        "language": {
            "emptyTable": "No records available",
        }
    });
},
userImage(image){
    if(image && image != null && image != 0) {
          return image
    } else {
          return '/assets/img/default-avatar.jpg'
    }
},
  formatMoney(amount, currency = 0) {
    if (!currency) {
      currency = {
        precision: 2,
        thousand_separator: ',',
        decimal_separator: '.',
        symbol: '$',
      }
    }

    amount = amount / 100

    let { precision, decimal_separator, thousand_separator, symbol } = currency

    try {
      precision = Math.abs(precision)
      precision = isNaN(precision) ? 2 : precision

      const negativeSign = amount < 0 ? '-' : ''

      let i = parseInt(
        (amount = Math.abs(Number(amount) || 0).toFixed(precision))
      ).toString()
      let j = i.length > 3 ? i.length % 3 : 0

      let moneySymbol = `<span style="font-family: sans-serif">${symbol}</span>`

      return (
        moneySymbol +
        ' ' +
        negativeSign +
        (j ? i.substr(0, j) + thousand_separator : '') +
        i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousand_separator) +
        (precision
          ? decimal_separator +
            Math.abs(amount - i)
              .toFixed(precision)
              .slice(2)
          : '')
      )
    } catch (e) {
      console.log(e)
    }
  },

  formatGraphMoney(amount, currency = 0) {
    if (!currency) {
      currency = {
        precision: 2,
        thousand_separator: ',',
        decimal_separator: '.',
        symbol: '$',
      }
    }

    amount = amount / 100

    let { precision, decimal_separator, thousand_separator, symbol } = currency

    try {
      precision = Math.abs(precision)
      precision = isNaN(precision) ? 2 : precision

      const negativeSign = amount < 0 ? '-' : ''

      let i = parseInt(
        (amount = Math.abs(Number(amount) || 0).toFixed(precision))
      ).toString()
      let j = i.length > 3 ? i.length % 3 : 0

      let moneySymbol = `${symbol}`

      return (
        moneySymbol +
        ' ' +
        negativeSign +
        (j ? i.substr(0, j) + thousand_separator : '') +
        i.substr(j).replace(/(\d{3})(?=\d)/g, '$1' + thousand_separator) +
        (precision
          ? decimal_separator +
            Math.abs(amount - i)
              .toFixed(precision)
              .slice(2)
          : '')
      )
    } catch (e) {
      console.log(e)
    }
  },

  checkValidUrl(url) {
    if (
      url.includes('http://localhost') ||
      url.includes('http://127.0.0.1') ||
      url.includes('https://localhost') ||
      url.includes('https://127.0.0.1')
    ) {
      return true
    }
    let pattern = new RegExp(
      '^(https?:\\/\\/)?' + // protocol
        '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
        '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
        '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
        '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
        '(\\#[-a-z\\d_]*)?$',
      'i'
    ) // fragment locator

    return !!pattern.test(url)
  },

  checkValidDomainUrl(url) {
    if (url.includes('localhost')) {
      return true
    }
    let pattern = new RegExp(
      '^([0-9A-Za-z-\\.@:%_+~#=]+)+((\\.[a-zA-Z]{2,3})+)(/(.)*)?(\\?(.)*)?'
    )
    return !!pattern.test(url)
  },

  fallbackCopyTextToClipboard(text) {
    var textArea = document.createElement('textarea')
    textArea.value = text
    // Avoid scrolling to bottom
    textArea.style.top = '0'
    textArea.style.left = '0'
    textArea.style.position = 'fixed'
    document.body.appendChild(textArea)
    textArea.focus()
    textArea.select()
    try {
      var successful = document.execCommand('copy')
      var msg = successful ? 'successful' : 'unsuccessful'
      console.log('Fallback: Copying text command was ' + msg)
    } catch (err) {
      console.error('Fallback: Oops, unable to copy', err)
    }
    document.body.removeChild(textArea)
  },
  copyTextToClipboard(text) {
    if (!navigator.clipboard) {
      this.fallbackCopyTextToClipboard(text)
      return
    }
    navigator.clipboard.writeText(text).then(
      function () {
        return true
      },
      function (err) {
        return false
      }
    )
  },
  arrayDifference(array1, array2) {
    return array1.filter((i) => {
      return array2.indexOf(i) < 0
    })
  },
  getBadgeStatusColor(status) {
    switch (status) {
      case 'DRAFT':
        return {
          bgColor: '#F8EDCB',
          color: '#744210',
        }
      case 'PAID':
        return {
          bgColor: '#D5EED0',
          color: '#276749',
        }
      case 'UNPAID':
        return {
          bgColor: '#F8EDC',
          color: '#744210',
        }
      case 'SENT':
        return {
          bgColor: 'rgba(246, 208, 154, 0.4)',
          color: '#975a16',
        }
      case 'REJECTED':
        return {
          bgColor: '#E1E0EA',
          color: '#1A1841',
        }
      case 'ACCEPTED':
        return {
          bgColor: '#D5EED0',
          color: '#276749',
        }
      case 'VIEWED':
        return {
          bgColor: '#C9E3EC',
          color: '#2c5282',
        }
      case 'EXPIRED':
        return {
          bgColor: '#FED7D7',
          color: '#c53030',
        }
      case 'PARTIALLY PAID':
        return {
          bgColor: '#C9E3EC',
          color: '#2c5282',
        }
      case 'OVERDUE':
        return {
          bgColor: '#FED7D7',
          color: '#c53030',
        }
      case 'COMPLETED':
        return {
          bgColor: '#D5EED0',
          color: '#276749',
        }
      case 'DUE':
        return {
          bgColor: '#F8EDCB',
          color: '#744210',
        }
      case 'YES':
        return {
          bgColor: '#D5EED0',
          color: '#276749',
        }
      case 'NO':
        return {
          bgColor: '#FED7D7',
          color: '#c53030',
        }
    }
  },
  dateFormat(value) {
    return moment(value).format("DD/MM/YYYY");
  },
  currentDate() {
    return moment().format("YYYY-MM-DD");
  },
  compareVersion(v1, v2, options) {
    const lexicographical = options && options.lexicographical
    const zeroExtend = options && options.zeroExtend
    let v1parts = v1.split('.')
    let v2parts = v2.split('.')
    function isValidPart(x) {
      return (lexicographical ? /^\d+[A-Za-z]*$/ : /^\d+$/).test(x)
    }
    if (!v1parts.every(isValidPart) || !v2parts.every(isValidPart)) {
      return NaN
    }
    if (zeroExtend) {
      while (v1parts.length < v2parts.length) v1parts.push('0')
      while (v2parts.length < v1parts.length) v2parts.push('0')
    }
    if (!lexicographical) {
      v1parts = v1parts.map(Number)
      v2parts = v2parts.map(Number)
    }
    for (let i = 0; i < v1parts.length; ++i) {
      if (v2parts.length == i) {
        return 1
      }
      if (v1parts[i] == v2parts[i]) {
        continue
      } else if (v1parts[i] > v2parts[i]) {
        return 1
      } else {
        return -1
      }
    }
    if (v1parts.length != v2parts.length) {
      return -1
    }
    return 0
  }
}
