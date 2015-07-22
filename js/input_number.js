$(function() {
inputNumber = function(params) {
	var element = '#' + params.id;
	var allowNegative = params.allowNegative;
	var allowDecimal = params.allowDecimal;
	var val = $( element ).val();
	var prevVal = val;
	var hasMinus = val.indexOf('-') >= 0 ? true : false;
	var hasPoint = val.indexOf('.') >= 0 ? true : false;
	var isCopyPaste = false;
	var checkStr = function(val) {
		var newStr = '';
		var i = 0;
		var len = val.length;
		var pointCnt = 0;

		if (allowNegative &&
			val.indexOf('-') == 0) {
			newStr += val[0];
			i++;
		}
		
		for (; i < len; i++) {
			if (allowDecimal && val[i] == '.') {
				pointCnt++;
			}

			if (Number.isInteger(+val[i]) ||
				(allowDecimal &&
				 val[i] == '.' && pointCnt <= 1)) {
				newStr += val[i];
			}
			else {
				break;
			}
		}

		return newStr;
	}
	var getCompletedStr = function(val) {
		if (!allowDecimal || val == '') {
		     return val;
		}

		if (val[0] == '.') {
			val = '0' + val;
		}
		else if (val[val.length - 1] == '.') {
			val = val + '0';
		}

		return val;
	}
	var getNewStrByScipIndex = function(str, index) {
		var newStr = '';
		var len = str.length;

		for (var i = 0; i < len; i++) {
			if (i == index) {
				continue;
			}

			newStr += val[i];
		}

		return newStr;
	}
	var comma2point = function(str) {
		return str.replace(',', '.');
	}
	var minusCode = jQuery.browser.mozilla ? 173 : 189;

	$( element ).keydown(function (e) {
		var e = e || window.event;
		var code = e.charCode || e.which;

		if (allowNegative && !hasMinus && !e.ctrlKey &&
			(code == minusCode || code == 109)) {
			hasMinus = true;
			prevVal = $( element ).val();
			return true;
		}

		if (allowDecimal && !hasPoint && !e.ctrlKey &&
			(code == 190 || code == 110)) {
			hasPoint = true;
			prevVal = $( element ).val();
			return true;
		}

		if (e.ctrlKey) {
			if (code == 65 || code == 67 ||
				code == 86 || code == 88 ||
				code == 90) {
				prevVal = $( element ).val();
				isCopyPaste = true;
				return true;
			}
		}

		if (e.shiftKey) {
			if (code == 45) {
				isCopyPaste = true;
			}
		}

		if (code >= 58  && code <= 90 ||  
			code >= 106 && code <= 111 ||
			code == 32 || code > 145) {
			return false;				
		}

		prevVal = $( element ).val();

		return true;

	}).keyup(function(e) {
		val = $( element ).val();

		var e = e || window.event;
		var code = e.charCode || e.which;
		var newVal = val;

		if (isCopyPaste) {
			var tval = comma2point(val);
			newVal = checkStr(tval);
			if (newVal != tval) {
				newVal = prevVal;
			}
			isCopyPaste = false;
		}

		if (allowDecimal) {
			var pi = val.indexOf('.');

			if (pi == 1 &&
			   (val[pi - 1] == '-' ||
				val[pi + 1] == '-')) {
				newVal = getNewStrByScipIndex(val, pi);
				hasPoint = false;
			}
			else if ((pi == 0 || pi > 1) &&
					(val[pi - 1] == '-' ||
				 	 val[pi + 1] == '-')) {
				var len = val.length;
				var mi = val[pi - 1] == '-' ? pi - 1 : pi + 1;
				newVal = getNewStrByScipIndex(val, mi);
				hasMinus = false;
			}

			if (hasPoint && pi == -1) {
				hasPoint = false;
			}
		}

		if (allowNegative) {
			var mi = val.indexOf('-');
			
			if (mi >= 0 && val[0] != '-') {
				newVal = prevVal;
				hasMinus = false;
			}

			if (hasMinus && mi == -1) {
				hasMinus = false;
			}
		}

		if (val != newVal) {
			$( element ).val( checkStr(newVal) );
		}
	}).change(function() {
		$( element ).val(getCompletedStr(checkStr(val)));
	}).contextmenu(function(e) {
		return false;
	});
}});