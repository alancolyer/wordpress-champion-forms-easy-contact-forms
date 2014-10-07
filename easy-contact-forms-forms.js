
ufoForms = new function(){

	this.regex={};
	this.regex.numeric = /^[0-9]+$/;
	this.regex.integer = /^\-?[0-9]+$/;
	this.regex.decimal = /^\-?[0-9]*\.?[0-9]+$/;
	this.regex.email = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,6})+$/;
	this.regex.alpha = /^[a-z]+$/i;
	this.regex.alphaNumeric = /^[a-z0-9]+$/i;
	this.regex.alphaDash = /^[a-z0-9_-]+$/i;
	this.regex.natural = /^[0-9]+$/i;
	this.regex.naturalNoZero = /^[1-9][0-9]*$/i;
	this.regex.ip = /^((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})$/i;
	this.regex.base64 = /[^a-zA-Z0-9\/\+=]/i;
	this.regex.currency = /^-?(?:0|[1-9]\d{0,2}(?:,?\d{3})*)(?:\.\d+)?$/;

	this.forms={};
	this.submits={};
	this.els = {};

	this.validateForm = function (formid, enforce){
		var submits = this.submits[formid];
		if (!submits) {
			return true;
		}
		var fields = this.forms[formid];
		if (!fields) {
			return true;
		}
		var isValid = true;
		for (var i = 0; i < fields.length; i++) {
			var config = fields[i];
			if (enforce){
				config.isvalid = ufoForms.validateField(config, 'blur');				
			}
			if (!config.isvalid) {
				isValid = false;
				break;
			}
		}

		for (var i = 0; i < submits.length; i++) {
			var config = submits[i];
			var submit = config.domEl;
			submit.disabled = !isValid;			
		}

		return isValid;
	}

	this.docReady = function(func){
		this.addEvent(document, 'readystatechange', function(){
			if (document.readyState == 'complete'){
				func();
			}
		});
	}

	this.validate = function (config){
		this.docReady(function(){ufoForms.addValidation(config)});
	}

	this.submitButton = function (config){
		this.docReady(function(){ufoForms.addSubmit(config)});
	}

	this.resetButton = function (config){
		this.docReady(function(){ufoForms.addReset(config)});
	}
	
	this.addSubmit = function (config){
		if (!this.submits[config.form]) {
			this.submits[config.form]=[];			
		}
		this.submits[config.form].push(config);

		var submit = document.createElement('button');
		try{
			submit.type = 'button';
		} catch (e) {
			submit.setAttribute('type', 'button');
		};
		submit.className = config.CSSClass || '';
		submit.style.cssText = config.CSSStyle || '';
		var parent = submit;
		if (config.LabelCSSClass || config.LabelCSSStyle) {
			var span = document.createElement('span'); 
			parent.appendChild(span);
			parent = span;
			span.className = config.config.LabelCSSClass || '';
			span.style.cssText = config.config.LabelCSSStyle || '';
		}
		parent.innerHTML = config.Label || '';
		var container = this.get(config.id+'-span');
		container.appendChild(submit);

		config.domEl = submit;
		var formid = config.form;

		this.addEvent(submit, 'click', function(){
			if (!ufoForms.validateForm(formid, true)) {
				return;
			}
			var els = [];
			var frm = ufoForms.get(formid);

			var collections = [];
			collections.push(frm.getElementsByTagName('input'));
			collections.push(frm.getElementsByTagName('select'));
			collections.push(frm.getElementsByTagName('textarea'));
			for (var i = 0; i < collections.length; i++) {
				var collection = collections[i];
				for (var j = 0; j < collection.length; j++) {
					els.push(collection[j]);
				}
			}
			var result = [];
			for (var i = 0; i < els.length; i++){
				var el = els[i];
				var config = ufoForms.els[el.id];
				var empty = config ? ufoForms.isEmpty(config) : el.value == '' || el.value == 'off';
				if (!empty) {
					var id = el.id.split('-');
					id = id[id.length - 2] + '-' + id[id.length - 1];
					value = el.value;
					value = value.replace(/&/g,'%26');
					value = value.replace(/=/g,'%3D');
					result.push(id + '=' + value);
				}
			}
			result = result.join('&');
			ufoForms.request(result, ufoForms.callback);	
		});
	}
	
	this.showMessage = function(resp){
		var formid = resp.formid; 
		if (resp.status == 1) {
			function redirect(){
				if (resp.url) {
					var t = setTimeout('document.location.href = "' + resp.url + '"',3000);
				}
			}
			function success() {
				while (form.hasChildNodes()){
	  				form.removeChild(form.firstChild);
				}			
				if (resp.text) {
					var div = document.createElement('div');
					div.className = resp.className;
					div.innerHTML = resp.text;
					form.appendChild(div);
					ufoForms.doFade(form, 0, 1, 1000, redirect);
				}
				else {
					redirect();
				}
			}
			var form = this.get(formid);
			form.disabled = true;
			this.doFade(form, 1, 0, 1000, success);	
		}
		else if (resp.status == 2) {
			var message = this.get(formid+'-message');
			message.innerHTML = resp.text;
			this.addClass(message, resp.className);
		}
	}
       
	this.hadleError = function(uhxr) {
		switch(uhxr.status){
    		case 12029:
			case 12030:
    		case 12031:
    		case 12152:
    		case 12159:
				uhxr.cObject.request(uhxr.cValues, uhxr.cFunction);
				break;
			default:				
				alert('Error. Status='+uhxr.status);
		}
	}
       
	this.callback = function(){
		if (uhxr.readyState == 4) {
			if (uhxr.status == 200) {
				var resp = eval('(' + uhxr.responseText + ')');								
				ufoForms.showMessage(resp);
			}
			else {
				ufoForms.hadleError(uhxr);
			}
		}
	}

	this.request = function(values, callbackfunction, m, asynch){
		m = m || 'add';
		asynch = asynch == undefined ? true : asynch;
		values = values.replace(/\+/gi,'%2B');
		values += '&t=CustomForms';
		values += '&ac=1';
		values += '&m='+m;
		values += '&action=easy-contact-forms-submit'; 
		uhxr = this.getXHR();
		if (!uhxr) return false;
		uhxr.cValues = values;
		uhxr.cObject = this;
		uhxr.cFunction = callbackfunction;
		uhxr.onreadystatechange = callbackfunction;
		uhxr.open('POST', ufobaseurl, asynch);
		uhxr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		uhxr.send(values);
  	}

	this.getXHR = function(){
		if (window.XMLHttpRequest) { 
			uhxr = new XMLHttpRequest();
			if (uhxr.overrideMimeType) {
				uhxr.overrideMimeType('text/html');
			}
		} else if (window.ActiveXObject) { 
			try {
				uhxr = new ActiveXObject('Microsoft.XMLHTTP');
			} catch (e) {}
		}
		return uhxr;
  	}

	this.get = function(id){
		return document.getElementById(id);
	}

	this.isEmpty = function(config){
		var val = config.domEl.value;
		if (config.IsBlankValue && config.DefaultValue == val) {
			return true;
		}
		if (this.hasClass(config.domEl, 'ufo-cb')){
			return config.domEl.value != 'on';
		}
		return config.domEl.value == '';
	}

	this.addReset = function (config) {
		var reset = this.get(config.id);
		config.domEl = reset;

		var formid = config.form;
		this.addEvent(reset, 'click', function(){
			var fields = ufoForms.forms[formid];
			if (!fields) {
				return;
			}
			for (var i = 0; i < fields.length; i++) {
				var config = fields[i];
				ufoForms.fieldReset(config);
			}
			var frm = ufoForms.get(ufoForms.frmIdPx+formid);
			frm.reset();
			ufoForms.validateForm(config.form);
		});
	}
	
	this.getMessageDiv = function(id, config, absolute) {
		var mdiv = this.get(id);
		if (mdiv) {
			if (absolute) {
				mdiv.style.position = 'absolute';
				var parent = mdiv.parentNode;
				parent.removeChild(mdiv);
				if (this.hasClass(parent, 'ufo-cell-center') && !parent.hasChildNodes()) {
					var sparent = parent.parentNode;
					sparent.removeChild(parent);
					sparent.parentNode.removeChild(sparent);
				}
			}
		}
		else {
			mdiv = document.createElement('div');
			mdiv.style.position = 'absolute';
			mdiv.id = id;
		}
		if (mdiv.style.position == 'absolute') {
			config.domEl.parentNode.appendChild(mdiv);
		}
		return mdiv;
	}                                
                                                   
	this.addMessages = function(config) {
		if (config.Required || config.Validate) {
			var mdiv = this.getMessageDiv(config.id+'-invalid', config, config.AbsolutePosition);
			mdiv.innerHTML = config.RequiredMessage || '';
			var className = config.RequiredMessageCSSClass || 'ufo-customfields-invalid';
			this.addClass(mdiv, className);
			className = config.RequiredMessagePosition ? 'ufo-hint-position-'+config.RequiredMessagePosition : 'ufo-hint-position-right';
			this.addClass(mdiv, className);				
			if (!config.InvalidCSSClass) {
				config.InvalidCSSClass = 'ufo-customfields-invalidvalue';            	
			}
			if (config.RequiredMessageCSSStyle) {
				try {
					mdiv.style.cssText = config.RequiredMessageCSSStyle;		
				} catch (e) {}
			}
			if (config.AbsolutePosition) {
				mdiv.style.position = 'absolute';
			}
			mdiv.style.display = 'none';
		}
		if (config.showValid) {
			var mdiv = this.getMessageDiv(config.id+'-valid', config, config.ValidMessageAbsolutePosition);
			mdiv.innerHTML = config.ValidMessage || '';
			var className = config.ValidCSSClass || 'ufo-customfields-valid';
			this.addClass(mdiv, className);
			className = config.ValidMessagePosition ? 'ufo-hint-position-'+config.ValidMessagePosition : 'ufo-hint-position-right';
			this.addClass(mdiv, className);				
			if (config.ValidCSSStyle) {
				try {
					mdiv.style.cssText = config.ValidCSSStyle;		
				} catch (e) {}
			}
			if (config.ValidMessageAbsolutePosition) {
				mdiv.style.position = 'absolute';
			}
			mdiv.style.display = 'none';
		}
	}

	this.addValidation = function(config){

		config.isvalid = true;

		this.els[config.id] = config;
		if (!this.forms[config.form]) {
			this.forms[config.form]=[];			
		}
		this.forms[config.form].push(config);			
		var el = this.get(config.id);
		config.domEl = el;

		for (var evt in config.events) {
			this.addEvent(el, evt, (function(evt, config){
				return function(event){
					if ( event.preventDefault ) {
						event.preventDefault();
					} else {
						event.returnValue = false;
					}
					config.isvalid = ufoForms.validateField(config, evt);
					ufoForms.validateForm(config.form);
				}
			})(evt, config));
		}
		this.addMessages(config);
	}

	this.validateField = function(config, event){
		var result = undefined, types = config.events[event];
		for (i = 0; i < types.length; i++) {
			if (typeof(ufoValidators) == 'undefined') {
				ufoValidators = {};				
			}
			var type = types[i];
			var vresult = ufoForms['validate'+type] ? 
				ufoForms['validate'+type](config, event) : ufoValidators[type] ? 
				ufoValidators[type](config, event) : ufoForms.validateRe(type, config, event);  				
			if (result == false) {
				continue;	
			}		
			result = vresult == undefined ? result : vresult;
		}
		if (typeof(result) != 'undefined') {
			this.changeView(result, config);
		}
		else {
			result = true;
		}
		return result;
	}

	this.validateRe = function(type, config, event){
		if (!config.required && this.isEmpty(config)) {
			this.fieldReset(config);
		 	return undefined;
		} 
		var result = true;
		if (config.required && this.isEmpty(config)) {
		 	result = false;
		} 
		else if (this.regex[type]) {
			result = this.regex[type].test(config.domEl.value);
		}
		return result;
	}
	
	this.validaterequired = function(config, event){
		return !this.isEmpty(config);
	}

	this.validatedefault = function(config, event){
		if (event == 'blur') {
			if (config.domEl.value == '') {
				config.domEl.value = config.DefaultValue;				
			}
			this.switchClass(config.domEl, config.DefaultValueCSSClass, config.domEl.value == config.DefaultValue);
		}
		if (event == 'focus') {
			this.removeClass(config.domEl, config.DefaultValueCSSClass);
			if (config.domEl.value == config.DefaultValue) {
				config.domEl.value = '';				
			}
		}
		return undefined;
	}

	this.validateminmax = function(config, event){
		if (!config.required && this.isEmpty(config)) {
			this.fieldReset(config);
		 	return undefined;
		} 
		var value = config.domEl.value ? config.domEl.value : '';
		if (config.max && value.length > config.max) {
			return false;	
		}
		if (config.min && value.length < config.min) {
			return false;	
		}
		return true;
	}

	this.validateminmaxnumeric = function(config, event){
		if (!config.required && this.isEmpty(config)) {
			this.fieldReset(config);
		 	return undefined;
		} 
		var value = config.domEl.value ? config.domEl.value : '0';
		if (value > config.max) {
			return false;	
		}
		if (value < config.min) {             
			return false;	
		}
		return true;
	}

	this.changeView = function(result, config){
		if (result) {
			this.fieldValid(config);
		}
		else {
			this.fieldInvalid(config);
		}
	} 
	this.doFade = function(el, from, to, duration, callback){
		if (duration == undefined) {
			duration = 400;
		}
		var fade = new _bsn.Fader(el,from, to, duration, callback);
	}

	this.fadeOut = function(elid, duration, callback){
		var el = this.get(elid);
		if (!el) {
	   		return;
		}
		if (el.style.display == 'none') {
			if (callback) {
				callback(el);
			}
	   		return;
		}
		if (!callback) {
			callback = function(){
				el.style.display='none';
			};		
		}
		this.doFade(el, 1, 0, duration, callback);	
	}

	this.fadeIn = function(elid, duration, callback){
		var el = this.get(elid);
		if (!el) {
			return;
		}
		_bsn.Fader._setOpacity(el, 0);
		el.style.display = 'block';
		if (el.style.position == 'absolute') {
			this.alignOffset(el);
		}
		else {
			this.alignWidth(el);
		}
		this.doFade(el, 0, 1, duration, callback);	
	}

	this.alignWidth = function(el) {
	}

	this.alignOffset = function(el) {
		var pid = el.id.split('-');
		pid.pop();
		var parent = this.get(pid.join('-'));
		if (this.hasClass(parent, 'ufo-hidden')) {
			parent = parent.parentNode;;
		}		
		parent.parentNode.style.position = 'relative';		
		var pright = this.hasClass(el, 'ufo-hint-position-right');
		var pbottom = this.hasClass(el, 'ufo-hint-position-bottom');
		var delta = 5;
		var xOffset = pright ? parent.offsetWidth + delta : 0;
		var yOffset = pbottom ? parent.offsetTop + parent.offsetHeight + delta : parent.offsetTop - el.offsetHeight - delta;
		yOffset = pright ? - Math.max(0, (el.offsetHeight - parent.offsetHeight) / 2 ) : yOffset;
		var width = el.offsetWidth;
		if (pright && !el.style.width) {
			el.style.width = width+'px';
		}		
		el.style.top = yOffset+'px';
		el.style.left = xOffset+'px';
		parent.parentNode.appendChild(el);		
	}

	this.fieldValidInvalid = function (config, valid){
		var el =  config.domEl, callback;
		if (config.InvalidCSSClass && valid) {
			this.removeClass(el, config.InvalidCSSClass);
		}
		if (config.InvalidCSSClass && !valid) {
			this.addClass(el, config.InvalidCSSClass);
		}
		if (valid) {
			if (config.showValid){
				callback = function() {
					var inval = ufoForms.get(el.id+'-invalid');
					inval.style.display = 'none';
					ufoForms.fadeIn(el.id+'-valid');
				};
			}
			this.fadeOut(el.id+'-invalid', 200, callback);
		}
		else {
			if (config.showValid){
				callback = function() {
					var val = ufoForms.get(el.id+'-valid');
					val.style.display = 'none';
					ufoForms.fadeIn(el.id+'-invalid');
				};
				this.fadeOut(el.id+'-valid', 200, callback);
			}
			else {
				this.fadeIn(el.id+'-invalid');
			}
		}
	}

	this.showHide = function(id, show){
		var display = show ? 'block' : 'none';
		var el = this.get(id);
		if (el) {
			el.style.display = display;
		}
	} 

	this.fieldReset = function (config){
		config.isvalid = true;
		if (config.InvalidCSSClass) {
			this.removeClass(config.domEl, config.InvalidCSSClass);
		}
		this.showHide(config.id+'-invalid', false);
		if (config.showValid){
			this.showHide(config.id+'-valid', false);
		}
	}

	this.fieldValid = function (config, valid){
		this.fieldValidInvalid(config, true);
	}

	this.fieldInvalid = function (config, valid){
		this.fieldValidInvalid(config, false);
	}

	this.addEvent = function(elem, evType, fn) {
		if (elem.addEventListener) {
			elem.addEventListener(evType, fn, false);
		}
		else if (elem.attachEvent) {
			elem.attachEvent('on' + evType, fn);
		}
		else {
			elem['on' + evType] = fn;
		}
	}

	this.hasClass = function(el, className){
		var re = new RegExp("(^|\\s)" + className + "(\\s|$)", "g");
		return re.test(el.className);
	}

	this.switchClass = function(el, className, on){
		if (on) {
			this.addClass(el, className);
		}
		else {
			this.removeClass(el, className);
		}
	}
	  
	this.addClass = function(el, className){
		var re = new RegExp("(^|\\s)" + className + "(\\s|$)", "g");
		if (re.test(el.className)) return;
		el.className = (el.className + " " + className).replace(/\s+/g, " ").replace(/(^ | $)/g, "");
	}
	  
	this.removeClass = function(el, className){
		var re = new RegExp("(^|\\s)" + className + "(\\s|$)", "g");
		el.className = el.className.replace(re, "$1").replace(/\s+/g, " ").replace(/(^ | $)/g, "");
	}
}

if (typeof(_bsn) == 'undefined') {
	_bsn = {}
	_bsn.Fader = {}

	_bsn.Fader = function (ele, from, to, fadetime, callback) {	
		if (!ele) return false;
	
		this.ele = ele;
		this.from = from;
		this.to = to;
		this.callback = callback;
		this.nDur = fadetime;
		this.nInt = 50;
		this.nTime = 0;
		var p = this;
		this.nID = setInterval(function() { p._fade() }, this.nInt);
	}

	_bsn.Fader.prototype._fade = function() {
		this.nTime += this.nInt;
		function tween(t,b,c,d)	{
			return b + ( (c-b) * (t/d) );
		}

		var ieop = Math.round( tween(this.nTime, this.from, this.to, this.nDur) * 100 );
		_bsn.Fader._setOpacity(this.ele, ieop);
	
		if (this.nTime == this.nDur) {
			clearInterval( this.nID );
			if (this.callback != undefined)
				this.callback(this.ele);
		}
	}

	_bsn.Fader._setOpacity = function(el, ieop) {
		var op = ieop/100;
		if (el.filters) {
			try {
				el.filters.item('DXImageTransform.Microsoft.Alpha').opacity = ieop;
			} catch (e) { 
				el.style.filter = 'progid:DXImageTransform.Microsoft.Alpha(opacity='+ieop+')';
			}
		}
		else {
			el.style.opacity = op;
		}
	}
}
