App.widgets.InlineTabs=function(){var wrapper;var inline_tab_anchors;var inline_loader;var inline_content;return{init:function(wrapper_id){var wrapper=$("#"+wrapper_id);var inline_tab_anchors=wrapper.find(".inline_tabs_links a");var inline_loader=wrapper.find(".inline_tabs_loader");var inline_content=wrapper.find(".inline_tabs_content");var inline_content_wrapper=wrapper.find(".inline_tabs_content_wrapper");wrapper.attr("event_scope",".inline_tab");var wrapper_quick_view=wrapper.parents(".quick_view_card:first");if(wrapper_quick_view.length){wrapper.attr("event_scope",".inline_tab."+wrapper_quick_view.attr("id"))}inline_tab_anchors.each(function(){var anchor=$(this);anchor.click(function(){if(anchor.data("already_loading")){return false}anchor.data("already_loading",true);inline_tab_anchors.removeClass("selected");anchor.addClass("selected");var loader_height=80;App.Wireframe.Events.unbind(wrapper.attr("event_scope"));inline_content.animate({height:loader_height,opacity:0},300,function(){inline_content.hide();inline_loader.css("height",loader_height+"px").css("opacity",0).show().animate({opacity:1},300,function(){$.ajax({type:"get",url:App.extendUrl(anchor.attr("href"),{skip_layout:1}),success:function(response){inline_content.css("height","auto").html(response);inline_loader.animate({height:inline_content.height()},300,function(){inline_loader.animate({opacity:0},300,function(){inline_loader.hide();inline_content.css("opacity",0).show().animate({opacity:1},300,function(){anchor.data("already_loading",false)})})})},error:function(response){App.Wireframe.Flash.error(App.lang("Failed to load :inline_tab page",{inline_tab:anchor.text()}));anchor.data("already_loading",false).removeClass("selected");inline_content.html("")}})})});return false})});inline_tab_anchors.eq(0).click()},refresh:function(wrapper_id){var wrapper=$("#"+wrapper_id);var inline_content=wrapper.find(".inline_tabs_content");var current_anchor=wrapper.find("div.inline_tabs_links a.selected");if(current_anchor.length){$.ajax({url:App.extendUrl(current_anchor.attr("href"),{skip_layout:1}),success:function(response){App.Wireframe.Events.unbind(wrapper.attr("event_scope"));inline_content.html(response)}})}},updateCount:function(wrapper_id,specific_tab,count){var wrapper=$("#"+wrapper_id);if(wrapper.is(".inline_tabs")){var inline_tabs=wrapper}else{var inline_tabs=wrapper.find(".inline_tabs:first")}if(specific_tab){var current_count=wrapper.find("div.inline_tabs_links a#"+inline_tabs.attr("id")+"_"+specific_tab+" span")}else{var current_count=wrapper.find("div.inline_tabs_links a.selected span")}if(current_count.length){current_count.html(count)}}}}();