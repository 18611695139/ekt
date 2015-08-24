$.extend($.fn.validatebox.defaults.rules, {
	phone: {
		validator: function (value, param) {
            if(value == param && typeof param != 'undefined')
                return true;
            var phones = value.split('/');
            var len = phones.length;
            for(var i=0;i<len;i++){
                if(/^((0[0-9]{2,3}\-[2-9][0-9]{6,7}(\-\d+)?)|(1[0-9]{10})|(400[0-9]{7}))$/.test(phones[i]))
                    continue;
                else
                    return false;
            }
            return true;
		},
		message: '请填写正确的电话号码格式'
	},
    email: {
        validator: function (value, param) {
            if(value == param && typeof param != 'undefined')
                return true;
			return /^(\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*)(;(\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*))*;?$/.test(value);
		},
		message: '请输入正确的email地址'
    },
    number: {
		validator: function (value, param) {
            if(value == param && typeof param != 'undefined')
                return true;
			return /^[0-9]*$/.test(value);
		},
		message: '请输入数字'
	},
    postcode: {
        validator: function (value, param) {
            if(value == param && typeof param != 'undefined')
                return true;
			return /^[0-9]{6}$/.test(value);
		},
		message: '请输入正确的邮编号码'
    }
});