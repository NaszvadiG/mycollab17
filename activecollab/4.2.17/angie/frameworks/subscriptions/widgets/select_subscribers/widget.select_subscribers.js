jQuery.fn.selectSubscribers=function(s){var settings=jQuery.extend({},s);return this.each(function(){var wrapper=$(this);wrapper.find("div.user_group li").each(function(){var list_element=$(this);if($.inArray(list_element.attr("user_id"),s.can_see_private)!==-1){list_element.attr("can_see_private",1)}else{list_element.attr("can_see_private",0)}});var filter_by_visibility=function(visibility){var wrapper=$(this);wrapper.find("div.user_group").each(function(){var group=$(this);group.show();if(visibility==1){group.find("li").show()}else{group.find('li[can_see_private="0"]').hide();group.find('li[can_see_private="1"]').show()}if(group.find("li:visible").length){group.show()}else{group.hide()}});wrapper.find('input[type="checkbox"]:not(:visible):checked').attr("checked",false)};this.filterByVisibility=filter_by_visibility;if(settings.object_type=="discussion"){var container=$('<div class="multiple_select_notifiers"></div>');$("<span>"+App.lang("Select")+":</span>").prependTo(container);$('<a href="#">'+App.lang("All")+"</a>").click(function(){wrapper.find('input[type="checkbox"]:visible').attr("checked","checked")}).appendTo(container);$('<a href="#">'+App.lang("None")+"</a>").click(function(){wrapper.find('input[type="checkbox"]:visible').removeAttr("checked")}).appendTo(container);wrapper.after(container)}})};