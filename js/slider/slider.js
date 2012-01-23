/*
 * iPhone lock slider - requires jQuery 1.2.3+
 *
 * Alex Boone
 * aboone.com
 *
 * 5/18/2008
 */
Slider = function(container, config){
	return {
		init : function(){
			this.container = container;
			// configuration options
			var cfg = config ? config : {};
			this.handler = cfg.handler ? cfg.handler : function(){ alert('unlocked!'); };
			this.color = cfg.color ? this.COLORS[cfg.color] : this.COLORS["plain"];
			this.mode = cfg.mode ? cfg.mode : "click";
			this.waver = cfg.waver ? cfg.waver : 150;
			var message = cfg.message ? cfg.message : "slide to unlock";
			var handleContent = cfg.handleContent ? cfg.handleContent : "";
			
			$('#'+this.container).html(
				'<div class="track">'
				+ '<div class="track-left"/>'
			 	+ '<div class="track-right"/>'
				+ '<div class="track-center">'
					+ '<div class="track-message">'+message+'</div>'
				+ '</div>'
				+ '<div class="handle">' + handleContent + '</div>'
				+ '</div>');
			
			this.handle = $('#' + this.container + ' .handle');
			this.track = $('#' + this.container + ' .track');
			this.message = $('#' + this.container + ' .track-message');
			
			// set color
			this.handle.css("backgroundPosition", "0 " + (this.color*39) + "px")
			
			var self = this;
			
			if(this.mode == "noclick"){
				this.handle.mouseover(function(e){
					self.initX(e);
					$('html').bind('mousemove', {slider: self}, self.slideNoClick);
				});
			} else {
				this.handle.mousedown(function(e){
					self.initX(e);
					$('html').bind('mousemove', {slider: self}, self.slide);
					$('html').one('mouseup', {slider: self}, self.release);
				});
			}
		},
		
		initX : function(e){
			var tx = this.track.get(0).offsetLeft;
			var ty = this.track.get(0).offsetTop;
			var tw = this.track.get(0).offsetWidth;
			var th = this.track.get(0).offsetHeight;
			var hx = this.handle.get(0).offsetLeft;
			var hw = this.handle.get(0).offsetWidth;
			var pd = hx-tx;
			
			this.min = 0;
			this.zero = hx;
			this.delta = e.pageX - this.zero;
			this.max = tw-2*pd-hw;
			this.maxY = ty+th+this.waver;
			this.minY = ty-this.waver;
		},
		
		slideX : function(e){
			return Math.max(this.min,Math.min(e.pageX-this.zero-this.delta,this.max));
		},
		
		slide : function(e){
			var s = e.data.slider;
			var x = s.slideX(e);
			s.handle.css("left", x);
			s.message.css("opacity", Math.max(0,1-(x*2)/s.max));
		},
		
		slideNoClick : function(e){
			var s = e.data.slider;
			s.slide(e);
			if( s.handle.css("left").replace(/[^0-9]/g,'') == s.max
				|| e.pageY > s.maxY || e.pageY < s.minY){
				s.release(e);
			}
		},
		
		release : function(e){
			var s = e.data.slider;
			$('html').unbind('mousemove');
			if(s.handle.css("left").replace(/[^0-9]/g,'') == s.max){
				if(s.handler() !== false){
					s.reset();
				}
			} else {
				s.reset();
			}
		},
		
		setMessage : function(msg){
			this.message.html(msg);
		},
		
		reset: function(){
			var t = 400;
			this.handle.animate({left: 0}, t);
			this.message.animate({opacity: 1}, t);
		},
		
		COLORS : {
			plain: 0,
			red: 1,
			green: 2
		}
	};
}
