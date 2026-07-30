/*!
 * jquery.base64.js 0.0.3 - https://github.com/yckart/jquery.base64.js
 * Makes Base64 en & -decoding simpler as it is.
 *
 * Based upon: https://gist.github.com/Yaffle/1284012
 *
 * Copyright (c) 2012 Yannick Albert (http://yckart.com)
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php).
 * 2013/02/10
 **/
// #

jQuery(function($){	
	function page__initialize(argument) {
		if( $('.Input--apbupdate').length === 0 ){
			$('#misc-publishing-actions').append('<input type="hidden" class="Input--apbupdate" name="apbupdate" value="1" />');
			$('#edittag').append('<input type="hidden" name="apbupdate" class="Input--apbupdate" value="1" />');
		}
		// # TOP MENU EDITS
/*			var First__Icon__List = {
				'wp-admin-bar-wp-logo':'<span class="YouColor-reader-text">YourColor</span>',
				'wp-admin-bar-site-name':'<i class="fa-solid fa-house"></i>',
				'wp-admin-bar-updates':'<i class="fa-solid fa-arrows-rotate"></i>',
				'wp-admin-bar-comments':'<i class="fa-solid fa-comment-dots"></i>',
				'wp-admin-bar-new-content':'<i class="fa-solid fa-plus"></i>',
			};

			$.each( First__Icon__List ,function(k,v){
				if( $('#'+k+' > a').data('loaded-done') == undefined ){
					$('#'+k+' > a').data('loaded-done',true);
					if( k == 'wp-admin-bar-site-name' ){
						$('#'+k+' > a').append(v);
					}else if( k == 'wp-admin-bar-wp-logo' ){
						$('#'+k+' > a').append(v);
					}else{
						$('#'+k+' > a .ab-icon').append(v);
					}
				}
			});*/

		// # POST STATUS MENU.	
			$('.misc-pub-section').each(function(ek,elem) {
				if( $(elem).data('loaded-done') == undefined ){
					$(elem).data('loaded-done',true);
					var CLass_Text = $(elem).attr('class')+'';
					var Icon = '';
					if( CLass_Text.indexOf( 'misc-pub-post-status' ) > 0 ){
						Icon = '<i class="fa-solid fa-chalkboard"></i>';
					}else if( CLass_Text.indexOf( 'misc-pub-visibility' ) > 0 ){
						Icon = '<i class="fa-solid fa-eye-slash"></i>';		
					}else if( CLass_Text.indexOf( 'misc-pub-revisions' ) > 0 ){
						Icon = '<i class="fa-solid fa-clock-rotate-left"></i>';		
					}else if( CLass_Text.indexOf( 'misc-pub-curtime' ) > 0 ){
						Icon = '<i class="fa-solid fa-calendar-days"></i>';
					}
					$(elem).prepend(Icon);
				}
			});

		// # CHENGE CHECK BOX.
			/*$('label input[type="checkbox"]').each(function(t,ch_elem){
				if( $(ch_elem).data('loaded-done') == undefined ){
					if( $(ch_elem).closest('.menu-item-handle').length === 0 ){

						$(ch_elem).data('loaded-done',true);
						var Ch_Parent = $(ch_elem).parent().attr('class')+'';
						if( Ch_Parent.indexOf( '-CheckBox-Box-Item' ) < 0 ){
							$(ch_elem).parent().addClass('-CheckBox-Box-Item hoverable activable');
							var fvlue = $(ch_elem).parent().text();
							//#$( ".inner" ).after( "<p>Test</p>" );
							$(ch_elem).after('<span></span><em>'+fvlue+'</em>');
						}

					}
				}
			});*/


			$('.YourColorEdits-Class-Style .taxonomy-add-new').each(function(a,t__elem) {
				if( $(t__elem).data('loaded-done') == undefined ){
					$(t__elem).data('loaded-done',true);				
					var ElemText = $(t__elem).text()+'';
					ElemText = ElemText.replace("+", "");
					$(t__elem).addClass('hoverable activable').text(ElemText);
				}
			});

			$('.wp-hidden-children').each(function(rt,children__elem) {
				if( $(children__elem).data('loaded-done') == undefined ){
					$(children__elem).data('loaded-done',true);
					// # 
					$(children__elem).addClass('wp-new-cilhdrens-code');
				}
			});

			$('.notice-dismiss').each(function(e,y__elem ) {
				if( $(y__elem).data('loaded-done') == undefined ){
					$(y__elem).data('loaded-done',true);
					// # 
					$(y__elem).prepend('<i class="fa-solid fa-circle-xmark"></i>');
				}
			});
		$('.YourColorEdits-Class-Style ul.add-menu-item-tabs li, .YourColorEdits-Class-Style ul.category-tabs li, .YourColorEdits-Class-Style ul.wp-tab-bar li').addClass('hoverable activable');

		// # OPTION PAGE SLIDER TAPS.
		if( $(".-Pages-Taps").length > 0 ){
			var NavBarSlider = ["<a class='SliderOwl-prev'><i class='fa-solid fa-arrow-right'></i></a>", "<a class='SliderOwl-next'><i class='fa-solid fa-arrow-left'></i></a>"];
			$(".-Pages-Taps").owlCarousel({
        responsiveClass:true,
        stopOnHover: true,
        loop: false,
        autoPlay: false,
        autoWidth: true,
        addClassActive: true,
        rtl: true,
        navs:true,
        navText : NavBarSlider,
	    });
		}

		// # WIDGETS RESPONSIVE.
		if( $('.Master-Widgets_selected').length > 0 ){
			var WidgetBarWidth = $('.Master-Widgets_selected').outerWidth();
			if( WidgetBarWidth < 1200 ) {
				$('.Master-Widgets_selected').addClass('-fullbar-showsin');
			}else{
				$('.Master-Widgets_selected').removeClass('-fullbar-showsin');
			}
		}

		// # ACCORDIO SORTED ELEMENTS 
		if( $('#side-sortables').length > 0 ){
			var element___bag = $('#side-sortables');
			var End__accordion__element = false;
			var Last__accordion__element = false;
			element___bag.find('.control-section').each(function(k,elem) {
				var TopBarValue = $(elem).attr('class')+'';
				if( TopBarValue.indexOf( 'hide-if-js' ) >= 0 ) {
					var Prevelement = $(elem).prev();
					var activeValue = Prevelement.attr('class')+'';
					if( activeValue.indexOf( 'hide-if-js' ) <= 0 ){
						//$(elem).prev().addClass('test');
						End__accordion__element = Last__accordion__element;
					}
				}
				Last__accordion__element = $(elem);
			});
			if( End__accordion__element != false ){
				$(End__accordion__element).addClass('bordre-zero');
			}
		}

	}
	page__initialize();

	$(window).on('resize', function(){
    	page__initialize();
	});

	$( document ).ajaxSuccess(function( event, request, settings ) {
		page__initialize();
	});


	$('body').on("click",'.-show-models-selected',function() {
		var BTN,MaserSelectors;
		BTN = $(this);
		MaserSelectors = BTN.closest('.Master-Widgets_selected');
		MaserSelectors.toggleClass('shows_models');

		if( MaserSelectors.hasClass('shows_models') ){
			$(this).find('span').text('إغلاق قائمة الشرائح ');
			$('html,body').animate({"scrollTop": MaserSelectors.offset().top },500);
		}else{
			$(this).find('span').text('إضافة شريحة جديدة ');
		}

	});

	function Scroller__EditorBars(elop) {
		// # EDITOR INNER TOOLS GET WIDTH.
			$('.mce-toolbar-grp').each(function(ew,wee) {
				var El__width = $(wee).closest('.wp-editor-container').outerWidth();
				El__width = El__width - 40;
				$(wee).css({"max-width":El__width+'px'});
			});

		// # EDITOR OUT TOOLS GET WIDTH.
			$('.wp-editor-tools').each(function(eqdc,ewr) {
				var El__width = $(ewr).closest('.wp-editor-wrap').find('.wp-editor-container').outerWidth();
				$(ewr).css({"max-width":El__width+'px'});
			});

		// # EDITRO MENU SCROLL TOP EDITS .
	    $('.wp-editor-wrap').each(function(e,elem){
	      var TopBarElem = $(elem).find('.wp-editor-tools');
	      var TopBarValue = TopBarElem.attr('style')+'';

	      if( TopBarValue != undefined ){

		      if( TopBarValue.indexOf( 'absolute' ) >= 0 ) {
		      	TopBarElem.removeClass('EditorScroller').css({"top":"0px"});
		      }else if( TopBarValue.indexOf( 'fixed' ) >= 0 ) {
		      	TopBarElem.addClass('EditorScroller');
		      }

		      var ToolEditorElem = $(elem).find('.mce-toolbar-grp');
		      var ToolBarValue = ToolEditorElem.attr('style')+'';

		      if( ToolBarValue.indexOf( 'absolute' ) >= 0 ) {
		      	ToolEditorElem.removeClass('EditorScroller').css({"top":"0px"});
	      	}else if( ToolBarValue.indexOf( 'fixed' ) >= 0 ) {
		      	ToolEditorElem.addClass('EditorScroller');
		      }

	      }
	    });
	}

  var prevScrollpos = window.pageYOffset;
  window.onscroll = function() {
  	var currentScrollPos = window.pageYOffset;
    var Header__elem = $(".wpadminbar");
    var Body__elem = $("body");

    if( prevScrollpos > currentScrollPos ) {
      Header__elem.removeClass('hidemenu');
      Body__elem.removeClass('hidemenu');
      Scroller__EditorBars(true);
    } else {
      Header__elem.addClass("hidemenu");
      Body__elem.addClass('hidemenu');
      Scroller__EditorBars(false);
    }
    // # 
    prevScrollpos = currentScrollPos;

  }

  // # BASE64 PLUGIN.
		;(function($) {

		    var b64 = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/",
		        a256 = '',
		        r64 = [256],
		        r256 = [256],
		        i = 0;

		    var UTF8 = {

		        /**
		         * Encode multi-byte Unicode string into utf-8 multiple single-byte characters
		         * (BMP / basic multilingual plane only)
		         *
		         * Chars in range U+0080 - U+07FF are encoded in 2 chars, U+0800 - U+FFFF in 3 chars
		         *
		         * @param {String} strUni Unicode string to be encoded as UTF-8
		         * @returns {String} encoded string
		         */
		        encode: function(strUni) {
		            // use regular expressions & String.replace callback function for better efficiency
		            // than procedural approaches
		            var strUtf = strUni.replace(/[\u0080-\u07ff]/g, // U+0080 - U+07FF => 2 bytes 110yyyyy, 10zzzzzz
		            function(c) {
		                var cc = c.charCodeAt(0);
		                return String.fromCharCode(0xc0 | cc >> 6, 0x80 | cc & 0x3f);
		            })
		            .replace(/[\u0800-\uffff]/g, // U+0800 - U+FFFF => 3 bytes 1110xxxx, 10yyyyyy, 10zzzzzz
		            function(c) {
		                var cc = c.charCodeAt(0);
		                return String.fromCharCode(0xe0 | cc >> 12, 0x80 | cc >> 6 & 0x3F, 0x80 | cc & 0x3f);
		            });
		            return strUtf;
		        },

		        /**
		         * Decode utf-8 encoded string back into multi-byte Unicode characters
		         *
		         * @param {String} strUtf UTF-8 string to be decoded back to Unicode
		         * @returns {String} decoded string
		         */
		        decode: function(strUtf) {
		            // note: decode 3-byte chars first as decoded 2-byte strings could appear to be 3-byte char!
		            var strUni = strUtf.replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g, // 3-byte chars
		            function(c) { // (note parentheses for precence)
		                var cc = ((c.charCodeAt(0) & 0x0f) << 12) | ((c.charCodeAt(1) & 0x3f) << 6) | (c.charCodeAt(2) & 0x3f);
		                return String.fromCharCode(cc);
		            })
		            .replace(/[\u00c0-\u00df][\u0080-\u00bf]/g, // 2-byte chars
		            function(c) { // (note parentheses for precence)
		                var cc = (c.charCodeAt(0) & 0x1f) << 6 | c.charCodeAt(1) & 0x3f;
		                return String.fromCharCode(cc);
		            });
		            return strUni;
		        }
		    };

		    while(i < 256) {
		        var c = String.fromCharCode(i);
		        a256 += c;
		        r256[i] = i;
		        r64[i] = b64.indexOf(c);
		        ++i;
		    }

		    function code(s, discard, alpha, beta, w1, w2) {
		        s = String(s);
		        var buffer = 0,
		            i = 0,
		            length = s.length,
		            result = '',
		            bitsInBuffer = 0;

		        while(i < length) {
		            var c = s.charCodeAt(i);
		            c = c < 256 ? alpha[c] : -1;

		            buffer = (buffer << w1) + c;
		            bitsInBuffer += w1;

		            while(bitsInBuffer >= w2) {
		                bitsInBuffer -= w2;
		                var tmp = buffer >> bitsInBuffer;
		                result += beta.charAt(tmp);
		                buffer ^= tmp << bitsInBuffer;
		            }
		            ++i;
		        }
		        if(!discard && bitsInBuffer > 0) result += beta.charAt(buffer << (w2 - bitsInBuffer));
		        return result;
		    }

		    var Plugin = $.base64 = function(dir, input, encode) {
		            return input ? Plugin[dir](input, encode) : dir ? null : this;
		        };

		    Plugin.btoa = Plugin.encode = function(plain, utf8encode) {
		        plain = Plugin.raw === false || Plugin.utf8encode || utf8encode ? UTF8.encode(plain) : plain;
		        plain = code(plain, false, r256, b64, 8, 6);
		        return plain + '===='.slice((plain.length % 4) || 4);
		    };

		    Plugin.atob = Plugin.decode = function(coded, utf8decode) {
		        coded = coded.replace(/[^A-Za-z0-9\+\/\=]/g, "");
		        coded = String(coded).split('=');
		        var i = coded.length;
		        do {--i;
		            coded[i] = code(coded[i], true, r64, a256, 6, 8);
		        } while (i > 0);
		        coded = coded.join('');
		        return Plugin.raw === false || Plugin.utf8decode || utf8decode ? UTF8.decode(coded) : coded;
		    };
		}(jQuery));
		$.base64.utf8encode = true;

	// # JSON PARSER PLUGIN.
		if (typeof JSON !== "object") {
		    JSON = {};
		}

		(function () {
		  "use strict";
		  var rx_one = /^[\],:{}\s]*$/;
		  var rx_two = /\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g;
		  var rx_three = /"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g;
		  var rx_four = /(?:^|:|,)(?:\s*\[)+/g;
		  var rx_escapable = /[\\"\u0000-\u001f\u007f-\u009f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
		  var rx_dangerous = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;

		  function f(n) {
		    return (n < 10) ? "0" + n : n;
		  }

		  function this_value() {
		    return this.valueOf();
		  }

		  if (typeof Date.prototype.toJSON !== "function") {
		    Date.prototype.toJSON = function () {
		      return isFinite(this.valueOf()) ? ( this.getUTCFullYear() + "-" + f(this.getUTCMonth() + 1) + "-" + f(this.getUTCDate()) + "T" + f(this.getUTCHours()) + ":" + f(this.getUTCMinutes()) + ":" + f(this.getUTCSeconds()) + "Z" ) : null;
		    };

		    Boolean.prototype.toJSON = this_value;
		    Number.prototype.toJSON = this_value;
		    String.prototype.toJSON = this_value;
		  }

		  var gap;
		  var indent;
		  var meta;
		  var rep;

		  function quote(string) {
		    rx_escapable.lastIndex = 0;
		    return rx_escapable.test(string) ? 
		    	"\"" + string.replace(rx_escapable, function (a) {
		        var c = meta[a];
		        return typeof c === "string" ? c : "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4);
		      }) + "\""
		    	: 
		    	"\"" + string + "\"";
		  }

		  function str(key, holder) {
		    var i;          // The loop counter.
		    var k;          // The member key.
		    var v;          // The member value.
		    var length;
		    var mind = gap;
		    var partial;
		    var value = holder[key];

		    if ( value && typeof value === "object" && typeof value.toJSON === "function" ) {
		      value = value.toJSON(key);
		    }

		    if (typeof rep === "function") {
		      value = rep.call(holder, key, value);
		    }

		    switch (typeof value) {
		    case "string":
		      return quote(value);
		    case "number":
		      return (isFinite(value)) ? String(value) : "null";
		    case "boolean":
		    case "null":
		      return String(value);
		    case "object":
		      if (!value) {
		        return "null";
		      }
		      gap += indent;
		      partial = [];
		      if (Object.prototype.toString.apply(value) === "[object Array]") {
		        length = value.length;
		        for (i = 0; i < length; i += 1) {
		          partial[i] = str(i, value) || "null";
		        }

		        v = partial.length === 0 ? "[]" : gap ? ( "[\n" + gap + partial.join(",\n" + gap) + "\n" + mind + "]" ) : "[" + partial.join(",") + "]";
		        gap = mind;
		        return v;
		      }

		      if (rep && typeof rep === "object") {
		        length = rep.length;
		        for (i = 0; i < length; i += 1) {
		          if (typeof rep[i] === "string") {
		            k = rep[i];
		            v = str(k, value);
		            if(v){
		              partial.push(quote(k) + ( (gap) ? ": " : ":" ) + v);
		            }
		          }
		        }
		      }else{
		        for (k in value) {
		          if (Object.prototype.hasOwnProperty.call(value, k)) {
		            v = str(k, value);
		          	if (v) {
		              partial.push(quote(k) + ( (gap) ? ": " : ":" ) + v);
		            }
		          }
		        }
		      }
		      v = partial.length === 0 ? "{}" : gap ? "{\n" + gap + partial.join(",\n" + gap) + "\n" + mind + "}" : "{" + partial.join(",") + "}";
		      gap = mind;
		      return v;
		    }
		  }

		  if (typeof JSON.stringify !== "function") {
		    meta = {    // table of character substitutions
		      "\b": "\\b",
		      "\t": "\\t",
		      "\n": "\\n",
		      "\f": "\\f",
		      "\r": "\\r",
		      "\"": "\\\"",
		      "\\": "\\\\"
		    };
		    JSON.stringify = function (value, replacer, space) {
		      var i;
		      gap = "";
		      indent = "";

		      if (typeof space === "number") {
		        for (i = 0; i < space; i += 1) {
		          indent += " ";
		        }
		      } else if (typeof space === "string") {
		        indent = space;
		      }

		      rep = replacer;
		      if (replacer && typeof replacer !== "function" && ( typeof replacer !== "object" || typeof replacer.length !== "number" ) ) {
		        throw new Error("JSON.stringify");
		      }
		      return str("", {"": value});
		    };
		  }

		  if (typeof JSON.parse !== "function") {
		    JSON.parse = function (text, reviver) {
		      var j;
		      function walk(holder, key) {
		        var k;
		        var v;
		        var value = holder[key];
		        if (value && typeof value === "object") {
		            for (k in value) {
		                if (Object.prototype.hasOwnProperty.call(value, k)) {
		                    v = walk(value, k);
		                    if (v !== undefined) {
		                        value[k] = v;
		                    } else {
		                        delete value[k];
		                    }
		                }
		            }
		        }
		        return reviver.call(holder, key, value);
		      }
		      
		      text = String(text);
		      rx_dangerous.lastIndex = 0;
		      if (rx_dangerous.test(text)) {
		        text = text.replace(rx_dangerous, function (a) {
		          return ( "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4) );
		        });
		      }
		      if( rx_one.test( text .replace(rx_two, "@") .replace(rx_three, "]") .replace(rx_four, "") ) ) {
		        j = eval("(" + text + ")");
		        return (typeof reviver === "function") ? walk({"": j}, "") : j;
		      }

		      throw new SyntaxError("JSON.parse");
		    };
		  }
		}());

	// # THEME CONSTRACT FUNCTIONS
		function isEmpty(value){
		  return !$.trim(value);
		}

		function stripslashes (str) {
			return (str + '').replace(/\\(.?)/g, function (s,n1) {
			  switch (n1) {
		      case '\\':
	          return '\\';
		      case '0':
	          return '\u0000';
		      case '':
	          return '';
		      default:
	          return n1;
			  }
			});
		}

		function strip_tags(str, allow){
		 	allow = (((allow || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');

		 	var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
		 	var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
	 		return str.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
		 		return allow.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 :'';
		 	});
		}

	// # Ajax Handler
		var AjaxHandlerXHR = false;
		var RetryInterval;
		function AjaxRequest(data) {
			if( AjaxHandlerXHR != false ) {
				AjaxHandlerXHR.abort();
			}
			data.error = function (jqXHR, exception) {
				var msg = '';
				if (jqXHR.status == 404) {
					msg = 'لم يتم العثور على الصفحة.';
					AjaxHandlerXHR = false;
				} else if (jqXHR.status == 500) {
					msg = 'حدث خطأ اثناء معاينة الملف.';
					AjaxHandlerXHR = false;
				} else if (exception === 'timeout') {
					msg = 'إنتهت مهلة الإتصال.';
					AjaxHandlerXHR = false;
				}
				if( msg != '' && msg != undefined ) {
					RetryInterval = setInterval(AjaxRequest(data), 5000);
				}
			}
			AjaxHandlerXHR = $.ajax(data).done(function(){
				clearInterval(RetryInterval);
				AjaxHandlerXHR = false;
			});
			return true;
		}

	// # UNIQID.
		var charstoformid = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz'.split('');
		var UniqID = function() {
		  var idlength = 10;
		    var uniqid = '';
		    for (var i = 0; i < idlength; i++) {
		      uniqid += charstoformid[Math.floor(Math.random() * charstoformid.length)];
		    }
		    return uniqid;
		}

	// # TOOLTIP .
	  var UserOnpage;
	  $(document).mouseleave(function () {
	    UserOnpage = false;
	    $('body > title--tooltip').remove();
	  });

	  $(document).mouseenter(function () {
	    UserOnpage = true;
	  });

	  var TooltipsTimeout;
	  if( $(window).outerWidth <= 728 ) {

	    $('body').on("mouseover", function(e){
	      var dropdown = $("[data-tooltip]");
	      if (!dropdown.is(e.target) && dropdown.has(e.target).length === 0 || dropdown.hasClass('open')) {
	        $('title--tooltip').remove();
	      }
	    });

	    $('body').on("mouseover",'[data-tooltip]', function(e){
	      var event = e;
	      if($(this).data('off-tooltip') == undefined){
	        var string = $(this).data('tooltip');
	        if( $(this).data('tooltip-base') != undefined) string = $.base64.atob(string,true);

	        var Tip__Uniq = UniqID();
	        if( $(this).data('uniq-id') != undefined ){
	          var Tip__Uniq = $(this).data('uniq-id');
	        }

	        var TooltipElement = $('body > title--tooltip');

	        // # POSTIONS CALC
	        var leftorig = $(this).offset().left;
	        var calcul = $(this).outerWidth() / 2;

	        // # CUSTOM CLASS .
	        if($(this).data('custom-class') != undefined){
	          var ScatsClass = '';
	        }else{
	          var ScatsClass = '';
	        }

	        // # Execute
	        if( TooltipElement.length == 0 ) {
	          $('body').append('<title--tooltip data-tip-uniq="'+Tip__Uniq+'" '+( ( $(this).data('custom-class') != undefined ) ? ' class="'+$(this).data('custom-class')+'"' : '' )+'></title--tooltip>');
	          TooltipElement = $('body > title--tooltip');
	        }

	        TooltipElement.html(string);
	        // # Position
	        LeftPosition = (leftorig + calcul - (TooltipElement.outerWidth() / 2) );
	        //LeftPosition = LeftPosition + 10;
	        if( LeftPosition < 0 ) {
	          LeftPosition = 16;
	        }
	        TopPosition = ($(this).offset().top + $(this).height() + 4) - $(window).scrollTop();
	        if( $(this).data('position') == 'top' ) {
	          TopPosition = ($(this).offset().top - TooltipElement.height() - 4) - $(window).scrollTop() - 10;
	        }
	        TooltipElement.css("left", LeftPosition+'px');
	        TooltipElement.css("top", TopPosition);

	        clearTimeout(TooltipsTimeout);
	        TooltipsTimeout = setTimeout(function(){
	          if( UserOnpage == true ) {
	            $('body > title--tooltip').fadeIn(150);
	          }
	        },300);
	      }
	    });
	  }

	// # CREATE NEW KEY .
		function CreateNewKey(keyargs){
			var Key = UniqID();
			if( jQuery.inArray(Key, keyargs) !== -1 ){
				CreateNewKey(keyargs);
			}else{
				return Key;
			}
		}

		function LoadedTabsActions() {
			if( $('.AddPostSwitch').length > 0 ){
				$('body').addClass('post-edit-page-taps');
				// # 
				$('.AddPostSwitch > li').each(function(k,v) {
					if( $(v).hasClass('active')  ){
						// # ALL ITEMS
							var ALL_MetaBoxes = $(v).parent().data('total-metabox');
							ALL_MetaBoxes = $.base64.atob(ALL_MetaBoxes,true);
							ALL_MetaBoxes = jQuery.parseJSON( ALL_MetaBoxes );
							$(ALL_MetaBoxes).hide();

						// # REMOVE ITEMS
							var RemoveItems = $(v).parent().data('remove-metabox');
							RemoveItems = $.base64.atob(RemoveItems,true);
							RemoveItems = jQuery.parseJSON( RemoveItems );
							$(RemoveItems).remove();

						// # ACTIVE ITEM .
							var Active__Items = $(v).data('metaboxes-tab');
							Active__Items = $.base64.atob(Active__Items,true);
							Active__Items = jQuery.parseJSON( Active__Items );
							$(Active__Items).show();
					}
					
				});

			}	
		}	
		LoadedTabsActions();
	// # THEME CONTEXT .	

		// # REMOVED ALERT
			function RemoveAlert(data) {
				var PopRemoverElement = '<div class="Popver--CoursesAlert">';
				  PopRemoverElement += '<div class="PopverAlertOverlay" onClick="$(this).parent().remove();"></div>';
				  PopRemoverElement += '<div class="PopverInnerElemnt">';
				    PopRemoverElement += '<div class="HeadAlert--Popvoer"><h2>'+data.headtitle+'</h2><span class="Remover--CoursesAlerts hoverable" onClick="$(this).parent().parent().parent().remove();"><i class="fa-solid fa-xmark"></i></span></div>';
				    PopRemoverElement += '<div class="ContentAlert--Popvoer">'+data.alertcontent+'</div>';
				    PopRemoverElement += '<div class="ALertConroller--Popvoer">';
				      PopRemoverElement += '<a href="#" onClick="$(this).parent().parent().parent().remove();" class="hoverable --in--pop--item">تراجع</a>';
				      PopRemoverElement += '<a href="#" '+data.ConfirmAttrs+' class="hoverable AlertIsConfirm --in--pop--item"> نعم </a>';
				    PopRemoverElement += '</div>';
				  PopRemoverElement += '</div>';
				PopRemoverElement += '</div>';
				$('body').append(PopRemoverElement);
			}

			$('body').on("click",'.--in--pop--item',function(e) {e.preventDefault();});

		// # ALERTS MESSAGE .
			function AlertMessage(rgua){
				var PopRemoverElement = '<div class="Popver--CoursesAlert '+rgua.type+'">';
				  PopRemoverElement += '<div class="PopverAlertOverlay" onClick="$(this).parent().remove();"></div>';
				  PopRemoverElement += '<div class="PopverInnerElemnt">';
				    PopRemoverElement += '<p class="-submit-ContentAlert--Popvoer">';
							if(rgua.type == 'error'){
								PopRemoverElement += '<span class="yc-free-icon" style="--yc-fi-p:#121331;--yc-fi-s:#1269eb;width:250px;height:250px"><i class="fa-solid fa-trash-can"></i></span>';
							}else{
								PopRemoverElement += '<span class="yc-free-icon" style="--yc-fi-p:#121331;--yc-fi-s:#1269eb;width:250px;height:250px"><i class="fa-solid fa-circle-check"></i></span>';
							}
						 	PopRemoverElement += '<span class="Alert-Subs--Popvoer">'+rgua.alert+'</span>';
				    PopRemoverElement += '</p>';
				    if(rgua.type == 'error'){
					    PopRemoverElement += '<div class="ALertConroller--Popvoer">';
					      PopRemoverElement += '<a href="#" onClick="$(this).parent().parent().parent().remove();" class="hoverable">حاول مرة أخري </a>';
					    PopRemoverElement += '</div>';
					  }
				  PopRemoverElement += '</div>';
				PopRemoverElement += '</div>';
				$('body').append(PopRemoverElement);

				return PopRemoverElement;
			}		

		// # FIELDS CONTEXT.
			function ContextField_Text(argument,key=false,value) {
				var output = '';
				argument.value = value;

				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;

				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';

				}else{
					InputName = argument.id;
				}

				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output += '<div class="-fix-forms-field-title"><h3>'+argument.title+'</h3></div>';
					output += '<input type="text" value="'+value+'" placeholder="'+argument.title+'" name="'+InputName+'" id="'+InputName+'">';
					output += ( ( argument.disc != undefined ) ) ? '<descor>'+argument.disc+'</descor>' : '';

				output += '</div>';
				return output;
			}

			function ContextField_Title(argument,key=false,value) {
				var output = '';
				argument.value = value;

				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;

				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';

				}else{
					InputName = argument.id;
				}

				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output +=  '<div class="-fix-forms-title-fields">'+( ( argument.icon != undefined ) ? argument.icon : '')+'<div class="-fix-form-InnerTitle"><h3>'+argument.title+'</h3>'+( ( argument.disc != undefined ) ? '<descor>'+argument.disc+'</descor>' : '')+'</div></div>';
				output += '</div>';
				return output;
			}

			function ContextField_Number(argument,key=false,value) {
				var output = '';
				argument.value = value;

				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';

				}else{
					InputName = argument.id;
				}

				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output += '<div class="-fix-forms-field-title"><h3>'+argument.title+'</h3></div>';
					output += '<input type="number" value="'+value+'" placeholder="'+argument.title+'" name="'+InputName+'" id="'+InputName+'">';
					output += ( ( argument.disc != undefined ) ) ? '<descor>'+argument.disc+'</descor>' : '';
				output += '</div>';
				return output;
			}

			function ContextField_Editor(argument,key=false,value) {
				var output = '';
				argument.value = value;

				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';

				}else{
					InputName = argument.id;
				}

				var S_argums = JSON.stringify(argument);
				var editro_id = UniqID();
				
				output += '<div class="-fix-inputs-area -field-editors-append "'+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+' data-inp-name="'+InputName+'">';
					output += '<div class="-fix-forms-field-title"><h3>'+argument.title+'</h3></div>';

				  output += '<div class="LoadingEditors EditorProduct" data-input="'+argument.id+'">';
				  	output += '<div class="LoadingEditor">';
				  		output += '<div class="loadingHides">جاري تحميل المحرر .. برجاء الانتظار</div>';
				  		output += '<h2>جاري تحميل المحرر .. برجاء الانتظار</h2>';
				  	output += '</div>';

				  	//output += '<textarea id="ContentProducer_FieldEditor_'+editro_id+'" auto-editor="'+editro_id+'" value="'+argument.value+'" name="'+InputName+'" placeholder="'+argument.title+'" style="display:none">'+argument.value+'</textarea>';
					
				  output += '</div>';
				  output += ( ( argument.disc != undefined ) ) ? '<descor>'+argument.disc+'</descor>' : '';
				output += '</div>';
				return output;
			}

			function ContextField_CompoSelectField(argument,key=false,value){
				var output = '';
				argument.value = value;
				if( key != false ) argument.parent_key = key;

				var StartArguments = JSON.stringify(argument);
				
				output += '<div class="--loader--elements-selector" data-load-ajaxfiled="'+$.base64.btoa( StartArguments )+'"></div>';
				return output;
			}

			function ContextField_ModelSelector(argument,key=false,value){
				var output = '';
				argument.value = value;
				if( key != false ) argument.parent_key = key;

				var StartArguments = JSON.stringify(argument);
				
				output += '<div class="--loader--elements-selector" data-load-ajaxfiled="'+$.base64.btoa( StartArguments )+'"></div>';
				return output;
			}

			function ContextField_InsertDataBase(argument,key=false,value){
				var output = '';
				argument.value = value;
				if( key != false ) argument.parent_key = key;

				var StartArguments = JSON.stringify(argument);
				
				output += '<div class="--loader--elements-selector" data-load-ajaxfiled="'+$.base64.btoa( StartArguments )+'"></div>';
				return output;
			}
			function ContextField_SingleGroup(argument,key=false,value){
				var output = '';
				argument.value = value;
				if( key != false ) argument.parent_key = key;

				var StartArguments = JSON.stringify(argument);
				
				output += '<div class="--loader--elements-selector" data-load-ajaxfiled="'+$.base64.btoa( StartArguments )+'"></div>';
				return output;
			}

			function ContextField_Radio(argument,key=false,value) {
				output = '';
				var StartArguments = JSON.stringify(argument);

				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';
				}else{
					InputName = argument.id;
				}		
				var uni = UniqID();
				if( argument.LoadMoreAjax == undefined ) argument.LoadMoreAjax = false;
				if( argument.LoaMoreAttr == undefined ) argument.LoaMoreAttr = '';

				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output += '<div class="-fix-forms-field-title"><h3>'+argument.title+'</h3></div>';
					
					output += '<div class="-Radio-Inner-Box" '+( ( argument.options == undefined && (argument.type == 'Taxonomy-Radio' || argument.type == 'Posts-Radio') ) ? 'data-load-ajaxfiled="'+$.base64.btoa( StartArguments )+'"' : '' )+'>';
						if( argument.options != undefined ){
							if( argument.LoadMoreAjax == true ){
								output += '<div class="AjaxSearchCenter">';
									output += '<input type="text" value="" placeholder="البحث السريع " data-input-search-center="'+uni+'" data-appender-elem="-ScrollerCenter" data-arguments="'+argument.SearchArguments+'">';
								output += '</div>';
							}

							output += '<div class="-Radio-Box-InnerArea -ScrollerCenter" '+argument.LoaMoreAttr+' data-uniqid="'+uni+'">';

								$.each(argument.options ,function(fky,fvlue) {
									output += '<div class="-Radio-Box-Item" data-argums-say="'+fky+'"  data-curr-value-field="'+value+'">';
										output += '<input'+((fky == value) ? ' checked' : '')+' type="radio" value="'+fky+'" name="'+InputName+'" id="'+InputName+fky+'" />';
										output += '<span></span>';
										output += '<em>'+fvlue+'</em>';
									output += '</div>';
								});

								if( argument.Custom_says != undefined ) {
									output += '<div class="Says-Field-Argums">';
										output += '<div class="Sayes-fields-ShowsIn"><i class="fas fa-eye"></i><span>مثال على اختياري </span></div>';
										
										output += '<div class="-Sayes-Fields-Context" style="display:none">';
											// # DECODE FIELDS ARGUMENTS.
											var decodedArguments = $.base64.atob(SayFieldsSetup,true);
											decodedArguments = jQuery.parseJSON( decodedArguments );
											$.each(decodedArguments ,function(skey,meky) {
												
												output += '<div class="Says-Single-Field" data-say-id="'+skey+'" '+((skey != value) ? 'style="display:none;"' : '')+'>';
													meky.value = '';
													output += CreateFields(meky);
												output += '</div>';
											});
										output += '</div>';
									output += '</div>';
								}
							output += '</div>';

							output += '<LoadMore--InpuArea><PostsScrollLoader data-more-click="'+uni+'" class="PostsScrollLoader LoadMorePostsBTN" '+( ( argument.LoadMoreAjax != false ) ? '' : 'style="display:none"')+'><i class="fa-solid fa-square-plus"></i><span>تحميل المزيد</span></PostsScrollLoader></LoadMore--InpuArea>';
						}


					output += '</div>';
					output += ( ( argument.disc != undefined ) ) ? '<descor>'+argument.disc+'</descor>' : '';
				output += '</div>';
				/*if( argument.shows_selected != undefined && argument.show_create_fields != undefined ){
						$.each(argument.show_create_fields ,function(skey,meky) {
							var StartArguments = JSON.stringify(meky.shows_At);
							
							output += '<field-inserts class="Insert-SelectOptions" data-field-id="'+meky.id+'" data-id="'+$.base64.btoa( StartArguments )+'" '+( ( jQuery.inArray( value, meky.shows_At) !== -1 in_array($value, $v['shows_At']) ) ? '' : 'style="display:none"' )+'>';
								output += CreateFields(meky);
							output += '</field-inserts>';
						});
				}*/

				return output;
			}

			function ContextField_SwitchBox(argument,key=false,value) {
				var output = '';
				argument.value = value;

				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';
				}else{
					InputName = argument.id;
				}		

				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output += '<div class="-fix-forms-field-title"><h3>'+argument.title+'</h3></div>';
				
					output += '<SwitchField data-field="'+InputName+'">';
						output += '<input type="checkbox" '+(( value == true ) ? 'checked' : '')+' value="on" name="'+InputName+'" id="'+InputName+'YC_CheckBox" />';
						output += '<div class="Switch">';
							output += '<span>معطّل</span>';
							output += '<strong>مفعّل</strong>';
							output += '<em></em>';
						output += '</div>';
						output += '<SwitchName>'+((argument.disc != undefined) ? argument.disc : argument.title)+'</SwitchName>';
					output += '</SwitchField>';
				output += '</div>';
				return output;
			}

			function ContextField_TextArea(argument,key=false,value){
				var output = '';
				argument.value = value;

				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';
				}else{
					InputName = argument.id;
				}

				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output += '<div class="-fix-forms-field-title"><h3>'+argument.title+'</h3></div>';
					output += '<textarea style="height:80px" placeholder="'+argument.title+'" name="'+InputName+'" id="'+argument.id+'">'+value+'</textarea>';
					output += ( ( argument.disc != undefined ) ) ? '<descor>'+argument.disc+'</descor>' : '';
				output += '</div>';

				return output;
			}

			function ContextField_TextArea_Code(argument,key=false,value){
				var output = '';
				argument.value = value;

				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';
				}else{
					InputName = argument.id;
				}

				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output += '<div class="-fix-forms-field-title"><h3>'+argument.title+'</h3></div>';
					output += '<textarea class="CodePreview" style="height:80px" placeholder="'+argument.title+'" name="'+InputName+'" id="'+argument.id+'">'+value+'</textarea>';
					output += ( ( argument.disc != undefined ) ) ? '<descor>'+argument.disc+'</descor>' : '';
				output += '</div>';

				return output;
			}

			function ContextField_CheckBox(argument,key=false,value){
				var output = '';
				argument.value = value;
				var StartArguments = JSON.stringify(argument);
				var uni = UniqID();
				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';
				}else{
					InputName = argument.id;
				}

				if( argument.LoadMoreAjax == undefined ) argument.LoadMoreAjax = false;
				if( argument.LoaMoreAttr == undefined ) argument.LoaMoreAttr = '';

				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output += '<div class="-fix-forms-field-title"><h3>'+argument.title+'</h3></div>';
					
					output += '<div class="-CheckBox-Centers" '+( ( argument.options == undefined && (argument.type == 'Taxonomy-CheckBox' || argument.type == 'Posts-CheckBox') ) ? 'data-load-ajaxfiled="'+$.base64.btoa( StartArguments )+'"' : '' )+'>';
						if( argument.options != undefined ){
							if( argument.LoadMoreAjax == true ){
								output += '<div class="AjaxSearchCenter">';
									output += '<input type="text" value="" placeholder="البحث السريع " data-input-search-center="'+uni+'" data-appender-elem="-ScrollerCenter" data-arguments="'+argument.SearchArguments+'">';
								output += '</div>';
							}

							output += '<div class="-CheckBox-Box-InnerArea -ScrollerCenter" '+argument.LoaMoreAttr+' data-uniqid="'+uni+'">';
								$.each(argument.options ,function(fky,fvlue) {

									var Activable__Elem = false;
									$.each(value,function(e,mre) {
										if( mre == fky ) Activable__Elem = true;
									});

									output += '<div class="-CheckBox-Box-Item">';
										output += '<input'+( ( Activable__Elem == true ) ? ' checked' : '')+' type="checkbox" value="'+fky+'" name="'+InputName+'[]" id="'+InputName+fky+'" />';
										output += '<span></span>';
										output += '<em>'+fvlue+'</em>';
									output += '</div>';
								});
							output += '</div>';

							output += '<LoadMore--InpuArea><PostsScrollLoader data-more-click="'+uni+'" class="PostsScrollLoader LoadMorePostsBTN" '+( ( argument.LoadMoreAjax != false ) ? '' : 'style="display:none"')+'><i class="fa-solid fa-square-plus"></i><span>تحميل المزيد</span></PostsScrollLoader></LoadMore--InpuArea>';
						}
					output += '</div>';
					output += ( ( argument.disc != undefined ) ) ? '<descor>'+argument.disc+'</descor>' : '';
				output += '</div>';
				return output;
			}

			function ContextField_Select(argument,key=false,value){
				var output = '';
				argument.value = value;
				// #
				var StartArguments = JSON.stringify(argument);

				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';
				}else{
					InputName = argument.id;
				}

				if( argument.LoadMoreAjax == undefined ) argument.LoadMoreAjax = false;
				if( argument.LoaMoreAttr == undefined ) argument.LoaMoreAttr = '';

				var uni = UniqID();
				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output += '<div class="-fix-forms-field-title"><h3>'+argument.title+'</h3></div>';

					output += '<div class="Select-Options-Items" '+( ( argument.options == undefined && (argument.type == 'Taxonomy-Select' || argument.type == 'Posts-Select' || argument.type == 'SVG-Icon') ) ? 'data-load-ajaxfiled="'+$.base64.btoa( StartArguments )+'"' : '' )+'>';
						if( argument.options != undefined ){
							output += '<input type="text" name="'+InputName+'" id="'+InputName+'" value="'+value+'" style="display:none" class="Selected-Value">';
							output += '<h2 data-select-open="'+InputName+'">';
								output += '<span>'+(( value != '' ) ? argument.options[value] : 'بدون تحديد ')+'</span><i class="fas fa-angle-down"></i>';
							output += '</h2>';
							output += '<div class="-Select-DropDown">';
									if( argument.LoadMoreAjax == true ){
										output += '<div class="AjaxSearchCenter">';
											output += '<input type="text" value="" placeholder="البحث السريع " data-input-search-center="'+uni+'" data-appender-elem="-ScrollerCenter" data-arguments="'+argument.SearchArguments+'">';
										output += '</div>';
									}

									output += '<ul class="Lists-Select-Items -ScrollerCenter" '+argument.LoaMoreAttr+' data-uniqid="'+uni+'">';
										$.each(argument.options ,function(fky,fvlue) {
											output += '<li data-uniqid="'+uni+'" '+( ( argument.selected_shows != undefined ) ? 'data-shows-selected="'+fky+'" data-meta-key="'+argument.id+'"' : '' )+' '+( ( argument.create_fields != undefined ) ? 'data-argums-fields="'+ $.base64.btoa( JSON.stringify(argument.create_fields) )+'"' : '' )+' data-title="'+fvlue+'" data-selected="'+fky+'" '+((fky == value) ? 'class="active"' : '')+'>'+fvlue+'</li>';
										});
									output += '</ul>';

									output += '<LoadMore--InpuArea><PostsScrollLoader data-more-click="'+uni+'" class="PostsScrollLoader LoadMorePostsBTN" '+( ( argument.LoadMoreAjax != false ) ? '' : 'style="display:none"')+'><i class="fa-solid fa-square-plus"></i><span>تحميل المزيد</span></PostsScrollLoader></LoadMore--InpuArea>';
							output += '</div>';
						}
					output += '</div>';
					output += ( ( argument.disc != undefined ) ) ? '<descor>'+argument.disc+'</descor>' : '';
				output += '</div>';

				if( argument.create_hide_fields != undefined ){
					$.each(argument.create_hide_fields,function(ek,evalue) {
						output += '<div class="-Hide-Boxes-Shows Select-Hide-Insert"  data-uniqid="'+uni+'" data-meta-key="'+argument.id+'" data-show-type="'+ek+'" '+( ( value ==  ek) ? '' : 'style="display:none"' )+'>';
							output += '<div class="Title-MoreForms"><i class="fa-solid fa-sliders"></i><h2>'+argument.options[ek]+'</h2></div>';
							if( evalue['fields'] != undefined ){
								$.each( evalue['fields'],function(rk,refield) {
									if( argument.parent_id != undefined ){
										refield['parent_id'] = argument.parent_id;
									}
									output += CreateFields(refield);
								});
							}

						output += '</div>';
						
					});
				}

				return output;
			}

			function ContextField_Date(argument,key=false,value){
				var output = '';
				argument.value = value;

				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';
				}else{
					InputName = argument.id;
				}

				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output += '<div class="-fix-forms-field-title"><h3>'+argument.title+'</h3></div>';
					output += '<input type="text" data-language="en" class="DatePreview" data-time="'+( ( argument.time != undefined && argument.time == true ) ? 'true' : 'false')+'" data-format="'+( ( argument.format != undefined )  ? argument.format : 'd-m-Y H:i:s')+'" name="'+InputName+'" id="'+argument.id+'" value="'+argument.value+'" spellcheck="false" attrtype="text" attrname="'+argument.id+'">';
					output += ( ( argument.disc != undefined ) ) ? '<descor>'+argument.disc+'</descor>' : '';
				output += '</div>';
				return output;
			}

			function ContextField_File(argument,key=false,value){
				var output = '';
				argument.value = value;

				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';
				}else{
					InputName = argument.id;
				}

				var Id_Uniq = UniqID();

				// ## IMAGE ID GET // 
				var style='';
				if( isEmpty( value ) ) { style='style="display:none;"'; }

				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output += '<div class="-fix-forms-field-title"><h3>'+argument.title+'</h3></div>';

					output +='<div class="FieldUpload--FilesBox FieldUpload--'+argument.id+'">';
				    //
				    if(argument.multiple == true){
							if( value.length > 0 ) { style='style="display:none;"';}	
							
				    	output +='<a data-custom-uploader="true" href="javascript:void(0);" data-id="'+argument.id+'" data-multiple="'+(( argument.multiple == false || argument.multiple == 'false') ? 'false' : 'true')+'" data-type="'+argument.mime+'" data-field="'+argument.id+'" data-name="'+InputName+'" data-rlname="'+argument.title+'" class="CustomUploadYC YC--Uploads--'+argument.id+'"><i class="fa-solid fa-arrow-up"></i><span>'+argument.button+'</span></a>';
				      output +='<a '+style+' href="javascript:void(0);" class="CustomImage--RemoveButton" data-multiple="true" id="'+argument.id+'_remove">حذف الكل</a>';
				      output +='<div class="previewList '+argument.id+'_previewLists" id="'+argument.id+'_previewLists">';
				      	$.each(value ,function (k,url) {
				          output +='<div class="CustomImage--Boxed">';
				            output +='<input type="hidden" name="'+InputName+'['+k+']" value="'+url+'" data-exvalue="'+url+'" />';
				            output +='<div class="CustomImage--Preview">';
				              if(argument.mime == 'image'){
				                output +='<img src="'+url+'" />';
				              }else if(argument.mime == 'video/mp4'){
				                output +='<iframe src="'+url+'" autoplay="0" width="270" height="180"></iframe>';
				              }else if(argument.mime == 'audio/mpeg'){
				                output +='<audio controls>';
				                  output +='<source src="horse.ogg" type="audio/ogg">';
				                  output +='<source src="'+url+'" type="audio/mpeg">';
				                output +='</audio>';
				              }
				            output +='</div>';
				            output +='<em onClick="this.parent().remove();"><span></span><span></span></em>';
				          output +='</div>';
				      	});
				      output +='</div>';
				      //
				    }else{
				    	var Val_Empty = false;
							if( value.url == undefined || value.url != undefined && value.url == '' || value.id == undefined || value.id != undefined && value.id == '' ){ 
								style='style="display:none;"'; 
								Val_Empty= true;
								value.url = '';
								value.id = '';
							}

							var Id_InputName = InputName.replace( argument.id+']',argument.id+'_id]');
							
							if( argument.mime == undefined || argument.mime == '' ) argument.mime = 'image';

				      output +='<input type="hidden" id="CustomImage_'+argument.id+'_id" class="CustomImage_'+argument.id+'_id" name="'+Id_InputName+'"  value="'+value.id+'" data-exvalue="'+value.id+'"  />';
				      output +='<input type="text" value="'+value.url+'" name="'+InputName+'" id="CustomImage_'+argument.id+'" class="CustomImage_'+argument.id+'"  data-exvalue="'+value.url+'" />';
				      output +='<a href="javascript:void(0);" data-custom-uploader="true" data-id="'+argument.id+'" data-multiple="'+(( argument.multiple == undefined || argument.multiple == false || argument.multiple == 'false') ? 'false' : 'true')+'" data-type="'+argument.mime+'" data-field="'+argument.id+'" data-name="'+InputName+'" data-rlname="'+argument.title+'" class="CustomUploadYC YC--Uploads--'+argument.id+'"><i class="fa-solid fa-arrow-up"></i><span>'+(( argument.button != undefined && argument.button != '' ) ? argument.button : 'رفع الملف ')+'</span></a>';
				      output +='<a '+style+' href="javascript:void(0);" class="CustomImage--RemoveButton" data-multiple="false" id="CustomImage_'+argument.id+'_remove"><i class="fa-solid fa-xmark"></i><span>حذف</span></a>';
				      
				      output +='<div class="CustomImage--Preview CustomImage_'+argument.id+'_preview" id="CustomImage_'+argument.id+'_preview" '+style+'>';
				    		output +='<div class="CustomImage--Boxed">';
				          if(argument.mime == 'image'){
				            output +='<img '+style+' src="'+value.url+'" />';
				          }else if(argument.mime == 'video/mp4'){
				            output +='<iframe src="'+value.url+'" '+style+' autoplay="0" width="270" height="180"></iframe>';
				            if( value.duration == undefined ) value.duration = '';
				            output +='<input type="hidden" id="CustomImage_'+argument.id+'_duration" class="CustomImage_'+argument.id+'_duration" name="'+argument.id+'_duration"  value="'+value.duration+'" data-exvalue="'+value.duration+'"  />';
				          }else if(argument.mime == 'audio/mpeg'){
			              output +='<audio controls '+style+'>';
			                output +='<source src="horse.ogg" type="audio/ogg">';
			                output +='<source src="'+value.url+'" type="audio/mpeg">';
			              output +='</audio>';
				          }
				        output +='</div>';
				    	output +='</div>';
				    }
				  output +='</div>';
				  output += ( ( argument.disc != undefined ) ) ? '<descor>'+argument.disc+'</descor>' : '';
				output += '</div>';

				return output;
			}

			function ContextField_Color(argument,key=false,value) {
				var output = '';
				argument.value = value;

				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';

				}else{
					InputName = argument.id;
				}

				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output += '<div class="-fix-forms-field-title"><h3>'+argument.title+'</h3></div>';
					output += '<input type="text" value="'+value+'" placeholder="'+argument.title+'" name="'+InputName+'" id="'+InputName+'" class="ColorViewer">';
					output += ( ( argument.disc != undefined ) ) ? '<descor>'+argument.disc+'</descor>' : '';
				output += '</div>';
				return output;
			}

			function ContextField_GroupsField(argument,key=false,value){
				var output = '';
				argument.value = value;
				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';

				}else{
					InputName = argument.id;
				}

				UniqItems = UniqID();

				var S_argums = JSON.stringify(argument);
				
				output += '<div class="-fix-inputs-area" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					output +='<div class="InputsAppender--Fields-BoxArea -FieldTo-'+argument.id+'">';
						output +='<div class="Title-MoreForms"><i class="fa-solid fa-store"></i><h2>'+argument.title+'</h2></div>';
						
						output +='<div class="-Create-More-Fields">';
							output +='<div class="Title-MoreForms"><i class="fa-solid fa-plus"></i><h2>إضافة عنصر جديد </h2></div>';
							output +='<div class="-Insert-Fields-Tool">';
								output +='<div class="Insert-Setp-InForm" data-uniq-item="'+UniqItems+'" data-title="لقد قُمت بتحديد  _Counter_  عنصر الى المميزات حتي الى الان ">';

									$.each( argument.fields,function(k,v) {
										v['parent_id'] = InputName;
										v['InsertElements'] = true;
										output += CreateFields(v);

										if( v.shows_selected != undefined && v.show_create_fields != undefined ){
												$.each(v.show_create_fields ,function(skey,meky) {
													meky['parent_id'] = InputName;
													meky['InsertElements'] = true;
													var StartArguments = JSON.stringify(meky.shows_At);										
													output += '<field-inserts class="Insert-SelectOptions" data-field-id="'+meky.id+'" data-id="'+$.base64.btoa( StartArguments )+'" '+( ( jQuery.inArray( value, meky.shows_At) !== -1 ) ? '' : 'style="display:none"' )+'>';
														output += CreateFields(meky);
													output += '</field-inserts>';
												});
										}

									});

								output +='</div>';
								output +='<div class="-row-create-button"><div class="-Insert-Form-Item" data-item-id="'+UniqItems+'" data-keys-argums="'+EmptyKeysValues+'"><i class="fa-solid fa-plus"></i><span>إنشاء عنصر جديد </span></div></div>';

								output +='<div class="-Fields-Insert-Area"></div>';
							output +='</div>';
						output +='</div>';
					output +='</div>';
				output +='</div>';

				return output;
			}

			function ContextField_DuplicateGroup(argument,key=false,value){
				var output = '';
				argument.value = value;
				var InputName;
				if( argument.InsertElements != undefined && argument.InsertElements != false){
					argument.InsertElements = false;
					InputName = 'Insert_'+argument.id;
					
				}else if( key == false && argument.parent_id != undefined){
					InputName = argument.parent_id+'['+argument.id+']';

				}else if( argument.parent_id != undefined ){
					InputName = argument.parent_id+'['+key+']['+argument.id+']';

				}else{
					InputName = argument.id;
				}

				UniqItems = UniqID();

				var S_argums = JSON.stringify(argument);

				
				output += '<div class="-DuplicateGroup-widgets" '+( ( argument.parent_id != undefined ) ? 'data-field-argums="'+$.base64.btoa( S_argums )+'" ' : 'data-vars="'+$.base64.btoa( S_argums )+'"' )+'>';
					
					output += '<div class="Title-MoreForms-Dublicate" '+( ( argument.value.length > 1 ) ? '' : 'style="display:none;"' )+'>';
						output += '<i class="fa-solid fa-sitemap"></i>';
						output += '<h2>لقد قُمت بتحديد <count-items>'+argument.value.length+'</count-items> عنصر حتي الأن </em></h2>';
						output += '<div class="Remove-Dublicate-GroupField" data-remove-itemsgroup="group-item" data-tooltip="حذف كل العناصر "><i class="fa-solid fa-trash-can"></i></div>';
					output += '</div>';

					var UniqIs = UniqID();
					output += '<div class="-Fields-Insert-DuplicateGroup">';
						output += '<div class="-Revilotion-Fields-Dublicate" data-dublicate-group-item="'+UniqIs+'">';
							output += '<div class="Title-MoreForms-Dublicate"><i class="fa-solid fa-pen-to-square"></i><h2>الشريحة  <em>['+UniqIs+']</em></h2></div>';

							$.each( argument.fields,function(k,v) {
								v['parent_id'] = InputName+'['+UniqIs+']';
								output += CreateFields(v);
							});

						output += '</div>';
					output += '</div>';

					output += '<div class="-Dublicate-create-button"><div class="-Insert-Dublicate-Item" data-dublicateitem-id="'+UniqItems+'" data-keys-argums="'+EmptyKeysValues+'"><i class="fa-solid fa-plus"></i><span>إنشاء عنصر جديد </span></div></div>';

				output += '</div>';

				return output;
			}
	// # FIELDS SETUP.

		$(".apbsortable").each(function(e,aps) {
			var $list = $(aps);
			var handle = $list.attr('data-connect-with') || $list.data('connectWith') || 'sortbyme, .-widget-item-title-';
			if ( $.fn.sortable ) {
				try { if ( $list.data('ui-sortable') ) { $list.sortable('destroy'); } } catch (err) {}
				$list.sortable({
					handle: handle,
					cursor: 'grabbing',
					tolerance: 'pointer',
					placeholder: 'kayan-sort-placeholder',
					forcePlaceholderSize: true,
					opacity: 0.92,
					axis: 'y'
				});
			}
		});
		if ( typeof window.kayanInitSortables === 'function' ) {
			window.kayanInitSortables($);
		}

		var EditorsLists = [];
		function GetFieldValue(elem,argument){
			if( argument.type == 'Text' ){

				// # FIELD CHECK.
				var CurrentValue = $(elem).find('input').val();
				if( isEmpty( CurrentValue ) ) {
					return '';
				}
				return CurrentValue;
			}else if( argument.type == 'Number' ){
				// # FIELD CHECK.
				var CurrentValue = $(elem).find('input').val();
				if( isEmpty( CurrentValue ) ) {
					return '';
				}
				return CurrentValue;
			}else if( argument.type == 'TextArea' ){

				// # FIELD CHECK.
				var CurrentValue = $(elem).find('textarea').val();
				if( isEmpty( CurrentValue ) ) {
					return '';
				}
				return CurrentValue;
				
			}else if( argument.type == 'Editor' ){

				// # FIELD CHECK.
				var TextArea_element = $(elem).find('.wp-editor-area');
				var editor = TextArea_element.attr("id");

				var CurrentValue = tinyMCE.get(editor).getContent();
				if( isEmpty( CurrentValue ) ) {
					return '';
				}
				return CurrentValue;

			}else if( argument.type == 'TextArea_Code' ){
				// # FIELD CHECK.
					var CurrentValue = $(elem).find('.CodeMirror')[0].CodeMirror.getValue();
				if( isEmpty( CurrentValue ) ) {
					return '';
				}				
				return CurrentValue;

			}else if( argument.type == 'Radio' || argument.type == 'Taxonomy-Radio' || argument.type == 'Posts-Radio' || argument.type == 'Users-Radio' ){

				// # FIELD CHECK.
				var CurrentValue = '';
				$(elem).find('input[type="radio"]').each( function(re,inp) {
					if( $(inp).prop('checked') && CurrentValue == '' ){
						CurrentValue = $(inp).attr('value');
					}
				});

				if( isEmpty( CurrentValue ) ) {
					return '';
				}
				return CurrentValue;

			}else if( argument.type == 'SwitchBox' ){

				// # FIELD CHECK.
				var CurrentValue = ( ( $(elem).find('input').is(":checked") ) ) ? true : false;
				return CurrentValue;

			}else if( argument.type == 'CheckBox' || argument.type == 'Taxonomy-CheckBox' || argument.type == 'Posts-CheckBox' || argument.type == 'Users-CheckBox' ){

				// # FIELD CHECK.
				var CurrentValue = {};
				$(elem).find('.-CheckBox-Box-Item').each( function(re,bxitem) {
					$(bxitem).find('input[type="checkbox"]').each( function(ww,inp) {
						if( $(inp).is(":checked") ){
							CurrentValue[re] = $(inp).attr('value');
						}
					});
					
				});

				if( isEmpty( CurrentValue ) ) {
					return CurrentValue;
				}
				return CurrentValue;

			}else if( argument.type == 'Select' || argument.type == 'Taxonomy-Select' || argument.type == 'Posts-Select' || argument.type == 'Users-Select' || argument.type == 'SVG-Icon' || argument.type == 'CuntrySelect' ){
				// # FIELD CHECK.
				var CurrentValue = $(elem).find('.Selected-Value').val();
				if( isEmpty( CurrentValue ) ) {
					return '';
				}
				return CurrentValue;

			}else if( argument.type == 'Select-Icon' ){
				// # FIELD CHECK.
				var CurrentValue = $(elem).find('.Selected-Value').val();
				if( isEmpty( CurrentValue ) ) {
					return '';
				}
				return CurrentValue;

			}else if( argument.type == 'Date' ){

				// # FIELD CHECK.
				var CurrentValue = $(elem).find('input').val();
				if( isEmpty( CurrentValue ) ) {
					return '';
				}

				return CurrentValue;

			}else if( argument.type == 'File' ){

				// # FIELD CHECK.
				var CurrentValue = {};
				if( argument.multiple == true ){
					$(elem).find('.CustomImage--Boxed').each(function(e,r) {
						var file_id = $(r).find('input[type="hidden"]').data('exvalue');
						var file_url = $(r).find('input[type="text"]').val();
						if( !isEmpty( file_id ) && !isEmpty( file_url ) ){
							CurrentValue[file_id] = file_url;
						}
						$(r).remove();
					});

					if( isEmpty( CurrentValue ) ) {
						return CurrentValue;						
					}
				}else{			
					if( $(elem).find('input.CustomImage_'+argument.id).length > 0 ){
						var YC__image_URL = $(elem).find('input.CustomImage_'+argument.id).val();
						CurrentValue.url = YC__image_URL;
					}

					if( $(elem).find('input.CustomImage_'+argument.id+'_id').length > 0 ){
						var YC__image_ID = $(elem).find('input.CustomImage_'+argument.id+'_id').val();
						CurrentValue.id = YC__image_ID;
					}

					if( $(elem).find('input.CustomImage_'+argument.id+'_duration').length > 0 ){
						var YC__image_Dur = $(elem).find('input.CustomImage_'+argument.id+'_duration').val();
						CurrentValue.duration = YC__image_Dur;
					}				

					if( isEmpty( CurrentValue['id'] ) || isEmpty( CurrentValue['url'] ) ) {
						return CurrentValue;
					}
				}
				return CurrentValue;

			}else if( argument.type == 'PacksColor' ){

				// # FIELD CHECK.
				var CurrentValue = {};
				$(elem).find('input[type="text"]').each( function(re,inp) {
					if( !isEmpty( $(inp).var() ) ){
						CurrentValue[re] = $(inp).vale();
					}
				});

				if( isEmpty( CurrentValue ) ) {
					return CurrentValue;					
				}

				return CurrentValue;

			}else if( argument.type == 'Color' ){

				// # FIELD CHECK.
				var CurrentValue = $(elem).find('input').val();
				if( isEmpty( CurrentValue ) ) {
					return CurrentValue;
				}
				return CurrentValue;
			}else if( argument.type == 'Title' ){
				CurrentValue = '';
				return CurrentValue;
			}else if( argument.type == 'Compo-Select-Field' ){

				// # FIELD CHECK.
				var CurrentValue = {};
				var Selectors__items__center = $(elem).find('.-scroller-slider-findors-UL');
				Selectors__items__center.find('[data-select-custom-items]').each( function(re,bxitem) {
					var Selector__input = $(bxitem).parent().find('input[type="hidden"]');
					CurrentValue[re] = Selector__input.attr('value');					
				});
				if( isEmpty( CurrentValue ) ) {
					return CurrentValue;
				}
				return CurrentValue;
			}

		}

		function SetupFields(elem,curkeys,empty=false,unsetinsert=false) {
			// # FUNCTION COMMENTS.
				// # empty :: EMPTY THIS FIELD.
			// ##	//

			if( $(elem).data('field-argums') != undefined ){
				var ArgumsField = $(elem).data('field-argums');
			}else{
				var ArgumsField = $(elem).data('vars');
			}
			// # DECODE FIELDS ARGUMENTS.
			var decodedArguments = $.base64.atob(ArgumsField,true);
			decodedArguments = jQuery.parseJSON( decodedArguments );

			if( unsetinsert != false ) delete decodedArguments.InsertElements;

			if( empty == false ){
				var CurrentValue =  GetFieldValue(elem,decodedArguments);
				if( CurrentValue == false &&  decodedArguments.require != undefined && decodedArguments.require == true ){
					return false;
				}
			}

			// # SETUPFIELDS 
			if( decodedArguments.type == 'Text' ){

				if( empty == true ) $(elem).find('input').val('');
				return ( ( empty == false ) ) ?  ContextField_Text(decodedArguments,curkeys,CurrentValue) : '';
			}else if( decodedArguments.type == 'Title' ){
				CurrentValue = '';
				return ( ( empty == false ) ) ?  ContextField_Title(decodedArguments,curkeys,CurrentValue) : '';
			}else if( decodedArguments.type == 'Number' ){

				if( empty == true ) $(elem).find('input').val('');
				return ( ( empty == false ) ) ?  ContextField_Number(decodedArguments,curkeys,CurrentValue) : '';
				
			}else if( decodedArguments.type == 'TextArea' ){

				if( empty == true ) $(elem).find('textarea').val('');
				return ( ( empty == false ) ) ?  ContextField_TextArea(decodedArguments,curkeys,CurrentValue) : '';

			}else if( decodedArguments.type == 'Editor' ){

				//if( empty == true ) $(elem).find('textarea').val('');
				return ( ( empty == false ) ) ?  ContextField_Editor(decodedArguments,curkeys,CurrentValue) : '';

			}else if( decodedArguments.type == 'TextArea_Code' ){

				if( empty == true ) $(elem).find('textarea').val('');
				return ( ( empty == false ) ) ?  ContextField_TextArea_Code(decodedArguments,curkeys,CurrentValue) : '';
			
			}else if( decodedArguments.type == 'Radio' || decodedArguments.type == 'Taxonomy-Radio' || decodedArguments.type == 'Posts-Radio' || decodedArguments.type == 'Users-Radio' ){

				return ( ( empty == false ) ) ?  ContextField_Radio(decodedArguments,curkeys,CurrentValue) : '';

			}else if( decodedArguments.type == 'SwitchBox' ){

				return ( ( empty == false ) ) ?  ContextField_SwitchBox(decodedArguments,curkeys,CurrentValue) : '';

			}else if( decodedArguments.type == 'CheckBox' || decodedArguments.type == 'Taxonomy-CheckBox' || decodedArguments.type == 'Posts-CheckBox' || decodedArguments.type == 'Users-CheckBox' ){

				// # FIELD CHECK.
				if( empty == true ){
					var CurrentValue = {};
					$(elem).find('.-CheckBox-Box-Item').each( function(re,bxitem) {
						$(bxitem).find('input[type="checkbox"]').each( function(ww,inp) {
							if( $(inp).is(":checked") ){
								$(inp).removeAttr("checked");								
							}
						});	
					});
				}


				return ( ( empty == false ) ) ?  ContextField_CheckBox(decodedArguments,curkeys,CurrentValue) : '';

			}else if( decodedArguments.type == 'Select' || decodedArguments.type == 'Taxonomy-Select' || decodedArguments.type == 'Posts-Select' || decodedArguments.type == 'Users-Select' || decodedArguments.type == 'SVG-Icon' ){

				return ( ( empty == false ) ) ?  ContextField_Select(decodedArguments,curkeys,CurrentValue) : '';	

			}else if( decodedArguments.type == 'Date' ){

				if( empty == true ) $(elem).find('input').val('');
				return ( ( empty == false ) ) ?  ContextField_Date(decodedArguments,curkeys,CurrentValue) : '';

			}else if( decodedArguments.type == 'File' ){

				if( empty == true ) {
					if( $(elem).find('input.CustomImage_'+decodedArguments.id).length > 0 ) $(elem).find('input.CustomImage_'+decodedArguments.id).val('');
					if( $(elem).find('input.CustomImage_'+decodedArguments.id+'_id').length > 0 ) $(elem).find('input.CustomImage_'+decodedArguments.id+'_id').val('');
					if( $(elem).find('input.CustomImage_'+decodedArguments.id+'_duration').length > 0 ) $(elem).find('input.CustomImage_'+decodedArguments.id+'_duration').val('');

					if( $(elem).find('img').length > 0 ) $(elem).find('img').attr('src','');
					if( $(elem).find('iframe').length > 0 ) $(elem).find('iframe').attr('src','');
					if( $(elem).find('audio').length > 0 ) $(elem).find('audio').remove();
					if( $(elem).find('.CustomImage--RemoveButton').length > 0 ) $(elem).find('.CustomImage--RemoveButton').hide();
					if( $(elem).find('.CustomImage--Preview').length > 0 ) $(elem).find('.CustomImage--Preview').hide();
				}
				return ( ( empty == false ) ) ? ContextField_File(decodedArguments,curkeys,CurrentValue) : '';

			}else if( decodedArguments.type == 'Color' ){

				if( empty == true ) $(elem).find('input').val('');
				return ( ( empty == false ) ) ?  ContextField_Color(decodedArguments,curkeys,CurrentValue) : '';

			}else if( decodedArguments.type == 'Compo-Select-Field' ){

				if( empty == true ) {
					$(elem).find('.--JS--Appended--Selector').html('');
					$(elem).find('.--JS-result--select').hide();
				}
				return ( ( empty == false ) ) ?  ContextField_CompoSelectField(decodedArguments,curkeys,CurrentValue) : '';

			}
			return false;
		}

		function CheckFieldsValue(elem){
			// # DECODE FIELDS ARGUMENTS.
			var decodedArguments = atob($(elem).data('vars'));
			decodedArguments = jQuery.parseJSON( decodedArguments );
			// # SETUPFIELDS 
			if( decodedArguments.type == 'Text' || decodedArguments.type == 'Date' || decodedArguments.type == 'Color' || decodedArguments.type == 'Number' ){

				// # FIELD CHECK.
				var CurrentValue = $(elem).find('input').val();			
				if( isEmpty( CurrentValue ) ) {
					if( decodedArguments.require != undefined && decodedArguments.require == true ){
						return false;
					}
				}

			}else if( decodedArguments.type == 'Title' ){
				return true;
			}else if( decodedArguments.type == 'TextArea' ){

				// # FIELD CHECK.
				var CurrentValue = $(elem).find('textarea').val();
				if( isEmpty( CurrentValue ) ) {
					if( decodedArguments.require != undefined && decodedArguments.require == true ){
						return false;
					}
				}

			}else if(decodedArguments.type == 'TextArea_Code' ){
				// # FIELD CHECK.
				var CurrentValue = $(elem).find('.CodeMirror')[0].CodeMirror.getValue();

				if( isEmpty( CurrentValue ) ) {
					if( decodedArguments.require != undefined && decodedArguments.require == true ){
						return false;
					}
				}

			}else if( decodedArguments.type == 'Editor'){

				// # FIELD CHECK.
				var TextArea_element = $(elem).find('.wp-editor-area');
				var editor = TextArea_element.attr("id");

				var CurrentValue = tinyMCE.get(editor).getContent();
				if( isEmpty( CurrentValue ) ) {
					if( decodedArguments.require != undefined && decodedArguments.require == true ){
						return false;
					}
				}

			}else if( decodedArguments.type == 'Radio' ||  decodedArguments.type == 'Taxonomy-Radio' || decodedArguments.type == 'Posts-Radio' || decodedArguments.type == 'Users-Radio' ){

				// # FIELD CHECK.
				var CurrentValue = '';
				$(elem).find('input[type="radio"]').each( function(re,inp) {
					if( $(inp).prop('checked') && CurrentValue == '' ){
						CurrentValue = $(inp).attr('value');
					}
				});

				if( isEmpty( CurrentValue ) ) {
					if( decodedArguments.require != undefined && decodedArguments.require == true ){
						return false;
					}
				}

			}else if( decodedArguments.type == 'SwitchBox' ){
				// # FIELD CHECK.
				var CurrentValue = ( ( $(elem).find('input').is(":checked") ) ) ? true : false;
				return true;
			}else if( decodedArguments.type == 'CheckBox' || decodedArguments.type == 'Taxonomy-CheckBox' || decodedArguments.type == 'Posts-CheckBox' || decodedArguments.type == 'Users-CheckBox' ){

				// # FIELD CHECK.
				var CurrentValue = {};
				var ArrCheck = [];
				$(elem).find('input[type="checkbox"]').each( function(re,inp) {
					if( $(inp).is(":checked") ){
						CurrentValue[re] = $(inp).attr('value');
						ArrCheck.push($(inp).attr('value'));
					}
				});
				if( ArrCheck.length == 0  ) {
					if( decodedArguments.require != undefined && decodedArguments.require == true ){
						return false;
					}
				}

			}else if( decodedArguments.type == 'Compo-Select-Field' ){

				// # FIELD CHECK.
				var CurrentValue = {};
				var ArrCheck = [];
				var Selectors__items__center = $(elem).find('.-scroller-slider-findors-UL');
				Selectors__items__center.find('[data-select-custom-items]').each( function(re,inp) {
					var Inp__item = $(inp).parent().find('input[type="hidden"]');
					CurrentValue[re] = Inp__item.attr('value');
					ArrCheck.push(Inp__item.attr('value'));					
				});
				if( ArrCheck.length == 0  ) {
					if( decodedArguments.require != undefined && decodedArguments.require == true ){
						return false;
					}
				}

			}else if( decodedArguments.type == 'Select' ||  decodedArguments.type == 'Taxonomy-Select' ||  decodedArguments.type == 'Posts-Select' || decodedArguments.type == 'Users-Select' || decodedArguments.type == 'SVG-Icon' || decodedArguments.type == 'CuntrySelect' ){
				// # FIELD CHECK.
				var CurrentValue = $(elem).find('.Selected-Value').val();
				if( isEmpty( CurrentValue ) ) {
					if( decodedArguments.require != undefined && decodedArguments.require == true ){
						return false;
					}
				}

			}else if( decodedArguments.type == 'File' ){

				// # FIELD CHECK.
				var CurrentValue = {};
				if( decodedArguments.multiple == true ){
					$(elem).find('.CustomImage--Boxed').each(function(e,r) {
						var file_id = $(r).find('input[type="hidden"]').data('exvalue');
						var file_url = $(r).find('input[type="hidden"]').val();
						if( !isEmpty( file_id ) && !isEmpty( file_url ) ){
							CurrentValue[file_id] = file_url;
						}
						$(r).remove();
					});

					if( isEmpty( CurrentValue ) ) {
						if( decodedArguments.require != undefined && decodedArguments.require == true ){
							return false;
						}
					}
				}else{			
					if( $(elem).find('input.CustomImage_'+decodedArguments.id).length > 0 ){
						CurrentValue['url'] = $(elem).find('input.CustomImage_'+decodedArguments.id).val();
					}

					if( $(elem).find('input.CustomImage_'+decodedArguments.id+'_id').length > 0 ){
						CurrentValue['id'] = $(elem).find('input.CustomImage_'+decodedArguments.id+'_id').val();
					}

					if( $(elem).find('input.CustomImage_'+decodedArguments.id+'_duration').length > 0 ){
						CurrentValue['duration'] = $(elem).find('input.CustomImage_'+decodedArguments.id+'_duration').val();
					}				

					if( isEmpty( CurrentValue['id'] ) || isEmpty( CurrentValue['url'] ) ) {
						if( decodedArguments.require != undefined && decodedArguments.require == true ){
							return false;
						}
					}
				}
			}
			return true;
		}

		function CreateFields(field) {

			curkeys = false;
			// # SETUPFIELDS 
			if( field.type == 'Text' ){

				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = '';
				CurrentValue = field.value;

				return ContextField_Text(field,curkeys,CurrentValue);
			}else if( field.type == 'Number' ){

				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = '';
				CurrentValue = field.value;

				return ContextField_Number(field,curkeys,CurrentValue);
				
			}else if( field.type == 'Title' ){
				if( field.value == undefined ) field.value = '';
				CurrentValue = field.value;
				return ContextField_Title(field,curkeys,CurrentValue);

			}else if( field.type == 'TextArea' ){

				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = '';
				CurrentValue = field.value;

				return ContextField_TextArea(field,curkeys,CurrentValue);

			}else if( field.type == 'Radio' || field.type == 'Taxonomy-Radio' || field.type == 'Posts-Radio' || field.type == 'Users-Radio' ){

				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = '';
				CurrentValue = field.value;

				return ContextField_Radio(field,curkeys,CurrentValue);

			}else if( field.type == 'SwitchBox' ){
				
				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = '';
				CurrentValue = field.value;

				return ContextField_SwitchBox(field,curkeys,CurrentValue);

			}else if( field.type == 'CheckBox' || field.type == 'Taxonomy-CheckBox' || field.type == 'Posts-CheckBox' || field.type == 'Users-CheckBox' ){

				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = {};
				CurrentValue = field.value;

				return ContextField_CheckBox(field,curkeys,CurrentValue);

			}else if( field.type == 'Select' || field.type == 'Taxonomy-Select' || field.type == 'Posts-Select' || field.type == 'Users-Select' || field.type == 'SVG-Icon' ){

				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = '';
				CurrentValue = field.value;
				
				return ContextField_Select(field,curkeys,CurrentValue);	

			}else if( field.type == 'Date' ){

				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = '';
				CurrentValue = field.value;
				
				return ContextField_Date(field,curkeys,CurrentValue);

			}else if( field.type == 'File' ){

				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = {};
				CurrentValue = field.value;

				return ContextField_File(field,curkeys,CurrentValue);
					
			}else if( field.type == 'Color' ){
				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = '';
				CurrentValue = field.value;

				return ContextField_Color(field,curkeys,CurrentValue);
			}else if( field.type == 'TextArea_Code' ){

				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = '';
				CurrentValue = field.value;

				return ContextField_TextArea_Code(field,curkeys,CurrentValue);

			}else if( field.type == 'Editor' ){

				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = '';
				CurrentValue = field.value;

				return ContextField_Editor(field,curkeys,CurrentValue);

			}else if( field.type == 'GroupsField' ){

				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = {};
				CurrentValue = field.value;

				return ContextField_GroupsField(field,curkeys,CurrentValue);

			}else if( field.type == 'DuplicateGroup' ){
				// # VALUE CHECK. 
				if( field.value == undefined ) field.value = {};
				CurrentValue = field.value;

				return ContextField_DuplicateGroup(field,curkeys,CurrentValue);
			}else if( field.type == 'Compo-Select-Field' ){
				if( field.value == undefined ) field.value = {};
				CurrentValue = field.value;

				return ContextField_CompoSelectField(field,curkeys,CurrentValue);

			}else if( field.type == 'Models-Selector' ){
				if( field.value == undefined ) field.value = {};
				CurrentValue = field.value;

				return ContextField_ModelSelector(field,curkeys,CurrentValue);

			}else if( field.type == 'InsertDataBase' ){
				if( field.value == undefined ) field.value = {};
				CurrentValue = field.value;

				return ContextField_InsertDataBase(field,curkeys,CurrentValue);
			}else if( field.type == 'SingleGroup' ){
				if( field.value == undefined ) field.value = {};
				CurrentValue = field.value;

				return ContextField_SingleGroup(field,curkeys,CurrentValue);

			}
		}

		var StopInsert = true;
		function EachingFields(currelem,key){
			var NewBoxes = {};
			var ExclodeIDS = {};
			currelem.find('.-fix-inputs-area').each(function(k,el){
				if( $(el).data('field-argums') != undefined ){
					ElEach = SetupFields($(el),key,false,true);
					if( ElEach == false ){
						StopInsert = false;
						if( $(el).find('necessary').length == 0 ){
							$(el).append('<necessary>هذا الحقل مطلوب</necessary>');
						}
					}

					// # DECODE FIELDS ARGUMENTS.
					if( $(el).data('field-argums') != undefined ){
						var decodedArguments = atob($(el).data('field-argums'));
					}
					decodedArguments = jQuery.parseJSON( decodedArguments );
					var ArgsID = decodedArguments.id;

					// # APPENDED DATA
					if( ElEach != false && ExclodeIDS[ArgsID] == undefined ) {
						$(el).find('necessary').remove();
						// #
						NewBoxes[ArgsID] = ElEach;
						// #
						if( decodedArguments.show_create_fields != undefined ){
							var ShowsInValue = GetFieldValue($(el),decodedArguments);

							$.each( decodedArguments.show_create_fields,function (f,refield) {
								if( currelem.find('.Insert-SelectOptions[data-field-id="'+refield.id+'"]').length > 0 ){
									var NewEelem =  currelem.find('.Insert-SelectOptions[data-field-id="'+refield.id+'"]');
									var eaeech = EachingFields(NewEelem,key);
									$.each( eaeech ,function (r,retext) {
										var S_argums = JSON.stringify(refield.shows_At);
										NewBoxes[refield.id] = '<field-inserts class="Insert-SelectOptions" data-field-id="'+refield.id+'" data-id="'+$.base64.btoa( S_argums+'' )+'" '+( ( jQuery.inArray(ShowsInValue, refield.shows_At) !== -1  ) ? '' : 'style="display:none"' )+'>'+retext+'</field-inserts>';
										ExclodeIDS[refield.id] = refield.id;
									});
								}
							});
						}
					}
				}
			});
			return NewBoxes;
		}

	// # CUSTOM FIELDS EDITS ACTIONS.

		// # EDITOR FIELD SETUP

			var AjaxEditro = false;
			function EditorActions() {

				if( $("[auto-editor]").length > 0 ) {
					$("[auto-editor]").each(function(t,ed) {
						if( $(ed).data('loaded-editors') == undefined ){
							var FieldID = $(ed).attr("auto-editor");
							quicktags({id : FieldID});
							// use wordpress settings
							tinymce.init({
								selector: FieldID,
							  init_instance_callback: function (editor) {
							    editor.on('Change Paste	KeyUp KeyPress KeyDown', function (e) {
							      InputsChanged( $(ed) );
							      $(ed).find('.wp-editor-area').val(editor.getContent());

							    });
							  }
							});
							// this is needed for the editor to initiate
							var EditorVar = tinyMCE.execCommand('mceAddEditor', false, FieldID);
							//console.log(FieldID);
							$(ed).data("loaded-editors",'true');
							$(ed).closest('.EditorProduct').removeClass('LoadingEditors');
						}
					});
				}

				i=0;
				$('.-fix-inputs-area.-field-editors-append').each(function(e,apb) {i++;
					if( i == 1 && AjaxEditro == false ){ AjaxEditro = true;
						var data = {
							'action':'SetupEditor',
							'InputName':$(apb).data('inp-name'),
							'UniqID':UniqID(),
						};
						if( $(apb).data('field-argums') != undefined ){
							data['argums'] = $(apb).data('field-argums');
						}else if( $(apb).data('vars') != undefined ){
							data['argums'] = $(apb).data('vars');
						}
						var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';

						$.ajax({
							url: AjaxURL,
							dataType: 'json',
							type: 'POST',
							data:data,
							success: function(msg) {
								AjaxEditro = false;
								$(apb).removeClass('-field-editors-append');
								$(apb).find('.EditorProduct').append(msg.output);

								setTimeout(function() {
									EditorActions();									
								},300);
							}
						});
					}
				});
			}
			EditorActions();

			var AjaxFieldsR = false;
			function AjaxFieldsLoaded() {
				var i = 0;
				$('[data-load-ajaxfiled]').each(function(k,elem) {i++;
					if( i == 1 && AjaxFieldsR == false ){
						AjaxFieldsR = true;
						var Argums = $(elem).data('load-ajaxfiled');

						var data = {
							'action':'LoadedFields',
							'Argums':Argums,
						};
						var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
						AjaxRequest({
							url: AjaxURL,
							dataType: 'json',
							type : "POST",
							data: data,
							success: function(msg) {
								AjaxFieldsR = false;
								$(elem).append(msg.output);
								$(elem).removeAttr('data-load-ajaxfiled');
								setTimeout(function() {
									AjaxFieldsLoaded();
								},500);

							}
						});
					}
					
				});
			}


		  function InputsChanged(elem) {
		  	// ## widget-fields-appender
		  	if( $(elem).closest('.widget-fields-appender').length > 0 ){
			  	var UniqKey = $(elem).closest('.widget-fields-appender').data('uniq');
			  	$('.-YC-widget-action-button[data-un-key="'+UniqKey+'"]').removeClass('disable');
		  	}
		  }


			function TeaxtAreaCode__Change() {
				for (var i = 0; i < CodePreviewList.length; i++) {
					if( CodePreviewList[i] != undefined ){
						CodePreviewList[i].on("change",function(cm,change){
							InputsChanged( $(cm.display.wrapper) );
							$(cm.display.wrapper).parent().find('.CodePreview').val( cm.doc.getValue() );
						});
					}
				}
			}

			function InitializeFields() {
				TeaxtAreaCode__Change();
				EditorActions();
				CuntryesLoaded();
				YcSliderEvents();
				setTimeout(function() {
					AjaxFieldsLoaded();
				},500);
			}

		// # THEME UPLOADER ACTION .	
			$('body').on("click",'[data-custom-uploader="true"]',function(e) {
				var field = $(this).data('field');
				var name = $(this).data('name');
				var datamultiple = $(this).data('multiple');
				var FieldType = $(this).data('type')
				var MasterElement = $(this).closest('.FieldUpload--FilesBox');
		 		e.preventDefault();
				var image_frame='';
				if(image_frame){
				 image_frame.open();
				}
		   	// Define image_frame as wp.media object
		   	image_frame = wp.media({
					title: $(this).data('rlname'),
					multiple : $(this).data('multiple'),
					library : {
						type : $(this).data('type'),
					}
		   	});
				image_frame.on('close',function() {
				  // On close, get selections and save to the hidden input
				  // plus other AJAX stuff to refresh the image preview
				  var selection =  image_frame.state().get('selection');
				  var gallery_ids = new Array();
				  var my_index = 0;
		     	var DataArgums = new FormData();

					if( datamultiple == true ) {
						selection.each(function(attachment) {
			 				var attachment = attachment.toJSON();
			 				gallery_ids[my_index] = attachment.id;
			 				//
			 				var SingleField = '<div class="CustomImage--Boxed">';
		            SingleField += '<input type="hidden" name="'+name+'['+attachment.id+']" value="'+attachment.url+'" data-exvalue="'+attachment.id+'" />';
		            SingleField += '<div class="CustomImage--Preview">';
		            	if(FieldType == 'image'){
		              	SingleField += '<img src="'+attachment.url+'" />';
		            	}else if(FieldType == 'video/mp4'){
		              	SingleField += '<iframe src="'+attachment.url+'" autoplay="0" width="270" height="180"></iframe>';
				          }else if(FieldType == 'audio/mpeg'){
				            SingleField += '<audio controls>';
				              SingleField += '<source src="horse.ogg" type="audio/ogg">';
				              SingleField += '<source src="'+attachment.url+'" type="audio/mpeg">';
				            SingleField += '</audio>';
				          }
		            SingleField += '</div>';
		            SingleField += '<em onClick="this.parent().remove();"><span></span><span></span></em>';
		          SingleField += '</div>';
		          //
							MasterElement.find('.'+field+'_previewLists').append(SingleField);
							if(MasterElement.closest('li').length > 0 ){
								var BreLop = MasterElement.closest('li');
								BreLop.find('.SaveChange').css({"pointer-events":'auto',"opacity":'1'});
							}
							my_index++;
						});

						InputsChanged( MasterElement );

					}else {
						var attachment = image_frame.state().get('selection').first();
						attachment = attachment.toJSON();
						gallery_ids[my_index] = attachment.id
						MasterElement.find('.CustomImage_'+field+'_id').val(attachment.id);
						MasterElement.find('.CustomImage_'+field).val(attachment.url);
		      	if(FieldType == 'image'){
							MasterElement.find('.CustomImage_'+field+'_preview').find('img').attr('src', attachment.url).show();
		      	}else if(FieldType == 'video/mp4'){
							MasterElement.find('.CustomImage_'+field+'_preview').find('iframe').attr('src', attachment.url).show();
							MasterElement.find('.CustomImage_'+field+'_duration').val(attachment.fileLength);
		        }else if(FieldType == 'audio/mpeg'){
		          var WindowsField = '<audio controls>';
		            WindowsField += '<source src="horse.ogg" type="audio/ogg">';
		            WindowsField += '<source src="'+attachment.url+'" type="audio/mpeg">';
		          WindowsField += '</audio>';
		          MasterElement.find('.CustomImage_'+field+'_preview').html(WindowsField);
		          MasterElement.find('.CustomImage_'+field+'_duration').val(attachment.fileLength);
		        }				
						MasterElement.find('.CustomImage--Preview').show();
						MasterElement.find('.CustomImage--RemoveButton').show();				
						if( MasterElement.closest('li').length > 0 ){
							var BreLop = MasterElement.closest('li');
							BreLop.find('.SaveChange').css({"pointer-events":'auto',"opacity":'1'});
						}

						InputsChanged( MasterElement );

					}
					var ids = gallery_ids.join(",");
					$(this).attr("data-ids",ids);
				});
		    image_frame.on('open',function() {
		      // On open, get the id from the hidden input
		      // and select the appropiate images in the media manager
		      var selection =  image_frame.state().get('selection');
		      if( $(this).data('ids') != undefined ){
			      var ids = $(this).data('ids').split(',');
			      ids.forEach(function(id) {
			        var attachment = wp.media.attachment(id);
			        attachment.fetch();
			        selection.add( attachment ? [ attachment ] : [] );
			      });
		      }
		    });
		    image_frame.open();
		 	});

		// # INSERT GROUP .
			$('body').on("click",'[data-item-id]',function(){
				var BTN,UniqID,FieldsMaster;
				BTN = $(this);
				UniqID = BTN.data('item-id');
				FieldsMaster = $('.Insert-Setp-InForm[data-uniq-item="'+UniqID+'"]');
				if( FieldsMaster.length > 0 ){

					// # DECODE KEY ITEMS IN THIS GROUP.
					var keysArgums = atob(BTN.data('keys-argums'));
					keysArgums = jQuery.parseJSON( keysArgums );
					CurrentKey = CreateNewKey(keysArgums);

					var AppendCenter = BTN.closest('.-Insert-Fields-Tool').find('.-Fields-Insert-Area');
					// # 
					StopInsert = true;
					var NewBoxes = EachingFields(FieldsMaster,CurrentKey);

					if( StopInsert != false ){
						var GroupItemsCount = AppendCenter.find('.-Revilotion-Inputs-Fields').length + 1;
						if( AppendCenter.find('.Title-MoreForms').length > 0 ){
							AppendCenter.find('.Title-MoreForms > h2 > count-items').html(GroupItemsCount);
						}else{
							LbAsmar = FieldsMaster.data('title').split('_Counter_');
							TitleOutput = '';
							TitleOutput +='<div class="Title-MoreForms">';
								TitleOutput +='<i class="fa-solid fa-sitemap"></i>';
								TitleOutput +='<h2>'+LbAsmar[0]+'<count-items>'+GroupItemsCount+'</count-items>'+LbAsmar[1]+' '+( ( FieldsMaster.data('inner-taps') != undefined && FieldsMaster.data('inner-taps') != false ) ? '<em data-find-tag="'+CurrentKey+'">['+BTN.closest('.-Insert-Fields-Tool').data('my-key')+']' : '' )+'</em></h2>';
								TitleOutput +='<div class="Remove-GroupField" data-remove-itemsgroup="group-item" data-tooltip="حذف كل العناصر "><i class="fa-solid fa-trash-can"></i></div>';
							TitleOutput +='</div>';
							AppendCenter.append(TitleOutput);
						}

						var output = '';
							output += '<div class="-Revilotion-Inputs-Fields" data-group-item="'+CurrentKey+'">';
								output += '<div class="Title-MoreForms"><i class="fa-solid fa-pen-to-square"></i><h2>العنصر  <em>['+CurrentKey+']</em></h2><div class="Remove-GroupField" data-remove-singlegroup="'+CurrentKey+'" data-tooltip="حذف العنصر '+CurrentKey+'"><i class="fa-solid fa-trash-can"></i></div></div>';
								$.each( NewBoxes ,function(el,fvlue) {
									if( fvlue != undefined ){
										output += fvlue;						
									}
								});
							output += '</div>';

						AppendCenter.append(output);
						PinnedJQuery();
						InitializeFields();

						InputsChanged( BTN );

						setTimeout(function() {
							FieldsMaster.find('.-fix-inputs-area').each(function(k,el){
								if( $(el).data('field-argums') != undefined ){
									ElEach = SetupFields($(el),CurrentKey,true);
								}
							});
						},500);
					}
				}
			});

		// # DublicateStep .
			$('body').on("click",'[data-dublicateitem-id]',function() {
				var BTN,ActionUniq,AppendCenter,DuplicateGroup,FieldArguments;
				BTN = $(this);
				ActionUniq = BTN.data('dublicateitem-id');
				DuplicateGroup = BTN.closest('.-DuplicateGroup-widgets');
				AppendCenter = DuplicateGroup.find('.-Fields-Insert-DuplicateGroup');

				// # DECODE KEY ITEMS IN THIS GROUP.
				var keysArgums = atob(BTN.data('keys-argums'),true);
				keysArgums = jQuery.parseJSON( keysArgums );
				CurrentKey = CreateNewKey(keysArgums);

				if( DuplicateGroup.data('field-argums') != undefined ){
					FieldArguments = DuplicateGroup.data('field-argums');
				}else{
					FieldArguments = DuplicateGroup.data('vars');
				}

				// ## 
				outout = '';
				// # DECODE FIELDS ARGUMENTS.
				var FieldArguments = $.base64.atob(FieldArguments,true);
				FieldArguments = jQuery.parseJSON( FieldArguments );

				console.log(FieldArguments);
				//FieldArguments = jQuery.parseJSON( FieldArguments );
				if( FieldArguments['fields'] != undefined ){

					var GroupItemsCount = AppendCenter.find('.-Revilotion-Fields-Dublicate').length + 1;
					if( AppendCenter.find('.Title-MoreForms-Dublicate-Master').length > 1 ){
						AppendCenter.find('.Title-MoreForms-Dublicate-Master > h2 > count-items').html(GroupItemsCount);
						AppendCenter.find('.Title-MoreForms-Dublicate-Master').show();
					}

					var InputName;
					if( FieldArguments.parent_id != undefined ){
						InputName = FieldArguments.parent_id+'['+FieldArguments.id+']';
					}else{
						InputName = FieldArguments.id;
					}
					outout += '<div class="-Revilotion-Fields-Dublicate" data-dublicate-group-item="'+CurrentKey+'">';
						outout += '<div class="Title-MoreForms-Dublicate"><i class="fa-solid fa-pen-to-square"></i><h2>الشريحة <em>['+CurrentKey+']</em></h2><div class="Remove-Dublicate-GroupField" data-remove-dublicate-singlegroup="'+CurrentKey+'" data-tooltip="حذف العنصر '+CurrentKey+'"><i class="fa-solid fa-trash-can"></i></div></div>';
						// #
						$.each( FieldArguments['fields'] ,function(k,v) {
							v['parent_id'] = InputName+'['+CurrentKey+']';
							outout += CreateFields(v);
							if( v.shows_selected != undefined && v.show_create_fields != undefined ){
									$.each(v.show_create_fields ,function(skey,meky) {
										meky['parent_id'] = InputName+'['+CurrentKey+']';
										var StartArguments = JSON.stringify(meky.shows_At);										
										output += '<field-inserts class="Insert-SelectOptions" data-field-id="'+meky.id+'" data-id="'+$.base64.btoa( StartArguments )+'" '+( ( jQuery.inArray( value, meky.shows_At) !== -1 ) ? '' : 'style="display:none"' )+'>';
											output += CreateFields(meky);
										output += '</field-inserts>';
									});
							}


						});
					outout += '</div>';

					AppendCenter.append(outout);

					InputsChanged( BTN );

					PinnedJQuery();
					InitializeFields();
				}
			});

		// # SHOW SAYES FIELDS 
			$('body').on("click",'.Sayes-fields-ShowsIn',function(){
				var BTN,MasterLabels;
				BTN = $(this);
				BTN.parent().find('.-Sayes-Fields-Context').toggle();
			});

			$('body').on("click",'[data-argums-say]',function(){
				var BTN,ParentCenter,ArgusValue,SayesCenter;
				BTN = $(this);
				ArgusValue = BTN.data('argums-say');
				ParentCenter = BTN.parent();

				ParentCenter.find('.Says-Single-Field').hide();
				ParentCenter.find('.Says-Single-Field[data-say-id="'+ArgusValue+'"]').show();

				// # CONTEXT CREATE ADD MORE OPTIONS .
				var CurrentBox = BTN.closest('.-fix-inputs-area');
				var key = BTN.closest('.-Revilotion-Inputs-Fields').data('group-item');
				output = '';
				CurrentBox.data("curr-value-field",ArgusValue);

				if( ArgusValue == 'Select' || ArgusValue == 'Taxonomy-Select' || ArgusValue == 'CheckBox' || ArgusValue == 'Taxonomy-CheckBox' || ArgusValue == 'Radio' || ArgusValue == 'Taxonomy-Radio' ){
					CurrentBox.parent().find('.Insert-SelectOptions').each(function (ae,fix) {
						var ValueArgums = $(fix).data('id');
						// # DECODE FIELDS ARGUMENTS.
						var decodedArguments = $.base64.atob(ValueArgums,true);
						decodedArguments = jQuery.parseJSON( decodedArguments );
						if( jQuery.inArray(ArgusValue, decodedArguments) !== -1 ){
							$(fix).show().attr("showin",'true');
						}else{
							$(fix).hide().attr("showin",'false');
						}
					});

				}else{
					CurrentBox.parent().find('field-inserts').hide();
				}

				/*if( ArgusValue == 'Select' || ArgusValue == 'Taxonomy-Select' || ArgusValue == 'CheckBox' || ArgusValue == 'Taxonomy-CheckBox' || ArgusValue == 'Radio' || ArgusValue == 'Taxonomy-Radio' ){
					if( ArgusValue == 'Taxonomy-Select' || ArgusValue == 'Taxonomy-CheckBox' || ArgusValue == 'Taxonomy-Radio' ){
						CurrentBox.parent().find('field-inserts').hide();
						CurrentBox.parent().find('.Insert-SelectOptions[data-id="'+ArgusValue+'"]').show();			
					}else{
						CurrentBox.parent().find('field-inserts').hide();
						CurrentBox.parent().find('.Insert-TextAreaOptions').show();
					}
				}else{
					CurrentBox.parent().find('field-inserts').hide();
				}*/
			});

			$('body').on("click",'[data-select-open]',function() {
				$(this).parent().toggleClass('active');
			});

			$('body').on("click",'[data-selected]',function() {
				$(this).toggleClass('active').siblings().removeClass('active');
				$(this).closest('.Select-Options-Items').removeClass('active');
				$(this).closest('.Select-Options-Items').find('.Selected-Value').val( $(this).data('selected') );

				$(this).closest('.Select-Options-Items').find('h2 > span').html( $(this).html() );

				if( $(this).closest('.-Next-Actions-Selected').length > 0 && $(this).data('selected') == 'mail'){
					$('.-fix-post-box[data-show-postbox="mail"]').show();
				}else if( $(this).closest('.-Next-Actions-Selected').length > 0 ){
					$('.-fix-post-box[data-show-postbox="mail"]').hide();
				}
				InputsChanged($(this) );
			});

			$('body').on("click",'.UI--selector--languages--ajax [data-selected]',function() {
				var BUTTON = $(this);
				var BUTTON__VALUE = $(this).data('selected');
				var UI__Selector__center = BUTTON.closest('.UI--selector--languages--ajax');
				var UI__Selector__Parent = UI__Selector__center.parent();

				UI__Selector__center.css({"pointer-events":'none',"opacity":'0.7'});

				var data = {
					'action':'update___object__languages',
					'CurrentValue': BUTTON__VALUE,
				};

				if( $('[name="tag_ID"]').length > 0 && $('[name="taxonomy"]').length > 0 ){
					data['ObjectAction'] = 'taxonomy';
					data['ObjectID'] = $('[name="tag_ID"]').val();
					data['ObjectType'] = $('[name="taxonomy"]').val();
				}

				if( $('#post_ID').length > 0 && $('#post_type').length > 0 ){
					data['ObjectAction'] = 'posts';
					data['ObjectID'] = $('#post_ID').val();
					data['ObjectType'] = $('#post_type').val();
				}

				var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
				$.ajax({
					url: AjaxURL,
					dataType: 'json',
					type: 'POST',
					data:data,
					success: function(msg) {
						UI__Selector__Parent.html(msg.output);
					}
				});


			});


			$(document).mouseup(function(e){
				dropdown = $('.Select-Options-Items .-Select-DropDown');
				if(!dropdown.is(e.target) && dropdown.has(e.target).length === 0 ) {
			    	dropdown.closest('.Select-Options-Items').removeClass('active');
			  	}  
			});

		// # INSRT NEW STEP .
			$('body').on("click",'[data-insert-step]',function() {
				var BTN,Argums,AppendCenter,StepID,PostID;
				BTN = $(this);
				Argums = BTN.data('insert-argums');
				StepID = BTN.data('step');
				PostID = BTN.data('insert-step');
				AppendCenter = $('.Inner-selected-fields[data-uniq-step="'+StepID+'"]');

				// # DECODE KEY ITEMS IN THIS GROUP.
				var keysArgums = atob(Argums);
				keysArgums = jQuery.parseJSON( keysArgums );
				CurrentKey = CreateNewKey(keysArgums);
				// #
				var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';

				AjaxRequest({
					url: AjaxURL,
					dataType: 'json',
					type : "POST",
					data: { "action":'insertstep',"Argums": Argums, "PostID":PostID ,"key":CurrentKey},
					success: function(msg) {
						AppendCenter.append(msg.output);
						BTN.data("insert-argums",msg.Argums);
					}
				});		
			});

			$('body').on("click",'[data-remove-singlegroup]',function(){
				InputsChanged( $(this).closest('.-Revilotion-Inputs-Fields').parent() );
				$(this).closest('.-Revilotion-Inputs-Fields').remove();
			});

			$('body').on("click",'[data-remove-dublicate-singlegroup]',function(){
				InputsChanged( $(this).closest('.-Revilotion-Fields-Dublicate').parent() );
				$(this).closest('.-Revilotion-Fields-Dublicate').remove();
			});
			
			$('body').on("click",'[data-remove-itemsgroup]',function(){
				InputsChanged( $(this).closest('.-Fields-Insert-Area').parent() );
				$(this).closest('.-Fields-Insert-Area').html('');
			});
			$('body').on("click",'[data-remove-setp]',function(){
				InputsChanged( $(this).closest('.InputsAppender--Fields-BoxArea') );
				$(this).closest('.InputsAppender--Fields-BoxArea').remove();
			});

		// # -DropDown-Fields-Action 
			$('body').on("click",'.-DropDown-Fields-Action',function(){
				$(this).parent().toggleClass('active');
			});

			$(document).mouseup(function(e){
				dropdown = $('.Select-Childs');
				if(!dropdown.is(e.target) && dropdown.has(e.target).length === 0 ) {
			    	dropdown.parent().removeClass('active');
			  	}  
			});
		
		// # TEXONOMY TAPS SELECTED .
			$('body').on('click','.SortedTaxonomyTaps > li',function(){
				var TermID = $(this).data('id');
				var TermName = $(this).find('span').text();
				var TermAttr = $(this).find('attr').text();
				var FieldId = $(this).parent().data('field');
				var key = UniqID();
				if($(this).hasClass('active')){
					$(this).removeClass('active');
					$('.SortedItemsTaps[data-field="'+FieldId+'"] > li[data-id="'+TermID+'"]').remove();
				}else{
					$(this).addClass('active');
					Items = '<li data-id="'+TermID+'">';
						Items += '<input type="hidden" name="'+FieldId+'['+key+']" value="'+TermID+'" />';
						Items += '<span>'+TermName+'</span>';
						Items += '<em>'+TermAttr+'</em>';
						Items += '<TapRemoveItem class="TapRemoveItem"><i class="fa fa-times"></i></TapRemoveItem>';
					Items  += '</li>';
					$('.SortedItemsTaps[data-field="'+FieldId+'"]').append(Items);
					$(this).parent().parent().find('mycountis').data('count',key);
				}
			});
			$('body').on('click','.TapRemoveItem',function(){
				var MyTerID = $(this).parent().data('id');
				var MyFieID = $(this).parent().parent().data('field');
				$('.SortedTaxonomyTaps[data-field="'+MyFieID+'"] > li[data-id="'+MyTerID+'"]').removeClass('active');
				$(this).parent().remove();
			});

		// # ON CHANGE RANGE INPUT .
			$('input[type=range]').on('input', function(e){
			  var min = e.target.min,
			    max = e.target.max,
			    val = e.target.value;
			  $(e.target).css({'backgroundSize': (val - min) * 100 / (max - min) + '% 100%'});

			  if( $(this).data('addvals') != undefined ){
			  	var BeThis,inputs=0,totalSum = 0;
			  	BeThis = $(this);
			  	BeThis.parent().find('span').text(val);
			  	// ################### // 
					BeThis.parent().parent().find('input[type=range]').each(function(ints, int) {
						totalSum += Number($(int).val());
						inputs++
					});
					totalRating = totalSum / inputs;
					totalScore = totalRating.toFixed(1).replace(/\.0$/, "");
					$('[data-totalrate]').find('em').text(totalScore);
					$('[data-totalrate]').find('input').val(totalScore);
					var percentComplete = totalScore / max*100;
					$('[data-totalrate]').find('parseo').css({"width":percentComplete+'%'});
			  }

			}).trigger('input');

		// # REMOVE IMAGE.	
			$('body').on('click','.CustomImage--RemoveButton',function(){
				var sms = $(this).closest('.FieldUpload--FilesBox');
				var se = sms.find('.CustomImage--Preview');
				var v__multiple = $(this).data('multiple');
				if( v__multiple != undefined && ( v__multiple == 'true' || v__multiple == true ) ) {
					sms.find('.CustomImage--Boxed').each(function(k,ke) {
						$(ke).remove();
					});
					$(this).hide();
				}else{
					sms.find('input').val('');
					se.find('img').attr('src','').hide();
					se.find('iframe').attr('src','').hide();
					se.find('audio').remove();
					se.hide();
					$(this).hide();
				}




				var Tubs = $(this).closest('.SingularElementGroup');
				if(Tubs.find('.SaveChange').length > 0){
					Tubs.find('.SaveChange').css({"pointer-events":'auto',"opacity":'1'});
				}

				InputsChanged( sms );

			});

		// # FIELDS SERCHING CENTER .
			function SetActiveSelectedCompo(selected_elements,searching__element,remove=false) {

				var fix__selected_items = selected_elements.find('[data-select-custom-items]');
				var fix__searching__items = searching__element.find('[data-select-custom-items]');

				var List = [];

				searching__element.find('[data-select-custom-items]').each(function(r,se__elem ) {
					$(se__elem).parent().removeClass('active');
				});


				if( fix__selected_items.length > 0 ){
					selected_elements.find('[data-select-custom-items]').each(function(e,elQuery) {
						var elementID =  $(elQuery).data('select-custom-items');
						searching__element.find('[data-select-custom-items="'+elementID+'"]').parent().addClass('active');
						// #List[ elementID ] = elementID;
						List.push(elementID);
					});
				}

			}

			function SearchFields(el,empty=false) {
				
				searchVal = $(el).val();
				Uniq = $(el).data('input-search-center');
				FieldsCenter = $('.-ScrollerCenter[data-uniqid="'+Uniq+'"]');
				var argums = $(el).data('arguments');
				var decodedArguments = atob(argums);
				decodedArguments = jQuery.parseJSON( decodedArguments );

				if( $(el).data('part') != undefined  ){
					FieldsCenter.show();
					var data = {
						'action':'SearchInFields',
						'SearchQuery':searchVal,
						'args':argums,
						'part':$(el).data('part')
					};
					// #
					var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
					AjaxRequest({
						url: AjaxURL,
						dataType: 'json',
						type : "POST",
						data: data,
						success: function(msg) {
							FieldsCenter.html('');
							FieldsCenter.append(msg.output);
							FieldsCenter.show();
							FieldsCenter.data("loadmore", msg.arguments);

							if( $('.-result-searching-too[data-uniqid="'+Uniq+'"]').length > 0 ){
								if( searchVal != '' ){
									$('.-result-searching-too[data-uniqid="'+Uniq+'"]').html('<span>نتائج البحث عن </span>"<strong>'+searchVal+'</strong>"');
								}else{
									$('.-result-searching-too[data-uniqid="'+Uniq+'"]').html('');
								}
							}

							if(msg.end != undefined && msg.end == true){
								FieldsCenter.attr("data-finish",true);
								$('[data-more-click="'+Uniq+'"]').hide();
							}else{
								FieldsCenter.attr("data-finish",false);
								$('[data-more-click="'+Uniq+'"]').show();
							}
							setTimeout(function() {
								//SetActiveSelectedCompo($('.--JS-result--select[data-uniqid="'+Uniq+'"]'),FieldsCenter);								
							},150);
						}
					});

				}else{
					if( decodedArguments.field.type == 'Taxonomy-Select' || decodedArguments.field.type == 'Posts-Select' || decodedArguments.field.type == 'Users-Select' || decodedArguments.field.type == 'Select' || decodedArguments.field.type == 'Select-Icon' || decodedArguments.field.type == 'CuntrySelect' || decodedArguments.field.type == 'Phone-Number'  ){
						if( empty == true ){
							FieldsCenter.find('li').show();
						}else{
							FieldsCenter.find('li').each(function(e,selectelem) {
								elemTitle = $(selectelem).data('title')+'';
								if( elemTitle.indexOf( searchVal ) >= 0) {
									$(selectelem).show();
								}else{
									$(selectelem).hide();
								}
							});
						}
					}else if( decodedArguments.field.type == 'Taxonomy-CheckBox' || decodedArguments.field.type == 'Posts-CheckBox' || decodedArguments.field.type == 'Users-CheckBox' ){

						if( empty == true ){
							FieldsCenter.find('.-CheckBox-Box-Item').show();
						}else{
							FieldsCenter.find('.-CheckBox-Box-Item').each(function(e,checkboxitem) {
								elemTitle = $(checkboxitem).find('em').text()+'';
								if( elemTitle.indexOf( searchVal ) >= 0) {
									$(checkboxitem).show();
								}else{
									$(checkboxitem).hide();
								}
							});
						}

					}else if( decodedArguments.field.type == 'Taxonomy-Radio' || decodedArguments.field.type == 'Posts-Radio' || decodedArguments.field.type == 'Users-Radio' ){

						if( empty == true ){
							FieldsCenter.find('.-Radio-Box-Item').show();
						}else{
							FieldsCenter.find('.-Radio-Box-Item').each(function(e,checkboxitem) {
								elemTitle = $(checkboxitem).find('em').text()+'';
								if( elemTitle.indexOf( searchVal ) >= 0) {
									$(checkboxitem).show();
								}else{
									$(checkboxitem).hide();
								}
							});
						}
					}else if( decodedArguments.field.type == 'Compo-Select-Field' ){
						FieldsCenter.show();
						if( empty == true ){
							FieldsCenter.find('.-selected-item-byme').show();
						}else{
							FieldsCenter.find('.-selected-item-byme').each(function(e,checkboxitem) {
								elemTitle = $(checkboxitem).data('title')+'';
								if( elemTitle.indexOf( searchVal ) >= 0) {
									$(checkboxitem).show();
								}else{
									$(checkboxitem).hide();
								}
							});
						}

					}

				}
			}

			$('body').on('keypress keyup keydown', '[data-input-search-center]', function(evt){
				fieldID = $(this).parent().data('type');
				var CurrElement = $(this);

				if(evt.keyCode == 13 ){
					evt.preventDefault();
					SearchFields(CurrElement,false);
					return false;		
				}else{
					setTimeout(function(){
						if(evt.keyCode === 27) {
							SearchFields(CurrElement,true);
						}else if(CurrElement.val() == '') {
							SearchFields(CurrElement,true);
						}else {
							SearchFields(CurrElement,false);
						}
					},100);
				}
			});

		// # SELECTED SHOWS OR CREATE FIELDS
			$('body').on("click",'[data-shows-selected]',function() {
				var BTN,MetaKey,ShowsType,argums,data__uniqid;

				BTN = $(this);
				MetaKey = BTN.data('meta-key');
				ShowsType = BTN.data('shows-selected');
				argums = BTN.data('argums-fields');
				data__uniqid = BTN.data('uniqid');

				if( argums == undefined ){
					$('.-Hide-Boxes-Shows').each(function(e,el) {
						var f = $(el).data('meta-key');
						var st = $(el).data('show-type');
						var un = $(el).data('uniqid');
						if( f == MetaKey && st != ShowsType && un == data__uniqid ){
							$(el).hide();
						}else if( f == MetaKey && st == ShowsType && un == data__uniqid ){
							$(el).show();
						}
					});
				}else if( argums != undefined ){

					$('.-Hide-Boxes-Shows').each(function(e,el) {
						var f = $(el).data('meta-key');
						var un = $(el).data('uniqid');
						if( f == MetaKey && un == data__uniqid ){
							$(el).remove();
						}
					});

					// ## 
					argums = atob(argums);
					argums = jQuery.parseJSON( argums );
					if( argums['choose_fields'] != undefined && argums['choose_fields'][ShowsType] != undefined ){
						outout = '';
						if( argums.parent_id != undefined ){
							var InputName = argums.parent_id+'['+argums.id+']'+'['+argums['choose_fields'][ShowsType]['id']+']';
						}else{
							var InputName = argums.id+'['+argums['choose_fields'][ShowsType]['id']+']';
						}

						outout +='<div class="-Hide-Boxes-Shows Group-Hide-Insert" data-uniqid="'+data__uniqid+'" data-meta-key="'+argums.select_field.id+'" data-show-type="'+argums['choose_fields'][ShowsType]['id']+'" data-create-fields="true">';
							outout +='<div class="Title-MoreForms"><i class="fa-solid fa-sliders"></i><h2>'+argums['choose_fields'][ShowsType]['title']+'</h2></div>';

							$.each( argums['choose_fields'][ShowsType]['fields'] ,function(k,single_field) {
								single_field['parent_id'] = InputName;

								if(  argums['value'] != undefined && argums['value'][ argums['choose_fields'][ShowsType]['id'] ] != undefined && argums['value'][ argums['choose_fields'][ShowsType]['id'] ][ single_field['id'] ] != undefined ){
									single_field['value'] = argums['value'][ argums['choose_fields'][ShowsType]['id'] ][ single_field['id'] ];
								}
								outout += CreateFields(single_field);
							});

						outout +='</div>';
						BTN.closest('.-fix-inputs-area').after(outout);
						PinnedJQuery();
						InitializeFields();
					}
				}
			});

		// # CUNTRYLOADED .
		var CuntryObject = false;
		function CuntryesLoaded() {

			/*if( CuntryObject == false ){
				var AjaxURL = HomeURL+'/CuntryLoaded.php';
				$.getJSON(AjaxURL, function(info) {
					CuntryObject = info;
					CuntryesLoaded();
				});
			}else{
				var i = 0;
				$('[data-cuntry-loded]').each(function(m,elem) {
					if( $(elem).data('loaded') == undefined ){i++;
						if( $(elem).data('cuntry-loded') == 'phone_number' ){
							var Append__elem = '';
							$.each( CuntryObject ,function(k,cuntry) {
								Append__elem += '<li data-title="'+cuntry.ar_name+'" data-cuntry-selected="'+cuntry.phone+'"><div class="-field-select-image"><img src="'+cuntry.logo+'"></div><span>'+cuntry.ar_name+' <em>( '+cuntry.phone+' )</em></span></li>';
							});

							$(elem).parent().find('ul').html(Append__elem);
							$(elem).data('loaded',true);
						}
						
					}
				});
			}*/

		}
		CuntryesLoaded();

		// # CUNTRY EDITS .
			$('body').on("click",'[data-cuntryselect-open]',function() {
				$(this).parent().toggleClass('active');
			});

			$('body').on("click",'[data-cuntry-selected]',function() {
				$(this).toggleClass('active').siblings().removeClass('active');
				$(this).closest('.-Select-Field-Code').removeClass('active');
				var e = $(this).data('cuntry-selected');
				$(this).closest('.-PhoneNumber-Field-YC').find('.Selected-Value').val( e );

				$(this).closest('.-Select-Field-Code').find('.-select-Code-number-title > span').html( $(this).html() );
				
			});

			$(document).mouseup(function(e){
				dropdown = $('.-Select-Field-Code .-Select-DropDown-PoneNumber');
				if(!dropdown.is(e.target) && dropdown.has(e.target).length === 0 ) {
			    	dropdown.closest('.-Select-Field-Code').removeClass('active');
			  	}  
			});

		// # WIDGETS SETUP .

			// # CREATE WIDGETS
				$('body').on("click",'[data-insert-widget]',function(){
					var BTN,WidgetID,WidgetAruments,WidgetInsert,WidgetAppender;
					BTN = $(this);
					WidgetID = BTN.data('insert-widget');
					WidgetAruments = BTN.data('argums-fields');
					// #
					WidgetInsert = BTN.closest('.RightWidgets-Select');
					// #
					WidgetAppender = BTN.closest('.-Master-Selected-Icon').find('.-Selcted-widget-items');
					// #
					var keysArgums = atob( WidgetInsert.data('argums-keys') );
					keysArgums = jQuery.parseJSON( keysArgums );
					CurrKey = CreateNewKey(keysArgums);
					// #

					var FieldArguments = WidgetInsert.data('fields-argums');
					FieldArguments = atob( FieldArguments );
					FieldArguments = jQuery.parseJSON( FieldArguments );

					if( WidgetAruments != undefined ){
						var WidgetAruments = atob( WidgetAruments );
						WidgetAruments = jQuery.parseJSON( WidgetAruments );
						
						var html_output='';

						var Cr_uniq = UniqID();
						html_output +='<div class="-widget-item -widget-type-'+WidgetAruments.widget_id+'" data-uniq-key="'+CurrKey+'" data-un-key="'+Cr_uniq+'" data-widget-post-setup="false" data-fields-argums="'+WidgetInsert.data('fields-argums')+'">';

							html_output +='<sortbyme class="-widget-item-title-">';
								html_output += '<h2>'+WidgetAruments['title']+( ( WidgetAruments['description'] != undefined ) ? '<p>'+WidgetAruments['description']+'</p>' : '' )+'</h2>';
								html_output +='<i class="fa-solid fa-brush hoverable activable -widget-open" data-uniq="'+Cr_uniq+'"></i>';
								html_output +='<i class="fa-solid fa-xmark hoverable activable -widget-remove" data-remove-widgets="'+Cr_uniq+'"></i>';
								html_output +='<input type="hidden" name="'+FieldArguments.InputName+'['+CurrKey+'][widget_id]" value="'+WidgetID+'">';
							html_output +='</sortbyme>';

							html_output +='<div class="widget-fields-appender" data-uniq="'+Cr_uniq+'" data-loaded-fields="true">';

							  html_output += '<div class="widget-fields-InnerElemnt">';
						  		html_output += '<div class="widget-fields-area">';
										if( WidgetAruments.fields != undefined ){
											$.each( WidgetAruments.fields ,function (k,single_field) {
												single_field['parent_id'] = 'widget_post_meta_'+Cr_uniq+'_'+FieldArguments.id;
												html_output += CreateFields(single_field);
											});
										}
						  		html_output += '</div>';
						  		html_output += '<div class="-YC-widget-save-area">';
										html_output += '<div class="-YC-widget-action-button -YC-widget-save-button activable disable" data-uniq="'+CurrKey+'" data-un-key="'+Cr_uniq+'" data-save-widget="widget_post_meta_'+Cr_uniq+'_'+FieldArguments.id+'"><span>حفظ</span><i class="fa-solid fa-arrow-left"></i></div>';
									html_output +='</div>';

								html_output +='</div>';
							html_output +='</div>';


						html_output +='</div>';

						WidgetAppender.append(html_output);

						// إعادة تفعيل السحب بعد إضافة ودجت جديد
						if ( typeof window.kayanInitSortables === 'function' ) {
							window.kayanInitSortables(jQuery);
						} else if ( $.fn.sortable && WidgetAppender.hasClass('apbsortable') ) {
							try { if ( WidgetAppender.data('ui-sortable') ) { WidgetAppender.sortable('destroy'); } } catch (err) {}
							WidgetAppender.sortable({
								handle: WidgetAppender.attr('data-connect-with') || 'sortbyme, .-widget-item-title-',
								cursor: 'grabbing',
								tolerance: 'pointer',
								placeholder: 'kayan-sort-placeholder',
								forcePlaceholderSize: true,
								opacity: 0.92,
								axis: 'y'
							});
						}

						// # 

						setTimeout(function() {
							$('.-widget-item[data-uniq-key="'+CurrKey+'"]').focus();
						 	$('body, html').animate({"scrollTop": ($('.-widget-item[data-uniq-key="'+CurrKey+'"]').offset().top - 125)},500);
						},500);

						var data = {
							'action':'CreateWidgetID',
							'widget_type':WidgetID,
							'input':FieldArguments.id,
							'uniqKey':Cr_uniq,							
						};
						// #
						var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
						AjaxRequest({
							url: AjaxURL,
							dataType: 'json',
							type : "POST",
							data: data,
							success: function(msg) {
								$('.-widget-item[data-uniq-key="'+CurrKey+'"]').find('.-widget-item-title-').append('<input type="hidden" name="'+FieldArguments.id+'['+CurrKey+'][widget_post__id]" value="'+msg.post_id+'">');
								$('.-widget-item[data-uniq-key="'+CurrKey+'"]').data("widget-post-id",msg.post_id);
								$('.-widget-item[data-uniq-key="'+CurrKey+'"]').find('.-YC-widget-save-button').removeClass('disable');
								$('.-widget-item[data-uniq-key="'+CurrKey+'"]').removeAttr('data-widget-post-setup');
								PinnedJQuery();
								InitializeFields();
							}
						});
					}
				});

			
			// # SAVE WIDGETS .
				$('body').on("click",'[data-save-widget]',function() {
					var BTN = $(this);
					var Data_arr = BTN.closest('form').serializeArray();
					var data = BTN.closest('form').serialize();
					var WidgetInput = BTN.data('save-widget');
					var ButtonUniq = BTN.data('uniq');
					var Button__Unkey = BTN.data('un-key');
					var FieldArguments = $('.-widget-item[data-un-key="'+Button__Unkey+'"]').data('fields-argums');
					var Widget__post_id = $('.-widget-item[data-un-key="'+Button__Unkey+'"]').data('widget-post-id');
					var AppenderCenter = $('.widget-fields-appender[data-uniq="'+Button__Unkey+'"]');

					if( $('.-widget-item[data-uniq-key="'+ButtonUniq+'"]').data('widget-post-id') != undefined ){
						AppenderCenter.addClass('disable-fields');
						BTN.addClass('loaded-button');
						BTN.append('<div class="-Loader-widgets-SVG"><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg></div></div>');

						var NewFormData = {
							"action":'SaveWidgetID',
							"widget__post":Widget__post_id,
							"field__id":WidgetInput,
							"field_arguments":FieldArguments,
							"single_widget__uniq":Button__Unkey,
						};

						// # FIELDS DECODE .
						FieldArguments = atob( FieldArguments );
						FieldArguments = jQuery.parseJSON( FieldArguments );

						var Test__ids = Data_arr;
						var key__ids = {};
						$.each(Data_arr,function(z,r) {
							if( r['name'].indexOf( WidgetInput ) >= 0 ){
								var end__counter = 0;
								$.each( Test__ids,function(kers,vres) {
									if( r['name'] == vres['name'] ) {
										key__ids[ vres['value'] ] = end__counter;
										end__counter++;
									}
								});
								//alert(end__counter);
					  		if( end__counter > 1 ){
					  			var in___value = r['value'];
					  			var find__key = key__ids[ in___value ];
					  			if( NewFormData[ r['name'] ] == undefined ) NewFormData[ r['name'] ] = [];
					  			NewFormData[ r['name'] ].push(in___value);
					  		}else{
					  			NewFormData[ r['name'] ] = r['value'];
					  		}
							}else if(  r['name'].indexOf( FieldArguments.id ) >= 0  ){
								var end__counter = 0;
								$.each( Test__ids,function(kers,vres) {
									if( r['name'] == vres['name'] ) {
										key__ids[ vres['value'] ] = end__counter;
										end__counter++;
									}
								});

					  		if( end__counter > 1 ){
					  			var in___value = r['value'];
					  			var find__key = key__ids[ in___value ];
					  			if( NewFormData[ r['name'] ] == undefined ) NewFormData[ r['name'] ] = [];
					  			NewFormData[ r['name'] ].push(in___value);
					  		}else{
					  			NewFormData[ r['name'] ] = r['value'];
					  		}
							}

							// # HOW TO TERM ID.
							if( r['name'] == 'tag_ID' ){
								NewFormData[ r['name'] ] = r['value'];
							}
							
							// # HOW TO TERM ID.
							if( r['name'] == 'post_ID' ){
								NewFormData[ r['name'] ] = r['value'];
							}

						});

						// # console.log(NewFormData);
						var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
						AjaxRequest({
							url: AjaxURL,
							dataType: 'json',
							type : "POST",
							data: NewFormData,
							success: function(msg) {
								AppenderCenter.removeClass('disable-fields');
								AppenderCenter.html(msg.output);
								$('body, html').animate({"scrollTop": ($('.-widget-item[data-un-key="'+Button__Unkey+'"]').offset().top - 125)},500);
								PinnedJQuery();
								InitializeFields();
							}
						});
					}

				});

			// # OPEN SINGLE WIDGET.
				$('body').on("click",'.-widget-open',function() {
					var BTN = $(this);
					var widgetUniq = BTN.data('uniq');
					var FieldArguments = $('.-widget-item[data-un-key="'+widgetUniq+'"]').data('fields-argums');
					var Widget__post_id = $('.-widget-item[data-un-key="'+widgetUniq+'"]').data('widget-post-id');
					// #
					$('.widget-fields-appender[data-uniq="'+widgetUniq+'"]').toggle();
					if( $('.widget-fields-appender[data-uniq="'+widgetUniq+'"]').data('loaded-fields') == undefined ){
						var Loader__output = '<fields-loader-widgets>';
							Loader__output += '<ul>';
								Loader__output += '<li><div class="f-lod"></div><div class="s-lod"><div></div><div></div></div></li>';
								Loader__output += '<li><div class="f-lod"></div><div class="s-lod"><div></div><div></div></div></li>';
								Loader__output += '<li><div class="f-lod"></div><div class="s-lod"><div></div><div></div></div></li>';
								Loader__output += '<li><div class="f-lod"></div><div class="s-lod"><div></div><div></div></div></li>';
								Loader__output += '<li><div class="f-lod"></div><div class="s-lod"><div></div><div></div></div></li>';
								Loader__output += '<li><div class="f-lod"></div><div class="s-lod"><div></div><div></div></div></li>';
								Loader__output += '<li><div class="f-lod"></div><div class="s-lod"><div></div><div></div></div></li>';
								Loader__output += '<li><div class="f-lod"></div><div class="s-lod"><div></div><div></div></div></li>';
								Loader__output += '<li><div class="f-lod"></div><div class="s-lod"><div></div><div></div></div></li>';
								Loader__output += '<li><div class="f-lod"></div><div class="s-lod"><div></div><div></div></div></li>';
							Loader__output += '</ul>';					
						Loader__output += '</fields-loader-widgets>';					
						$('.widget-fields-appender[data-uniq="'+widgetUniq+'"]').append(Loader__output);

						var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
						AjaxRequest({
							url: AjaxURL,
							dataType: 'json',
							type : "POST",
							data: {"action":'Open__widget',"field_arguments":FieldArguments,"widget__post":Widget__post_id,"single_widget__uniq":widgetUniq},
							success: function(msg) {
								$('.widget-fields-appender[data-uniq="'+widgetUniq+'"]').html(msg.output);
								$('.widget-fields-appender[data-uniq="'+widgetUniq+'"]').data("loaded-fields",'true');
								PinnedJQuery();
								InitializeFields();
							}
						});
					}
				});

			// # UNDO SINGLE WIDGET.	
				$('body').on("click",'.-YC-widget-undo-button',function() {
					var BTN = $(this);
					var widgetUniq = BTN.data('un-key');
					var FieldArguments = $('.-widget-item[data-un-key="'+widgetUniq+'"]').data('fields-argums');
					var Widget__post_id = $('.-widget-item[data-un-key="'+widgetUniq+'"]').data('widget-post-id');
					// #
					$('.widget-fields-appender[data-uniq="'+widgetUniq+'"]').addClass('disable-fields');
					BTN.addClass('loaded-button');
					BTN.append('<div class="-Loader-widgets-SVG"><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg></div></div>');

					var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
					AjaxRequest({
						url: AjaxURL,
						dataType: 'json',
						type : "POST",
						data: {"action":'Open__widget',"field_arguments":FieldArguments,"widget__post":Widget__post_id,"single_widget__uniq":widgetUniq},
						success: function(msg) {
							$('.widget-fields-appender[data-uniq="'+widgetUniq+'"]').removeClass('disable-fields');
							$('.widget-fields-appender[data-uniq="'+widgetUniq+'"]').html(msg.output);
							$('.widget-fields-appender[data-uniq="'+widgetUniq+'"]').data("loaded-fields",'true');
							$('body, html').animate({"scrollTop": ($('.-widget-item[data-un-key="'+widgetUniq+'"]').offset().top - 125)},500);
							PinnedJQuery();
							InitializeFields();
						}
					});
					
				});

			// # WIDGET REMOVE .	
				$('body').on("click",'[data-remove-widgets]',function(){
					var BTN = $(this);
					var UniqId = BTN.data('remove-widgets');
					var DataAlert = BTN.data('alert');
					var WidgetElement = $('.-widget-item[data-un-key="'+UniqId+'"]');
					var Widget__post_id = WidgetElement.data('widget-post-id');
					var FieldArguments = WidgetElement.data('fields-argums');
					var Data_arr = WidgetElement.closest('form').serializeArray();
					var AppendCenter = $('.widget-fields-appender[data-uniq="'+UniqId+'"]');
					var widgetUniq = WidgetElement.data('uniq-key');

					if(DataAlert != undefined && DataAlert != false ){
						$('.Popver--CoursesAlert').remove();
						AppendCenter.hide();
						WidgetElement.find('.-widget-item-title-').addClass('has-loader').append('<div class="-Loader-widgets-SVG"><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg></div></div>');

						var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';

						var data = {
							"action":'Remove-Widgets',
							"widget__post":Widget__post_id,
							"field_arguments":FieldArguments,
							"widgetUniq":widgetUniq,
						};

						// # FIELDS DECODE .
						FieldArguments = atob( FieldArguments );
						FieldArguments = jQuery.parseJSON( FieldArguments );

						$.each(Data_arr,function(z,r) {
							var TestName = r['name'].split('[')[0];
							console.log(TestName);
							if(  FieldArguments.id == TestName ){
								data[ r['name'] ] = r['value'];
							}

							// # HOW TO TERM ID.
							if( r['name'] == 'tag_ID' ){
								data[ r['name'] ] = r['value'];
							}
						});

						AjaxRequest({
							url: AjaxURL,
							dataType: 'json',
							type : "POST",
							data: data,
							success: function(msg) {
								WidgetElement.remove();
							}
						});
					}else{
					  var Data = {
					    "headtitle":'هل تريد حذف نموذج',
					    "alertcontent" : 'هل تريد بالتأكيد حذف هذا النموذج نهائياً؟',
					    "ConfirmAttrs" : 'data-remove-widgets="'+UniqId+'" data-alert="true"',
					  }
					  RemoveAlert(Data);
					}

				});

			
			// # WIDGETS SEARCH.
			  $('body').on('keypress keyup keydown', '[data-searching-widgets]', function(evt){
			    fieldID = $(this).parent().data('type');
			    var CurrElement = $(this);
			    var searchVal = CurrElement.val();

			    if(evt.keyCode == 13 ){
			      evt.preventDefault();
			      CurrElement.closest('.RightWidgets-Select').find('.widgets-Selected-Items').each(function(k,elem){
			        var h__two = $(elem).find('.WidgetInfo').find('h2').text()+'';
			        var p__elem = $(elem).find('.WidgetInfo').find('p').text()+'';
			        if( h__two.indexOf( searchVal ) >= 0 || p__elem.indexOf( searchVal ) >= 0 ) {
			          $(elem).show();
			        }else{
			          $(elem).hide();
			        }
			      });

			      return false;   
			    }else{
			      setTimeout(function(){
			        if(evt.keyCode === 27) {
			          CurrElement.closest('.RightWidgets-Select').find('.widgets-Selected-Items').show();
			        }else if(CurrElement.val() == '') {
			          CurrElement.closest('.RightWidgets-Select').find('.widgets-Selected-Items').show();
			        }else {
			          CurrElement.closest('.RightWidgets-Select').find('.widgets-Selected-Items').each(function(k,elem){
			            var h__two = $(elem).find('.WidgetInfo').find('h2').text()+'';
			            var p__elem = $(elem).find('.WidgetInfo').find('p').text()+'';
			            if( h__two.indexOf( searchVal ) >= 0 || p__elem.indexOf( searchVal ) >= 0 ) {
			              $(elem).show();
			            }else{
			              $(elem).hide();
			            }
			          });
			        }
			      },100);
			    }
			  });

			// # WidgetsChange .
		  
			  $('body').on("keypress keyup keydown change",'.widget-fields-appender input',function() {
			  	InputsChanged($(this) );
			  });
			  $('body').on("click",'.widget-fields-appender [data-shows-selected]',function() {
			  	InputsChanged($(this) );
			  });
			  $('body').on("bind",'.widget-fields-appender textarea',function() {
			  	InputsChanged($(this) );
			  });


		// # INSERT DB GROUPS .
			$('body').on("click",'[data-insert-db-row]',function(){
				var Button = $(this);
				var Currentid = Button.data('insert-db-row');
				var EachArea = $('[data-uniq-db-each="'+Currentid+'"]');
				var AppendArea = $('[data-db-insert-append="'+Currentid+'"]');
				var AppendCounter = $('[data-db-insert-count="'+Currentid+'"]');


				var Parent_fix = EachArea.closest('.-fix-inputs-area');
				if( Parent_fix.data('field-argums') != undefined ){
					var Arguments = Parent_fix.data('field-argums');
				}else{
					var Arguments = Parent_fix.data('vars');
				}				

				var InsertValidation = true;

				var data = {
					'action':'insert__single_DB',
					'Arguments': Arguments,
				};

				var ValuesList = {};

				EachArea.find('.-fix-inputs-area').each(function(k,elem){

					if( $(elem).data('field-argums') != undefined ){
						var ArgumsField = $(elem).data('field-argums');
					}
					// # DECODE FIELDS ARGUMENTS.
					var decodedArguments = $.base64.atob(ArgumsField,true);
					decodedArguments = jQuery.parseJSON( decodedArguments );

					var CurrentValue = GetFieldValue($(elem),decodedArguments);
					//alert('ID=>'+decodedArguments.id+'||CurrentValue=>'+CurrentValue);

					if( ( CurrentValue == false || CurrentValue == '' ) && decodedArguments.require != undefined && decodedArguments.require == true ){
						InsertValidation = false;
						if( $(elem).find('necessary').length == 0 ){
							$(elem).append('<necessary>هذا الحقل مطلوب</necessary>');
						}
					}else{
		  			//if( ValuesList[decodedArguments.id] == undefined ) ValuesList[decodedArguments.id] = [];
		  			ValuesList[decodedArguments.id] = CurrentValue;

						$(elem).find('necessary').remove();
					}

				});
				data['Values'] = ValuesList;

				if( InsertValidation != false ){

					EachArea.find('.-fix-inputs-area').each(function(k,elem){
						SetupFields($(elem),'',true);
					});

					var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
					$.ajax({
						url: AjaxURL,
						dataType: 'json',
						type: 'POST',
						data:data,
						success: function(msg) {
							AppendArea.append(msg.output);
							if( msg.CounterOutput != undefined ){
								AppendCounter.replaceWith(msg.CounterOutput);
							}else{
								AppendCounter.remove();
							}
							PinnedJQuery();
							InitializeFields();

						}
					});
				}

			});	

		// # data-edit-db-insert-item
			$('body').on("click",'[data-edit-db-insert-item ]',function() {
				var BUTTTON = $(this);
				var ParentItem = BUTTTON.closest('.--Js-nocss-important-db-item-class');
				var CurrentUpdatedID = BUTTTON.data('edit-db-insert-item');
				var FieldCenter = ParentItem.closest('.-fix-inputs-area');
				var Arguments = FieldCenter.data('vars');
				var UniD = ParentItem.data('db-uniq-itemid');

				var data = {
					'action':'Update__single_DB',
					'Arguments': Arguments,
					'update__id': CurrentUpdatedID,
				};

				var ValuesList = {};
				var InsertValidation = true;

				ParentItem.find('.-fix-inputs-area').each(function(k,elem){

					if( $(elem).data('field-argums') != undefined ){
						var ArgumsField = $(elem).data('field-argums');
					}
					// # DECODE FIELDS ARGUMENTS.
					var decodedArguments = $.base64.atob(ArgumsField,true);
					decodedArguments = jQuery.parseJSON( decodedArguments );

					var CurrentValue = GetFieldValue($(elem),decodedArguments);
					if( ( CurrentValue == false || CurrentValue == '' ) && decodedArguments.require != undefined && decodedArguments.require == true ){
						InsertValidation = false;
						if( $(elem).find('necessary').length == 0 ){
							$(elem).append('<necessary>هذا الحقل مطلوب</necessary>');
						}
					}else{
		  			ValuesList[decodedArguments.id] = CurrentValue;
						$(elem).find('necessary').remove();
					}
				});

				ParentItem.css({"pointer-events":'none',"opacity":'0.7'});
				data['Values'] = ValuesList;

				if( InsertValidation != false ){

					var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
					$.ajax({
						url: AjaxURL,
						dataType: 'json',
						type: 'POST',
						data:data,
						success: function(msg) {
							ParentItem.replaceWith(msg.output);
							PinnedJQuery();
							InitializeFields();							
						}
					});
				}
			});	

	  // # REMOVE RELATION TRANSLATE ITEM .
	  	$('body').on("click",'[data-remove-db-insert-item]',function() {
  			var BUTTTON = $(this);

  			if( BUTTTON.data('done-alert') != undefined ){
					var ParentItem = $('.--Js-nocss-important-db-item-class[data-db-uniq-itemid="'+BUTTTON.data('uniq')+'"][data-db-insert-itemid="'+BUTTTON.data('remove-db-insert-item')+'"]');

					var data = {
						'action':'remove__DB___relation',
						'remove__id':BUTTTON.data('remove-db-insert-item'),
						'Arguments':ParentItem.data('db-arguments'),
					};

	  			$('.Popver--CoursesAlert').remove();

					ParentItem.css({"pointer-events":'none',"opacity":'0.7'});
					var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
					$.ajax({
						url: AjaxURL,
						dataType: 'json',
						type: 'POST',
						data:data,
						success: function(msg) {
							ParentItem.css({"pointer-events":'auto',"opacity":'1'});
							if( msg.error == undefined ){
								ParentItem.remove();
							}
						}
					});
  			}else{
				  var alert__json = {
				    "headtitle":'تأكيد حذف العنصر ',
				    "alertcontent" : 'هل متأكد من حذف هذا العنصر من عناصر الترجمة الحالية ',
				    "ConfirmAttrs" : 'data-remove-db-insert-item="'+BUTTTON.data('remove-db-insert-item')+'" data-uniq="'+BUTTTON.data('uniq')+'" data-done-alert="true"',
				  }
				  RemoveAlert(alert__json);
  			}
	  	});

	// # POSTS PAGE EDITS 

		// # How TO POSTS IS SELECTED.
			function HowToCheckBoxNow(key){
				var List,Queue;
				Queue = 0;
				List = [];

				$('.-ScrollerCenter[data-uniqid="'+key+'"] > .-contain-MiniBox.selected').each(function(e,v){Queue++;
					List.push($(v).data('post-id'));
				});

				if(Queue > 0){
					$('[data-navs-actions="RemoveSelectAll"]').css({"pointer-events":'auto', "opacity":'1'});
					$('[data-navs-actions="RemoveAllSelected"]').css({"pointer-events":'auto', "opacity":'1'});
				}else{
					$('[data-navs-actions="RemoveSelectAll"]').css({"pointer-events":'none', "opacity":'0.5'});
					$('[data-navs-actions="RemoveAllSelected"]').css({"pointer-events":'none', "opacity":'0.5'});
				}	
			}

			$('body').on("click",'[data-selected-postactions]',function() {
				$(this).closest('.-contain-MiniBox').toggleClass('selected');
				HowToCheckBoxNow( $(this).data('uniqid') );
			});

		// # POST NAV ACTIONS.
			$('body').on("click",'[data-navs-actions]',function(){
				var BTN,Action,Uniq;
				BTN = $(this);
				Action = BTN.data('navs-actions');
				Uniq = BTN.data('uniqid');

				var List = [];

				$('.-ScrollerCenter[data-uniqid="'+Uniq+'"] > .-contain-MiniBox'+(( Action == 'RemoveAllSelected' ) ? '.selected' : '')).each(function(e,v){
					if( Action == 'SelectAll' ){
						$(v).addClass('selected');
					}else if( Action == 'RemoveSelectAll' ){
						$(v).removeClass('selected');			
					}else if( Action == 'RemoveAllSelected' ){
						List.push( $(v).data('post-id') );				
					}
				});

				if( Action == 'RemoveAllSelected' ){
					var Argums = List.join(',');
				  var Data = {
				    "headtitle":'هل تريد حذف '+List.length+' عنصر ؟ ',
				    "alertcontent" : 'هل تريد بالتأكيد حذف '+List.length+' نموذج ؟',
				    "ConfirmAttrs" : 'data-remove-post-id="'+Argums+'" data-alert="true" data-uniq="'+Uniq+'" data-location="stay"',
				  }
				  RemoveAlert(Data);
				}
				HowToCheckBoxNow(Uniq);
			});

		// # REMOVE POST BY ID.	
			$('body').on("click",'[data-remove-post-id]',function(e){e.preventDefault();
				var BTN,removedID,DataAlert,CommentElement;
				BTN = $(this);
				removedID = BTN.data('remove-post-id')+'';
				DataAlert = BTN.data('alert');

				//
				if(DataAlert != undefined && DataAlert != false ){
					var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
					var location = BTN.data('location');
					var IDList = [];
					if( removedID.indexOf(',') > -1 ){
						var r = ',';
						var x = $.trim(removedID.match(r).toString());
						var str_split = removedID.split(x);
						for (var i = 0; i < str_split.length; i++) {
							if( str_split[i] != undefined ){
		    				IDList.push($.trim(str_split[i]));
							}
						}
					}else{
						IDList.push( removedID );
					}
					AjaxRequest({
						url: AjaxURL,
						dataType: 'json',
						type : "POST",
						data: { "action":'remove-post',"removedID": removedID,"location":location},
						success: function(msg) {
							$('.Popver--CoursesAlert').remove();
				      $.each( IDList,function(e,fe){
				      	$('.-contain-MiniBox[data-post-id="'+fe+'"]').remove();
				      });

				      if( msg.reload__page != undefined ){
								setTimeout(function(){
									window.location.href = msg.reload__page;
								}, 300);
				      }

						}
					});
				}else{
				  var Data = {
				    "headtitle":'هل تريد حذف الطلب',
				    "alertcontent" : 'هل تريد بالتأكيد حذف هذا الطلب نهائياً؟',
				    "ConfirmAttrs" : 'data-remove-post-id="'+removedID+'" data-alert="true" data-location="'+( ( BTN.data('location') != undefined ) ? BTN.data('stay') : 'stay')+'"',
				  }
				  RemoveAlert(Data);
				}
			});

		// # REMOVE USER BY ID.	
			$('body').on("click",'[data-remove-user-id]',function(e){e.preventDefault();
				var BTN,removedID,DataAlert,CommentElement;
				BTN = $(this);
				removedID = BTN.data('remove-user-id')+'';
				DataAlert = BTN.data('alert');

				//
				if(DataAlert != undefined && DataAlert != false ){
					var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
					var location = BTN.data('location');
					var IDList = [];
					if( removedID.indexOf(',') > -1 ){
						var r = ',';
						var x = $.trim(removedID.match(r).toString());
						var str_split = removedID.split(x);
						for (var i = 0; i < str_split.length; i++) {
							if( str_split[i] != undefined ){
		    				IDList.push($.trim(str_split[i]));
							}
						}
					}else{
						IDList.push( removedID );
					}
					AjaxRequest({
						url: AjaxURL,
						dataType: 'json',
						type : "POST",
						data: { "action":'remove-user',"removedID": removedID,"location":location},
						success: function(msg) {
							$('.Popver--CoursesAlert').remove();
				      $.each( IDList,function(e,fe){
				      	$('.-contain-MiniBox[data-post-id="'+fe+'"]').remove();
				      });

				      if( msg.reload__page != undefined ){
								setTimeout(function(){
									window.location.href = msg.reload__page;
								}, 300);
				      }

						}
					});
				}else{
				  var Data = {
				    "headtitle":'هل تريد حذف العضوية',
				    "alertcontent" : 'هل تريد بالتأكيد حذف هذه العضوية نهائياً؟',
				    "ConfirmAttrs" : 'data-remove-user-id="'+removedID+'" data-alert="true" data-location="'+( ( BTN.data('location') != undefined ) ? BTN.data('stay') : 'stay')+'"',
				  }
				  RemoveAlert(Data);
				}
			});


	// # TAXONOMY INSERT POPOVER .	
		$('body').on("click",'[data-taxonomy-insert]',function() {
			var BTN,TaxArgums,UniqID;
			BTN = $(this);
			TaxArgums = BTN.data('taxonomy-insert');
			UniqID = BTN.data('uniq');

			if( $('.Popver--CoursesAlert[data-popover-uniqs="'+UniqID+'"]').length > 0 ){
				$('.Popver--CoursesAlert[data-popover-uniqs="'+UniqID+'"]').show();
			}else{
				var data = {
					'action':'InsertTaxonomy',
					'args':TaxArgums,
					'uniq':UniqID,
				};
				var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
				AjaxRequest({
					url: AjaxURL,
					dataType: 'json',
					type : "POST",
					data: data,
					success: function(msg) {
						$('body').append(msg.output);
					}
				});			
			}
		});

	// # LOADMORE ACTIONS.
		var LoadingMore = false;
		function MoreAjax(argument) {
			argument.append('<div class="-BioLoaded"><div class="loader"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/></svg></div></div>');
			LoadingMore = true;
			var Args = argument.data("loadmore");
			var data = {
				'action':'loadmore',
				'args':Args,
				'uniq':argument.data("uniqid"),
				'part':argument.data("part"),
			};

			var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';

			$.ajax({
				url: AjaxURL,
				dataType: 'json',
				type: 'POST',
				data:data,
				success: function(msg) {
					LoadingMore = false;
					$('.-BioLoaded').remove();				
					$('[data-more-click="'+argument.data("uniqid")+'"]').removeClass('isloader');

					if( argument.find('.NothingFoundFilter').length === 0 ){
						argument.append(msg.output);					
					}

					argument.data("loadmore", msg.arguments);

					if(msg.end != undefined && msg.end == true){
						argument.attr("data-finish",true);
						$('[data-more-click="'+argument.data("uniqid")+'"]').hide();
					}
				}
			});
		}

		// # More Click 
			$('body').on('click','[data-more-click]',function(e){e.preventDefault();
				var ScrollerCenter = $('.-ScrollerCenter[data-uniqid="'+$(this).data('more-click')+'"]');
				if(LoadingMore == false && ScrollerCenter.data('finish') == false ){
					$(this).addClass('isloader');
					MoreAjax(ScrollerCenter);
				}
			});

	// # YOURCOLOR CUSTOM FORM SUBMIT .
		$('body').on("submit", '[data-form-ajax]', function(){
			var FormElement = $(this);
			var data, submit, method, url;
			// Validate
			StopInsert = true;
			$(this).find('.-fix-inputs-area').each(function(k,el){
				if( $(el).data('vars') != undefined ){

					ElEach = CheckFieldsValue($(el));
					if( ElEach == false ){
						StopInsert = false;
						if( $(el).find('necessary').length == 0 ){
							$(el).append('<necessary>هذا الحقل مطلوب</necessary>');
						}
					}else{
						$(el).find('necessary').remove();					
					} 
				}
			});

			if( StopInsert != false ) {
				data = $(this).serialize();
				method = $(this).attr('method');

				if( $(this).data('for-action') != undefined ){
					url = HomeURL+'/wp-admin/admin-ajax.php';
					data = data +'&action='+$(this).attr('action');
				}else{
					url = $(this).attr('action');
				}
				//
				FormElement.addClass('Checking');
				$.ajax({
					url: url,
					dataType: 'json',
					data: data,
					type: method,
					success: function(msg) {
						if( FormElement.data('form-result') == 'beforeinsert_products' ){
							$('body').append(AlertMessage(msg));
							if(msg.type == "sucsses"){
								setTimeout(function(){
									window.location.href = msg.post_url;
								}, 500);
							}
						}else if( FormElement.data('form-result') == 'inset_term' ){
							FormElement.find('necessary').remove();
							FormElement.append('<necessary class="-type-alert-'+msg.alert+'">'+msg.message+'</necessary>');
							if( msg.output != undefined ){
								$('.-appender-tax-center[data-append-uniq="'+FormElement.data('uniq')+'"]').append( msg.output );
							}
							
							if( FormElement.closest('.Popver--CoursesAlert').length > 0 ){
								setTimeout(function(){
									FormElement.closest('.Popver--CoursesAlert').remove();
								}, 300);
							}

						}
					}
				});
			}
			return false;
		});

	// # POST TYPE TABS SWITCHING.
		$('body').on('click','[data-metaboxes-tab]',function(){
			if( !$(this).hasClass('active') ){
				$(this).addClass('active').siblings().removeClass('active');
				// # ALL ITEMS
					var ALL_MetaBoxes = $(this).parent().data('total-metabox');
					ALL_MetaBoxes = $.base64.atob(ALL_MetaBoxes,true);
					ALL_MetaBoxes = jQuery.parseJSON( ALL_MetaBoxes );
					$(ALL_MetaBoxes).hide();

				// # REMOVE ITEMS
					var RemoveItems = $(this).parent().data('remove-metabox');
					RemoveItems = $.base64.atob(RemoveItems,true);
					RemoveItems = jQuery.parseJSON( RemoveItems );
					$(RemoveItems).remove();

				// # ACTIVE ITEM .
					var Active__Items = $(this).data('metaboxes-tab');
					Active__Items = $.base64.atob(Active__Items,true);
					Active__Items = jQuery.parseJSON( Active__Items );
					$(Active__Items).show();
			}

		});

	// # DATA BUTTONS ACTIONS .
	  $('body').on('click', '[data-button]', function(e){ e.preventDefault();
	    var Button = $(this);

	    //  # POPOVER ACTION .
	      if( Button.data( 'button' ) == 'Orders--Actions' ){
	        OrdersActions( $(this) );
	      }

	  });
	  function OrdersActions( elem ) {
	  	var Button = elem;
			var Args = Button.data("argument");
			var data = {
				'action':'orders__actions',
				'args':Args
			};
			var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
			$.ajax({
				url: AjaxURL,
				dataType: 'json',
				type: 'POST',
				data:data,
				success: function(msg) {
					if( msg.alert != undefined ){
						location.reload();
					}
				}
			});
	  }

// # YC Slider Events 
  $('body').on('click','[data-navs]',function(){
    var BtnNav,NavType,MangerEvent,MyUlEvents,BxWidth;
    BtnNav = $(this);
    NavType = BtnNav.data('navs');
    MangerEvent = BtnNav.parent();

    if(MangerEvent.data('sliderclass') != undefined && MangerEvent.data('sliderclass') != ''){
      var FindeoEl = MangerEvent.data('sliderclass');
      MyUlEvents = MangerEvent.find(FindeoEl);
    }else{
      if(MangerEvent.data('ulfind') != undefined){
        MyUlEvents = MangerEvent.find(MangerEvent.data('ulfind'));
      }else{
        MyUlEvents = MangerEvent.find('ul');
      }      
    }
    //BxWidth = MyUlEvents.find("li:first-child").width();
    BxWidth = MangerEvent.width() - 15;
    if(NavType == 'right'){
      MyUlEvents.animate({scrollLeft: "+="+BxWidth+"px"}, "slow");
    }else if(NavType == 'left'){
      MyUlEvents.animate({scrollLeft: "-="+BxWidth+"px"}, "slow");
    }
  });
  var YcSliderEvents = function(){
    var MyUlWidth,TotalCount,ItemWidth;
    $('[data-customslider]').each(function(hmada, ylab){
      if($(ylab).data('customslider') != undefined && $(ylab).data('customslider') != false){
        TotalCount = 0;
        MyUlWidth = $(ylab).width();
        //alert(MyUlWidth);
        if($(ylab).data('ulfind') != undefined){
          MangerUl = $(ylab).find($(ylab).data('ulfind'));
          Elemntfind = $(ylab).data('findelements');
        }else{
          MangerUl = $(ylab).find('ul');
          Elemntfind = 'li';
        }
        $(MangerUl).find(Elemntfind).each(function(kol, myli){
          TotalCount = TotalCount + $(myli).width();
        }); 
        if(TotalCount > MyUlWidth){
          $(ylab).append('<YcSliderEvents class="ScrollRight" data-navs="right"><i class="fa-solid fa-arrow-right"></i></YcSliderEvents><YcSliderEvents class="ScrollLeft" data-navs="left"><i class="fa-solid fa-arrow-left"></i></YcSliderEvents>');
          $(ylab).data('customslider',false);
          $(ylab).addClass('ActivableSlider');
        }
      }
    });
  }
  YcSliderEvents();
  $(window).on('resize', YcSliderEvents);
  $(window).on('load', YcSliderEvents);
  $(window).ajaxSuccess(YcSliderEvents);



	// # ( POST && TAONOMY && USER ) CUSTOM SELECT FIELD ACTIONS .

	  // # SELECT ITEM
	  	$('body').on('click','[data-select-custom-items]',function(){
	  		var BUTTTON = $(this);
	  		var CurrentClone = BUTTTON.parent().clone();
	  		var elementID = BUTTTON.data('select-custom-items');
	  		var multiple = BUTTTON.data('multiple');
	  		var HideInput = BUTTTON.data('hide-input');
				var ParentButton__center = BUTTTON.closest('.-ScrollerCenter');
				var ParentButton__UniqID = ParentButton__center.data('uniqid');
				var AppendCenter = $('.--JS-result--select[data-uniqid="'+ParentButton__UniqID+'"]');
				var decode__HideInput = atob(HideInput);
				decode__HideInput = jQuery.parseJSON( decode__HideInput );


				if( multiple != undefined && multiple == true ){
					BUTTTON.parent().toggleClass('active');
				}else{
					var Ps = BUTTTON.parent();
					Ps.addClass('active').siblings().removeClass('active');
					ParentButton__center.hide();
				}

				if( BUTTTON.parent().hasClass('active') ){
					CurrentClone.append(decode__HideInput);
					CurrentClone.addClass('active');
					if( AppendCenter.length > 0 ){
						if( multiple != undefined && multiple == true ){
							AppendCenter.find('.--JS--Appended--Selector').append(CurrentClone);
							YcSliderEvents();
						}else{
							AppendCenter.find('.--JS--Appended--Selector').html(CurrentClone);
						}
					}
				}else{
					var s__find = AppendCenter.find('.--JS--Appended--Selector [data-select-custom-items]');
					s__find.each(function(e,elQuery) {
						if( $(elQuery).data('select-custom-items') == elementID ) $(elQuery).parent().remove();
					});					
				}

				// # FOUND OTHER ITEMS . 
					if( AppendCenter.find('.--JS--Appended--Selector').html() == '' ){
						AppendCenter.hide();
					}else{
						AppendCenter.show();
					}
	  	});	

	  // # REMOVE ITEM 
	  	$('body').on("click",'[data-remove-compo-select-item]',function() {
	  		var parent__element = $(this).closest('.--JS-result--select');
	  		var Uniq = parent__element.data('uniqid');
	  		$(this).parent().remove();

	  		SetActiveSelectedCompo( $('.--JS-result--select[data-uniqid="'+Uniq+'"]') ,$('.-ScrollerCenter[data-uniqid="'+Uniq+'"]') , true );
				// # FOUND OTHER ITEMS . 
					if( $('.--JS-result--select[data-uniqid="'+Uniq+'"]').find('.--JS--Appended--Selector').html() == '' ){
						$('.--JS-result--select[data-uniqid="'+Uniq+'"]').hide();
					}else{
						$('.--JS-result--select[data-uniqid="'+Uniq+'"]').show();
					}	  		
	  	});

	  
	  // # SELECT ITEM TRSNALTER TOOLS 
	  	$('body').on('click','[data-select-custom-ajax]',function(){
	  		var BUTTTON = $(this);
	  		var connecter__id = BUTTTON.data('select-custom-ajax');
	  		// # 
				var ParentButton__center = BUTTTON.closest('.-ScrollerCenter');
				var ParentButton__UniqID = ParentButton__center.data('uniqid');
				// # 
				var AppendCenter = $('.-result-searching-translate-ajax[data-uniqid="'+ParentButton__UniqID+'"]');
				var translate_relation__post__ajaxData = AppendCenter.data('argums');
				// # 
				var AlertCenter = $('.--translate--append-alerts[data-uniqid="'+ParentButton__UniqID+'"]');

				ParentButton__center.css({"pointer-events":'none',"opacity":'0.7'});

				var data = {
					'action':'translate___post_select',
					'args':translate_relation__post__ajaxData,
					'connecter__id':connecter__id
				};

				var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
				$.ajax({
					url: AjaxURL,
					dataType: 'json',
					type: 'POST',
					data:data,
					success: function(msg) {
						var Show__SearchingCenter = true;
						if( msg.error != undefined ){

							if( msg.error.alert != false  ){
								AlertCenter.removeClass('--sucsses--bins');
								Show__SearchingCenter = false;

							  var alert__json = {
							    "headtitle":msg.error.title,
							    "alertcontent" : msg.error.message,
							    "ConfirmAttrs" : 'data-translate-select-alerts="'+msg.error.connecter__id+'" data-translate-select-argums="'+msg.error.args+'" data-uniq="'+ParentButton__UniqID+'"',
							  }
							  RemoveAlert(alert__json);

							}else{
								AlertCenter.html(msg.error.message);
							}
						}	

						if( msg.output != undefined ){
							AlertCenter.addClass('--sucsses--bins');
							AlertCenter.html(msg.message);
							AppendCenter.find('.--relations--current--obj--list').html(msg.output);
							AppendCenter.find('.-Your-selected-title > em').html(msg.counter);
							if( msg.counter > 0 ){
								AppendCenter.show();
							}else{
								AppendCenter.hide();
							}
						}


						ParentButton__center.css({"pointer-events":'auto',"opacity":'1'});

					}
				});
	  	});

	  // # ALERT TO AJAX SELECT ITEM TRSNALTER TOOLS 
	  	$('body').on('click','[data-translate-select-alerts]',function(){
	  		var BUTTTON = $(this);
	  		var connecter__id = BUTTTON.data('translate-select-alerts');
	  		var translate_relation__post__ajaxData = BUTTTON.data('translate-select-argums');
	  		var UnID = BUTTTON.data('uniq');
	  		// # 
				var ParentButton__searching_center = $('.-ScrollerCenter[data-uniqid="'+UnID+'"]');
				// # 
				var AppendCenter = $('.-result-searching-translate-ajax[data-uniqid="'+UnID+'"]');
				// # 
				var AlertCenter = $('.--translate--append-alerts[data-uniqid="'+UnID+'"]');

				ParentButton__searching_center.css({"pointer-events":'none',"opacity":'0.7'});
	  		$('.Popver--CoursesAlert').remove();

				var data = {
					'action':'translate___post_select',
					'args':translate_relation__post__ajaxData,
					'connecter__id':connecter__id
				};

				var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
				$.ajax({
					url: AjaxURL,
					dataType: 'json',
					type: 'POST',
					data:data,
					success: function(msg) {
						var Show__SearchingCenter = true;
						if( msg.error != undefined ){

							if( msg.error.alert != false  ){
								AlertCenter.removeClass('--sucsses--bins');
								Show__SearchingCenter = false;

							  var alert__json = {
							    "headtitle":msg.error.title,
							    "alertcontent" : msg.error.message,
							    "ConfirmAttrs" : 'data-translate-select-alerts="'+msg.error.connecter__id+'" data-translate-select-argums="'+msg.error.args+'" data-uniq="'+UnID+'"',
							  }
							  RemoveAlert(alert__json);

							}else{
								AlertCenter.html(msg.error.message);
							}
						}	

						if( msg.output != undefined ){
							AlertCenter.addClass('--sucsses--bins');
							AlertCenter.html(msg.message);
							AppendCenter.find('.--relations--current--obj--list').html(msg.output);
							AppendCenter.find('.-Your-selected-title > em').html(msg.counter);
							if( msg.counter > 0 ){
								AppendCenter.show();
							}else{
								AppendCenter.hide();
							}
						}

						ParentButton__searching_center.css({"pointer-events":'auto',"opacity":'1'});
					}
				});
	  	});
	// # REMOVE RELATION TRANSLATE ITEM .
	  	$('body').on("click",'[data-remove-translate-select-item]',function() {
  			var BUTTTON = $(this);

  			if( BUTTTON.data('done-alert') != undefined ){
					var data = {
						'action':'remove__translate___relation_select',
						'remove__id':BUTTTON.data('remove-translate-select-item'),
					};

	  			$('.Popver--CoursesAlert').remove();

					var AppendCenter = $('.-result-searching-translate-ajax[data-uniqid="'+BUTTTON.data('uniq')+'"]');
					AppendCenter.css({"pointer-events":'none',"opacity":'0.7'});
					var AjaxURL = HomeURL+'/wp-admin/admin-ajax.php';
					$.ajax({
						url: AjaxURL,
						dataType: 'json',
						type: 'POST',
						data:data,
						success: function(msg) {
							AppendCenter.css({"pointer-events":'auto',"opacity":'1'});

							if( msg.output != undefined && msg.output != '' ){
								AppendCenter.find('.--relations--current--obj--list').html(msg.output);
							}else{
								AppendCenter.find('.--relations--current--obj--list').html('');
							}

							if( msg.counter != undefined &&  msg.counter > 0 ){
								AppendCenter.find('.-Your-selected-title > em').html(msg.counter);
								AppendCenter.show();
							}else{
								AppendCenter.hide();
							}
						}
					});
  			}else{
  				var AppendCenter = BUTTTON.closest('.-result-searching-translate-ajax');
				  var alert__json = {
				    "headtitle":'تأكيد حذف العنصر ',
				    "alertcontent" : 'هل متأكد من حذف هذا العنصر من عناصر الترجمة الحالية ',
				    "ConfirmAttrs" : 'data-remove-translate-select-item="'+BUTTTON.data('remove-translate-select-item')+'" data-done-alert="true" data-uniq="'+AppendCenter.data('uniqid')+'"',
				  }
				  RemoveAlert(alert__json);
  			}
	  	});

	// # TOGGLE SINGLE GROUP
	  	$('body').on("click",'[data-toggle-singlegroup]',function(e) {e.preventDefault();
	    	var BTN,MasterQuestion,TogggleContent,ToggleValue,ContnetHeight;
	    	BTN = $(this);
		    MasterQuestion = BTN.parent();
		    TogggleContent = MasterQuestion.find('.-Toggle-Content');
		    ToggleValue = TogggleContent.find('.-ToggleContentValue');
		    ContnetHeight = ToggleValue.outerHeight();
		    TogggleContent.css({"--pin-height":ContnetHeight+'px'});
		    if( BTN.data('toggle-off') != undefined  ){
		      MasterQuestion.toggleClass("active");
		    }else{
		      MasterQuestion.toggleClass("active").siblings().removeClass("active");
		    }
	  	});

// # copyToClipboard
  function copyToClipboard(text) {

     var textArea = document.createElement( "textarea" );
     textArea.value = text;
     document.body.appendChild( textArea );       
     textArea.select();

     try {
        var successful = document.execCommand( 'copy' );
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Copying text command was ' + msg);
     } catch (err) {
        console.log('Oops, unable to copy',err);
     }    
     document.body.removeChild( textArea );
  }
	  	
// # SINGLE SHARE COPY CONTENT
  $("body").on("click", '[data-copy-action]', function(e){e.preventDefault();
    var This = $(this);
    clipboardText = This.parent().find('input').val(); 
    copyToClipboard( clipboardText );
    FastAlerts('<i class="fa-solid fa-circle-check"></i><span>تم نسخ الكود بنجاح </span><em class="notifications--tempo--close"><i class="fas fa-times"></i></em>');
  });


});


