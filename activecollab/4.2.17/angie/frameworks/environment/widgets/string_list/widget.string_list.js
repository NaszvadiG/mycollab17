jQuery.fn.stringList=function(s){var reindex_row_data=function(wrapper){var counter=1;wrapper.find("tr.item").each(function(){var row=$(this);row.removeClass("even").removeClass("odd");if((counter%2)>0){row.addClass("odd")}else{row.addClass("even")}row.find("td.num").text("#"+counter);counter++})};return this.each(function(){var wrapper=$(this);wrapper.find("a.add_list_item_button").click(function(e){var new_category=jQuery.trim(prompt(App.lang("Please provide new item"),""));if(typeof(new_category)=="string"&&new_category){if(new_category.length<3){App.Wireframe.Flash.error("Value should be at least 3 characters long")}else{var does_no_exist=true;wrapper.find("tr.item").each(function(){var row=$(this);if(row.find("input[type=hidden]").val().toLowerCase()==new_category.toLowerCase()){does_no_exist=false;row.find("td.value").highlightFade()}});if(does_no_exist){var row=$('<tr class="item"><td class="num">#</td><td class="value"><span></span> <sup>'+App.lang("Unsaved")+'</sup> <input type="hidden" /></td><td class="remove"><a href="#"><img src="'+App.Wireframe.Utils.imageUrl("/icons/12x12/delete.png","environment")+"></a></td></tr>");row.find("td.value span").text(new_category);row.find("input[type=hidden]").val(new_category).attr("name",wrapper.attr("string_list_name")+"[]");wrapper.find("table").append(row);row.find("td").highlightFade();reindex_row_data(wrapper)}}}return false});wrapper.delegate("tr.item td.remove a","click",function(){$(this).parent().parent().remove();reindex_row_data(wrapper);return false})})};