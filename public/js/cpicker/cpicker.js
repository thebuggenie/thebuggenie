/* HTML Color Picker    
 * http://cpicker.com/ 
 */

/*
 * Change: add an iconPath option for TBG3
 * @Author Edno
 */

// JavaScript Document
var ColorPicker = Class.create();
ColorPicker.prototype = {
	web_values: [0, 51, 102, 153, 204, 255],
	r_values:	[204, 102, 0, 255, 153, 51],
	fixed_values: ['000', '333', '666', '999', 'ccc', 'fff', 'f00', '0f0', '00f', 'ff0', '0ff', 'f0f'],
	hex: 		['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f'],
	landing:	null,
	options:	{},
	cnumber:	7,
	
	initialize: function() {
		var options = Object.extend({
			cubeLandingClass: 'picker-cube-landing',
			cubePixelClass:	'picker-cube-pixel',
			cubeClass:		'picker-cube',
			landingClass:	'picker-landing',
			fixedClass:		'picker-fixed-colors',
			clearClass:		'picker-clear',
			headerClass:	'picker-header',
			sampleClass:	'picker-sample',
			textValueClass:	'picker-text-value',
			iconClass:		'picker-icon',
			iconCubeClass:	'picker-icon-cube',
			iconContiniousClass: 'picker-icon-continious',
			iconRainbowClass: 'picker-icon-rainbow',
			iconGrayscaleClass:	'picker-icon-grayscale',
			grayscaleLandingClass: 'picker-grayscale-landing',
			rainbowLandingClass: 'picker-rainbow-landing',
			rainbowPixelClass: 'picker-rainbow-pixel',
			defaultColor:	'#ffffff',
			closeClass:		'picker-close-icon',
			pickerID:		'color-picker',
			pixelSize: 		10,	// min 8 - max 12
			pixelMargin:	1,
			iconPath:		'/js/cpicker/images/' // change by Edno
		}, arguments[0] || {});
		
		//
		// picker header
		// 
		this.header = document.createElement('DIV');
		this.header.className = options.headerClass;
		
		//
		// picker sample
		//
		this.sample = document.createElement('DIV');
		this.sample.className = options.sampleClass;
		this.header.appendChild(this.sample);
		
		//
		// picker sample text
		//
		this.textValue = document.createElement('SPAN');
		this.textValue.className = options.textValueClass;
		this.header.appendChild(this.textValue);
		
		
		//
		// picker landing
		//
		this.landing = document.createElement('DIV');
		this.landing.id = options.pickerID;
		this.landing.className = options.landingClass;
		this.landing.style.width = (options.pixelSize + options.pixelMargin) * 
				(Math.floor(this.web_values.length * this.web_values.length / 2)  + 3) + 1 + 'px';
				
		Event.observe(this.landing, 'mouseover', this.onPickerMove.bindAsEventListener(this));
		Event.observe(this.landing, 'click', this.onPickerClick.bindAsEventListener(this));
		Event.observe(this.landing, 'mousedown', this.dragStart.bindAsEventListener(this));
		this.landing.onselectstart = function(event) { 
			event = event || window.event;
			var element = Event.element(event);
			if (element.className != options.textValueClass)
				return false;
		};

		this.landing.appendChild(this.header);
		
		// cube icon
		this.iconCube = document.createElement('A');
		this.iconCube.className = options.iconClass + ' ' + options.iconCubeClass;
		this.iconCube.style.backgroundImage = "url('" + options.iconPath + "icon-cube.gif')";
		this.iconCube.onclick = this.onIconSelect.bind(this, 'cube');
		this.header.appendChild(this.iconCube);
		// cube icon
		this.iconContinious = document.createElement('A');
		this.iconContinious.className = options.iconClass + ' ' + options.iconContiniousClass;
		this.iconContinious.style.backgroundImage = "url('" + options.iconPath + "icon-continious.gif')"; // change by Edno
		this.iconContinious.onclick = this.onIconSelect.bind(this, 'continious');
		this.header.appendChild(this.iconContinious);
		// cube icon
		this.iconRainbow = document.createElement('A');
		this.iconRainbow.className = options.iconClass + ' ' + options.iconRainbowClass;
		this.iconRainbow.style.backgroundImage = "url('" + options.iconPath + "icon-rainbow.gif')"; // change by Edno
		this.iconRainbow.onclick = this.onIconSelect.bind(this, 'rainbow');
		this.header.appendChild(this.iconRainbow);
		// cube icon
		this.iconGrayscale = document.createElement('A');
		this.iconGrayscale.className = options.iconClass + ' ' + options.iconGrayscaleClass;
		this.iconGrayscale.style.backgroundImage = "url('" + options.iconPath + "icon-grayscale.gif')"; // change by Edno
		this.iconGrayscale.onclick = this.onIconSelect.bind(this, 'grayscale');
		this.header.appendChild(this.iconGrayscale);
		
		// close icon
		var closeIcon = document.createElement('DIV');
		closeIcon.className = options.closeClass;
		closeIcon.style.backgroundImage = "url('" + options.iconPath + "icon-close.gif')"; // change by Edno
		closeIcon.title = 'Close picker';
		closeIcon.onclick = this.hide.bind(this);
		this.header.appendChild(closeIcon);
		
		this.options = options;
	/*
		this.setColor(options.defaultColor);
		this.findColor(options.defaultColor);
	*/
		this.setClear(this.header);
		
		//
		// fictive loading
		//
		this.loading = document.createElement('DIV');
		this.loading.className = 'picker-load-cube';
		this.loading.backgroundImage = "url('" + options.iconPath + "load-cube.png')"; // change by Edno
		this.landing.appendChild(this.loading);
		this.preloadLoadings();
		
//		this.onIconSelect('cube');
	},
	
	onIconSelect: function(iType) {
		switch (iType) {
			case 'continious':
				if (!this.module_continious)
					this.continious();
				break;
			case 'rainbow':
				if (!this.module_rainbow)
					this.rainbow();
				break;
			case 'grayscale':
				if (!this.module_grayscale)
					this.grayscale();
				break;
			default:
				if (!this.module_cube)
					this.cube();
				iType = 'cube';
		}
		
		if (this.loading)
			this.loading.style.display = 'none';
			
		if (this.module_grayscale) {
			this.module_grayscale.style.display = (iType == 'grayscale') ? 'block' : 'none';
			this.iconGrayscale.style.borderBottom = (iType == 'grayscale') ? '2px solid #7f9db9' : 'none';
		}
		if (this.module_rainbow) {
			this.module_rainbow.style.display = (iType == 'rainbow') ? 'block' : 'none';
			this.iconRainbow.style.borderBottom = (iType == 'rainbow') ? '2px solid #7f9db9' : 'none';
		}
		if (this.module_continious) {
			this.module_continious.style.display = (iType == 'continious') ? 'block' : 'none';
			this.iconContinious.style.borderBottom = (iType == 'continious') ? '2px solid #7f9db9' : 'none';
		}
		if (this.module_cube) {
			this.module_cube.style.display = (iType == 'cube') ? 'block' : 'none';
			this.iconCube.style.borderBottom =  (iType == 'cube') ? '2px solid #7f9db9' : 'none';
		}
	},
	
	getPixel: function(color) {
		var pixel = document.createElement('DIV');
			pixel.className = this.options.cubePixelClass;
			pixel.style.backgroundColor = color;
			pixel.style.width = this.options.pixelSize + 'px';
			pixel.style.height = this.options.pixelSize + 'px';
			
		var colors = this.RGB(color);
		pixel.color_r = colors[0];
		pixel.color_g = colors[1];
		pixel.color_b = colors[2];
		pixel.color = '#' + this.d2h(colors[0], 2) + this.d2h(colors[1], 2) + this.d2h(colors[2], 2);
		return pixel;
	},
	
	setClear: function(element) {
		var div = document.createElement('DIV');
		div.className = this.options.clearClass;
		element.appendChild(div);
	},
	
	cube: function() {
		
		this.cube_model = document.createElement('DIV');
		this.cube_model.className = this.options.cubeLandingClass;
		this.cube_model.style.width = Math.floor(this.web_values.length / 2) * 
				((this.options.pixelSize * this.web_values.length) + 
				  this.web_values.length * this.options.pixelMargin) + 'px';
		
		for (var r = 0; r < this.web_values.length; r++) {
		 var cube = document.createElement('DIV');
		 	cube.className = this.options.cubeClass;
			cube.style.width = (this.options.pixelSize * this.web_values.length) + 
					this.web_values.length * this.options.pixelMargin + 'px';

		 for (var b = 0; b < this.web_values.length; b++)
		  for (var g = 0; g < this.web_values.length; g++) {
			var pixel = this.getPixel('rgb('+ this.web_values[r] +','+ this.web_values[g] +','+ this.web_values[b] +')');
			cube.appendChild(pixel);
		  }
		 
		 this.cube_model.appendChild(cube);
		}


		// fixed colors
		var fixed_colors = document.createElement('DIV');
		fixed_colors.className = this.options.fixedClass;
		fixed_colors.style.width = (this.options.pixelSize + this.options.pixelMargin) * 3 + 'px';
		
		for (var i = 0; i < this.fixed_values.length * 3; i++) {
			var pixel = this.getPixel(((i - 1) % 3 == 0) 
					? '#' + this.fixed_values[Math.round((i - 2) / 3)] : '#000');
			fixed_colors.appendChild(pixel);
		}
		
		// module
		var module = document.createElement('DIV');
		module.appendChild(fixed_colors);
		module.appendChild(this.cube_model);
		this.module_cube = module;
		
		this.setClear(module);
		this.landing.appendChild(module);
	},
	
	continious: function() {
		
		this.continious_model = document.createElement('DIV');
		this.continious_model.className = this.options.cubeLandingClass;
		this.continious_model.style.width = Math.floor(this.web_values.length / 2) * 
				((this.options.pixelSize * this.web_values.length) + 
				  this.web_values.length * this.options.pixelMargin) + 'px';
		
		for (var r = 0; r < this.r_values.length; r++) {
		 var cube = document.createElement('DIV');
		 	cube.className = this.options.cubeClass;
			cube.style.width = (this.options.pixelSize * this.web_values.length) + 
					this.web_values.length * this.options.pixelMargin + 'px';

		  var b_start = ((r % 2) && (r > 2)) || ((r % 2 == 0) && (r <= 2))
		  			? this.web_values.length-1 : 0;
		  var b_finish = ((r % 2) && (r > 2)) || ((r % 2 == 0) && (r <= 2))
		  			? -1 : this.web_values.length;
		  var b_step = ((r % 2) && (r > 2)) || ((r % 2 == 0) && (r <= 2))
		  			? -1 : 1;

		  var g_start = (r <= 2) ? this.web_values.length-1 : 0;
		  var g_finish = (r <= 2) ? -1 : this.web_values.length;
		  var g_step = (r <= 2) ? -1 : 1;

		 for (var g = g_start; g != g_finish; g = g + g_step) 
		  for (var b = b_start; b != b_finish; b = b + b_step) {

			var pixel = this.getPixel('rgb('+ this.r_values[r] +','+ this.web_values[g] +','+ this.web_values[b] +')');
			cube.appendChild(pixel);
		  }

		 this.continious_model.appendChild(cube);
		}


		// fixed colors
		var fixed_colors = document.createElement('DIV');
		fixed_colors.className = this.options.fixedClass;
		fixed_colors.style.width = (this.options.pixelSize + this.options.pixelMargin) * 3 + 'px';
		
		for (var i = 0; i < this.fixed_values.length * 3; i++) {
			var pixel = this.getPixel(((i - 1) % 3 == 0) 
					? '#' + this.fixed_values[Math.round((i - 2) / 3)] : '#000');
			fixed_colors.appendChild(pixel);
		}
		
		// module
		var module = document.createElement('DIV');
		module.appendChild(fixed_colors);
		module.appendChild(this.continious_model);
		this.module_continious = module;
		
		this.setClear(module);
		this.landing.appendChild(module);
	},
	
	grayscale: function() {
		this.grayscale_model = document.createElement('DIV');
		this.grayscale_model.className = this.options.grayscaleLandingClass;
		
		for (var c = 255; c >= 0; c--) {
			var pixel = this.getPixel('rgb('+ c +','+ c +','+ c +')');
			this.grayscale_model.appendChild(pixel);
		}

		for (var c = 0; c < 17; c++) {
			var pixel = this.getPixel('rgb(236,233,216)');
			this.grayscale_model.appendChild(pixel);
		}

		// module
		var module = document.createElement('DIV');
		module.appendChild(this.grayscale_model);
		this.module_grayscale = module;
		
		this.setClear(module);
		this.landing.appendChild(module);
	},
	
	rainbow: function() {
		this.rainbow_model = document.createElement('DIV');
		this.rainbow_model.className = this.options.rainbowLandingClass;
		var step = 16;

		for(var j=0; j < this.cnumber; j++) {
			for (var i=0; i < 256; i += step) {
				var c_r = [this.d2h(i, 2), 'ff', this.d2h(255-i, 2), '00', '00', this.d2h(i, 2), 'ff'];
				var c_g = ['00', this.d2h(i, 2), 'ff', 'ff', this.d2h(255-i, 2), '00', this.d2h(i, 2)];
				var c_b = ['00', '00', '00', this.d2h(i, 2), 'ff', 'ff', 'ff'];
				var pixel = document.createElement('DIV');
					pixel.className = this.options.rainbowPixelClass;
					pixel.style.backgroundColor = '#' + c_r[j] + c_g[j] + c_b[j];
					pixel.color = '#' + c_r[j] + c_g[j] + c_b[j];
					pixel.style.width = Math.round(this.options.pixelSize / 5) + 'px';
				this.rainbow_model.appendChild(pixel);
			}
		}
		
		// module
		this.module_rainbow = this.rainbow_model;
		this.setClear(this.rainbow_model);
		this.landing.appendChild(this.rainbow_model);
	},
	
	onPickerClick: function(event) {
		var element = Event.element(event);
		if (!element.color) return;
		
		if (this.callback && typeof(this.callback) == 'function') {
			this.callback(this.textValue.innerHTML);
			return;
		}
		
		if (this.selector)
		{
			switch (this.selector.textValue) {
				case 'color':
					this.selector.style.color = this.textValue.innerHTML;
					break;
				case 'border':
					this.selector.style.borderColor = this.textValue.innerHTML;
					break;
				case 'outline':
					this.selector.style.outlineColor = this.textValue.innerHTML;
					break;
				default:
					this.selector.style.backgroundColor = this.textValue.innerHTML;
			}
		}
		this.hide();
	},
	
	onPickerMove: function(event) {
		var element = Event.element(event);
		if (!element.color) return;

		if (this.lastHover) {
			this.lastHover.style.border = 'none';
			this.lastHover.style.width = this.options.pixelSize + 'px';
			this.lastHover.style.height = this.options.pixelSize + 'px';
		}

		if (element.className == this.options.cubePixelClass) 
		{
			this.lastHover = element;
			element.style.border = '1px solid #' + ((element.color_r + element.color_g + element.color_b) / 3 < 127 ? 'fff' : '000');
			element.style.width = this.options.pixelSize - 2 + 'px';
			element.style.height = this.options.pixelSize - 2 + 'px';
		}
		
		this.setColor(element.color);
	},
	
	setColor: function(color) {
		this.textValue.innerHTML = color;
		this.sample.style.backgroundColor = color;
	},
	
	findColor: function(color) {
		if (!this.cube_model) return;
		
		var divs = this.cube_model.getElementsByTagName('DIV');
		var colors = this.RGB(color);
		
		for(var i=0; i < divs.length; i++) {
			var element = divs[i];
			if ((element.color_r == colors[0]) &&
				(element.color_g == colors[1]) && 
				(element.color_b == colors[2])) {
				this.lastHover = element;
				element.style.border = '1px solid #' + 
					((element.color_r + element.color_g + element.color_b) / 3 < 127 ? 'fff' : '000');
				element.style.width = this.options.pixelSize - 2 + 'px';
				element.style.height = this.options.pixelSize - 2 + 'px';
			}
		}
	},
	
	RGB: function(color) {
		if (!color) 
			return [0,0,0];
		
		if (color.indexOf('rgb') != -1) {
			if (/rgb[^0-9]+([0-9]+)[^0-9]+([0-9]+)[^0-9]+([0-9]+)/i.exec(color))
				return [parseInt(RegExp.$1), parseInt(RegExp.$2), parseInt(RegExp.$3)];
		}
		
		if (color.length > 5) {
			if (/^\#?([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2}$)/i.exec(color))
				return [this.h2d(RegExp.$1), this.h2d(RegExp.$2), this.h2d(RegExp.$3)];
		}
		
		if (/^\#?([0-9a-f])([0-9a-f])([0-9a-f])$/i.exec(color))
			return [this.h2d(RegExp.$1 + '' + RegExp.$1), 
					this.h2d(RegExp.$2 + '' + RegExp.$2), 
					this.h2d(RegExp.$3 + '' + RegExp.$3)];
		
		return [0,0,0];
	},
	
	h2d: function(color) {
		eval('color = 0x' + color + ';');
		return parseInt(color);
	},

	d2h: function(number, len) {
		if (number < 16) {
			var res = this.hex[number];
			while (res.length < len) 
				res = '0' + res;
			return res;
		}
			
		var mod = number % 16;
		return this.d2h((number - mod) / 16, len-1).toString() + this.hex[mod];
	},

	invert: function(color) {
		return 255 - color;
	},
	
	show: function(element, selector, pickerType) {
		var element = $(element);
		var landing = $(this.options.pickerID);
		if (!landing) {
			document.body.appendChild(this.landing);
			landing = this.landing;
		}
		
		var pos = Position.cumulativeOffset(element);

		landing.style.left = pos[0] + 'px';
		landing.style.top = pos[1] + element.offsetHeight + 'px';
		landing.style.display = 'block';
		
		this.selector = element;
		this.selector.textValue = selector;
		Event.observe(document.body, 'mouseup', this.dragStop.bindAsEventListener(this));
		Event.observe(document.body, 'mousemove', this.dragDrag.bindAsEventListener(this));
		
		window.setTimeout(this.onIconSelect.bind(this, pickerType), 10);
	},
	
	hide: function() {
		this.landing.style.display = 'none';
		Event.stopObserving(document.body, 'mouseup', this.dragStop.bindAsEventListener(this));
		Event.stopObserving(document.body, 'mousemove', this.dragDrag.bindAsEventListener(this));
	},
	
	dragStart: function(event) {
		var element = Event.element(event);
		if (element.className != this.options.headerClass &&
			element.className != this.options.sampleClass) return;
		
		this.dragging = true;
		this.startPoint = [Event.pointerX(event), Event.pointerY(event)];
		this.startOffset = Position.cumulativeOffset(this.landing);
	},
	
	dragStop: function(event) {
		this.dragging = false;
	},
	
	dragDrag: function(event) {
		if (!this.dragging) return;
		
	    var pointer = [Event.pointerX(event), Event.pointerY(event)];
		var shifting = [pointer[0] - this.startPoint[0], pointer[1] - this.startPoint[1]];
		this.landing.style.left = this.startOffset[0] + shifting[0] + 'px';
		this.landing.style.top = this.startOffset[1] + shifting[1] + 'px';
	},
	
	preloadLoadings: function() {
		var loadings = ['load-cube.png'];
		for(var i=0; i < loadings.length; i++) {
			var newImg = new Image();
			newImg.src = this.options.iconPath + loadings[i]; // change by Edno
		}
	},
	
	setCallback: function(callback) {
		this.callback = callback;
	}
};