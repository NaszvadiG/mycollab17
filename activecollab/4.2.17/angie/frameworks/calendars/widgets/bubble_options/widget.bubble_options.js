(function($){var public_methods={init:function(options){return this.each(function(){this.options=options;initialize_options.apply(this);initialize_visibility_control.apply(this)})}};function initialize_options(){var object=$(this);var height=object.innerHeight();var data=this.options;var ul=$("<ul></ul>");$.each(data,function(index,subitem){if(subitem.text&&subitem.url){var item=$('<li><a href="'+App.clean(subitem.url)+'" title="'+App.clean(subitem.text)+'">'+App.clean(subitem.text)+"</a></li>");item.appendTo(ul);var link=item.find("a:first");if(subitem.icon){link.css("background-image","url("+subitem.icon+")")}if(subitem["class"]){link.addClass(subitem["class"])}if(typeof(subitem.onclick)=="string"&&subitem.onclick){var onclick;eval("onclick = "+subitem.onclick);if(typeof(onclick)=="function"){onclick.apply(link[0])}}}});var drop_down=$('<div class="bubble_object_options_drop_down"></div>');var title=$("<span>"+App.lang("Options")+"</span>");var bubble_options=object.find("ul.bubble_options");var option=$('<li class="bubble_object_options"></li>');drop_down.css("max-height",height-bubble_options.outerHeight()+"px").append(ul).hide();option.append(title).append(drop_down);bubble_options.prepend(option)}function initialize_visibility_control(){var object=$(this);var option=object.find("li.bubble_object_options");var drop_down=option.find("div.bubble_object_options_drop_down");var timeout;option.hover(function(){drop_down.show()},function(){drop_down.hide()})}var plugin_name="bubbleOptions";var settings={};$.fn[plugin_name]=function(method){if(public_methods[method]){return public_methods[method].apply(this,Array.prototype.slice.call(arguments,1))}else{if(typeof method==="object"||!method){return public_methods.init.apply(this,arguments)}else{$.error("Method "+method+" does not exist in jQuery."+plugin_name)}}}})(jQuery);