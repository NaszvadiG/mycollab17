(function($){var public_methods={init:function(options){return this.each(function(){var wrapper=$(this);var project_time_expenses_wrapper=wrapper.parents(".project_time_expenses_wrapper");this.tel_data={settings:jQuery.extend({},options)};if(!this.tel_data.settings.initial_data||!this.tel_data.settings.initial_data.length){project_time_expenses_wrapper.hide()}else{project_time_expenses_wrapper.siblings("#empty_slate_time_expenses").hide()}render_initial_wireframe.apply(this);handle_interaction.apply(this);handle_events.apply(this);batch_import.apply(this,[this.tel_data.settings.initial_data,this.tel_data.settings.parent_tasks])})}};var render_initial_wireframe=function(){var wrapper=$(this);if(this.tel_data.settings.can_manage_items){$(this).append('<p class="mass_update_status">'+App.lang("Select Multiple Records to Change their Status")+"</p>")}};var batch_import=function(items,parent_tasks){var wrapper=$(this);var wrapper_dom=this;if(!items||!items.length){return false}App.each(items,function(index,item){var parent_task=typeof(parent_tasks)=="object"&&parent_tasks&&item.parent_type=="Task"&&typeof(parent_tasks[item.parent_id])=="object"?parent_tasks[item.parent_id]:null;add_item.apply(wrapper_dom,[item,parent_task])})};var add_item=function(item,parent_task){var item_type=item["class"].toLowerCase();var row=$('<tr class="item time_record" record_date="'+item.record_date["formatted_date_gmt"]+'" record_date_timestamp="'+item.record_date["timestamp"]+'" record_type="'+item["class"]+'" record_id="'+item.id+'"><td class="value"></td><td class="icon"><img src="" alt="" /></td><td class="user"></td><td class="description"></td><td class="status"></td><td class="options"></td>'+(this.tel_data.settings.can_manage_items?'<td class="mass_update_checkbox"><input type="checkbox" value="mass_update_tracking_objects" value="'+item.id+'"></td>':"")+"</tr>");if(item_type=="timerecord"){row.find("td.icon img:first").attr({src:App.Wireframe.Utils.imageUrl("icons/16x16/time-record.png","tracking"),alt:App.lang("Time Record")});row.find("td.value").append(App.hoursFormat(item.value))}else{if(item_type=="expense"){row.find("td.icon img:first").attr({src:App.Wireframe.Utils.imageUrl("icons/16x16/expense.png","tracking"),alt:App.lang("Expense")});row.find("td.value").append(App.moneyFormat(item.value,this.tel_data.settings.currency_code))}}App.Wireframe.Utils.userLink(item.user,true).appendTo(row.find("td.user"));var parent_task_text=parent_task?'<a href="'+App.clean(parent_task.url)+'" class="quick_view_item">#'+parent_task.task_id+": "+App.clean(parent_task.name)+"</a>":"";var summary_text=item.summary?App.clean(item.summary):"";if(parent_task&&parent_task.is_completed){row.find("td.description").addClass("completed")}if(parent_task_text&&summary_text){row.find("td.description").append(parent_task_text+" &mdash; "+summary_text)}else{if(parent_task_text){row.find("td.description").append(parent_task_text)}else{if(summary_text){row.find("td.description").append(summary_text)}}}switch(item.billable_status){case 1:var verbose_billable_status=App.lang("Billable");break;case 2:var verbose_billable_status=App.lang("Pending Payment");break;case 3:var verbose_billable_status=App.lang("Paid");break;default:var verbose_billable_status=App.lang("Not Billable");break}row.find("td.status").text(verbose_billable_status);if(item.permissions["can_edit"]){$('<a><img src="'+App.Wireframe.Utils.imageUrl("/icons/12x12/edit.png","environment")+'" /></a>').attr("href",item.urls["edit"]).addClass("edit").appendTo(row.find("td.options"))}if(item.permissions["can_trash"]){row.find("td.options").append(" ");$('<a><img src="'+App.Wireframe.Utils.imageUrl("/icons/12x12/move-to-trash.png","system")+'" /></a>').attr("href",item.urls["trash"]).addClass("trash").appendTo(row.find("td.options"))}find_day_table.apply(this,[item.record_date]).find("tbody").prepend(row)};var find_day_table=function(date){var wrapper=$(this);var wrapper_dom=this;var day_table=wrapper.find("table[date_timestamp="+date.timestamp+"]");if(day_table.length){return day_table}var colspan=this.tel_data.settings.can_manage_items?7:6;var day_table=$('<table class="common date_wrapper" cellspacing="0" date_timestamp="'+date.timestamp+'"><thead><tr><td colspan="'+colspan+'" class="date">'+date.formatted_date_gmt+"</td></tr></thead><tbody></tbody>");var date_table_added=false;wrapper.find("table.date_wrapper").each(function(){if(parseInt($(this).attr("date_timestamp"))<date.timestamp){wrapper.prepend(day_table);date_table_added=true;return false}});if(!date_table_added){wrapper.append(day_table)}return day_table};var create_working_row=function(){return $('<tr class="working_row"><td colspan="5"><img src="'+App.Wireframe.Utils.indicatorUrl("small")+'" alt="" /> '+App.lang("Working")+"</td></tr>")};var init_item_form_row=function(row,on_valid,on_success,on_error,on_cancel){var form=row.find("form");var summary_field=form.find("div.subtask_summary input[type=text]");form.find("input").keydown(function(e){if(e.keyCode==27){on_cancel.apply(form[0])}});form.find("a.item_form_cancel").click(function(){on_cancel.apply(form[0]);return false});form.submit(function(){if(row.is("tr.time_record_form")){var data={"time_record[user_id]":form.find("div.time_record_user select").val(),"time_record[value]":jQuery.trim(form.find("div.time_record_value input").val()),"time_record[job_type_id]":form.find("div.time_record_value select").val(),"time_record[record_date]":jQuery.trim(form.find("div.time_record_date input").val()),"time_record[summary]":jQuery.trim(form.find("div.time_record_summary input").val()),"time_record[billable_status]":form.find("div.time_record_billable select").val(),submitted:"submitted"};if(data["time_record[value]"]==""){form.find("div.time_record_value input").focus();return false}if(data["time_record[record_date]"]==""){form.find("div.time_record_date input").focus();return false}}else{var data={"expense[user_id]":form.find("div.expense_user select").val(),"expense[value]":jQuery.trim(form.find("div.expense_value input").val()),"expense[category_id]":jQuery.trim(form.find("div.expense_value select").val()),"expense[record_date]":jQuery.trim(form.find("div.expense_date input").val()),"expense[summary]":jQuery.trim(form.find("div.expense_summary input").val()),"expense[billable_status]":form.find("div.expense_billable select").val(),submitted:"submitted"};if(data["expense[value]"]==""){form.find("div.expense_value input").focus();return false}if(data["expense[record_date]"]==""){form.find("div.expense_date input").focus();return false}}on_valid();$.ajax({url:form.attr("action"),type:"post",data:data,success:function(response){on_success(response)},error:function(response){on_error(response)}});return false})};var show_item_form=function(form){form.show("fast",function(){var attributes_width=0;form.find("div.item_attribute").each(function(){if(!$(this).is("div.item_summary_wrapper")){attributes_width+=$(this).width()}});form.find("div.item_summary_wrapper input").width(form.width()-attributes_width-56);form.find("div.item_value_wrapper input").focus()})};var update_tables=function(){var wrapper=$(this);var wrapper_dom=this;wrapper.find("table.date_wrapper").each(function(){var date_wrapper=$(this);if(date_wrapper.find("tr.item").length<1){date_wrapper.remove()}})};var handle_interaction=function(){var wrapper=$(this);var wrapper_dom=this;wrapper.delegate("p.mass_update_status a","click",function(event){var selected_status=parseInt($(this).attr("billable_status"));if(selected_status<0&&selected_status>3){App.Wireframe.Flash.error("Operation failed");return false}switch(selected_status){case 0:var verbose_billable_status=App.lang("Not Billable");break;case 1:var verbose_billable_status=App.lang("Billable");break;case 2:var verbose_billable_status=App.lang("Pending Payment");break;case 3:var verbose_billable_status=App.lang("Paid");break}var selected_items=wrapper.find("tr.item.selected");if(selected_items.length>0){if(confirm(App.lang("Are you sure that you want to update selected records?"))){var time_records=[];var expenses=[];selected_items.each(function(){var row=$(this);if(row.attr("record_type")=="TimeRecord"){time_records.push(parseInt(row.attr("record_id")))}else{expenses.push(parseInt(row.attr("record_id")))}});var mass_update_paragraph=wrapper.find("p.mass_update_status");var prev_paragraph_html=mass_update_paragraph.html();mass_update_paragraph.html(App.lang("Updating selected time records and expenses..."));wrapper.find("tr.item td.mass_update_checkbox input").prop("disabled",true);var unblock=function(){mass_update_paragraph.html(prev_paragraph_html);wrapper.find("tr.item td.mass_update_checkbox input").prop("disabled",false)};$.ajax({url:wrapper_dom.tel_data.settings.mass_update_url,type:"post",data:{time_record_ids:time_records.join(","),expense_ids:expenses.join(","),new_billable_status:selected_status,submitted:"submitted",},success:function(response){unblock();var updated_records=0;if(typeof(response)=="object"&&(App.isset(response.updated_time_records)||App.isset(response.updated_expenses))){if(jQuery.isArray(response.updated_time_records)){App.each(response.updated_time_records,function(k,v){wrapper.find("tr.item.selected[record_type=TimeRecord][record_id="+v+"] td.status").html(verbose_billable_status);updated_records++})}if(jQuery.isArray(response.updated_expenses)){App.each(response.updated_expenses,function(k,v){wrapper.find("tr.item.selected[record_type=Expense][record_id="+v+"] td.status").html(verbose_billable_status);updated_records++})}}if(updated_records==1){App.Wireframe.Flash.success("One record has been updated")}else{App.Wireframe.Flash.success(":num records have been updated",{num:updated_records})}},error:function(){App.Wireframe.Flash.error("Operation failed");unblock()}})}}return false});wrapper.delegate("td.mass_update_checkbox input[type=checkbox]","click",function(event){if(this.checked){$(this).parent().parent().addClass("selected")}else{$(this).parent().parent().removeClass("selected")}if(wrapper.find("tr.item.selected").length>0){wrapper.find("p.mass_update_status").html(App.lang("Change Status")+': <a href="#" billable_status="0">'+App.lang("Not Billable")+'</a> &middot; <a href="#" billable_status="1">'+App.lang("Billable")+'</a> &middot; <a href="#" billable_status="2">'+App.lang("Pending Payment")+'</a> &middot; <a href="#" billable_status="3">'+App.lang("Paid")+"</a>")}else{wrapper.find("p.mass_update_status").html(App.lang("Select Multiple Records to Change their Status"))}});wrapper.delegate("td.options a.edit","click",function(event){var link=$(this);var row=link.parents("tr:first");var working_row=create_working_row();row.hide().after(working_row);$.ajax({url:link.attr("href"),type:"get",success:function(response){var edit_row=$(response);working_row.after(edit_row).remove();init_item_form_row(edit_row,function(){edit_row.remove();working_row=create_working_row();row.after(working_row)},function(record){working_row.remove();var date_timestamp=parseInt(row.attr("record_date_timestamp"));var current_date_wrapper=row.parent().parent();row.attr({record_date:record.record_date["formatted_date_gmt"],record_date_timestamp:record.record_date["timestamp"]});App.Wireframe.Utils.userLink(record.user,true).appendTo(row.find("td.user").empty());if(record["class"]=="Expense"){row.find("td.value").empty().text(App.moneyFormat(record.value,record.currency["code"]))}else{row.find("td.value").empty().text(App.hoursFormat(record.value))}if(typeof(record.parent)=="object"&&record.parent&&record.parent["class"]=="Task"){var parent_task_text='<a href="'+App.clean(record.parent["urls"]["view"])+'">#'+record.parent["task_id"]+": "+App.clean(record.parent["name"])+"</a>"}else{var parent_task_text=""}var summary_text=record.summary?App.clean(record.summary):"";if(parent_task_text&&summary_text){row.find("td.description").empty().append(parent_task_text+" &mdash; "+summary_text)}else{if(parent_task_text){row.find("td.description").empty().append(parent_task_text)}else{if(summary_text){row.find("td.description").empty().append(summary_text)}}}row.find("td.status").empty().text(record.billable_status_verbose);var new_date_timestamp=parseInt(row.attr("record_date_timestamp"));if(date_timestamp!=current_date_wrapper.attr("date_timestamp")){var new_date_wrapper=wrapper.find("table[date_timestamp="+new_date_timestamp+"]");if(new_date_wrapper.length==0){new_date_wrapper=create_date_table(row.attr("record_date"),new_date_timestamp)}new_date_wrapper.find("tbody").prepend(row);update_tables.apply(wrapper_dom)}row.show().find("td").highlightFade();if(record["class"]=="Expense"){App.Wireframe.Content.trigger("project_expense_updated",[record])}else{App.Wireframe.Content.trigger("project_time_updated",[record])}},function(response){working_row.remove();row.show();App.Wireframe.Flash.error("Failed to submit changes. Please try again later")},function(){edit_row.remove();row.show()})},error:function(response){working_row.remove();row.show();App.Wireframe.Flash.error("Failed to load edit form. Please try again later")}});return false});wrapper.delegate("td.options a.trash","click",function(event){if(!confirm(App.lang("Are you sure that you want to move selected item to Trash?"))){return false}var anchor=$(this);var image=anchor.find("img:first");var original_image=image.attr("src");image.attr("src",App.Wireframe.Utils.indicatorUrl("small"));$.ajax({type:"post",url:anchor.attr("href"),success:function(record){image.attr("src",original_image);anchor.parents("tr:first").remove();update_tables.apply(wrapper_dom);if(record["class"]=="Expense"){App.Wireframe.Content.trigger("project_expense_deleted",[record])}else{App.Wireframe.Content.trigger("project_time_deleted",[record])}App.Wireframe.Flash.success("Item has been successfully moved to Trash")},error:function(){image.attr("src",original_image);App.Wireframe.Flash.error("Failed to move item to Trash. Please try again later")}});return false})};var handle_events=function(){var wrapper=$(this);var wrapper_dom=this;var project_time_expenses_wrapper=wrapper.parents(".project_time_expenses_wrapper");App.Wireframe.Content.bind("time_record_created.content",function(event,time_record){if(wrapper_dom.tel_data.settings.project_id!=time_record.project.id){return false}add_item.apply(wrapper_dom,[time_record]);if(project_time_expenses_wrapper.is(":hidden")){project_time_expenses_wrapper.show().siblings("#empty_slate_time_expenses").hide()}if(time_record.value==1){App.Wireframe.Flash.success("One hour added")}else{App.Wireframe.Flash.success(":num hours added",{num:time_record.value})}});App.Wireframe.Content.bind("time_record_updated.content",function(event,time_record){if(wrapper_dom.tel_data.settings.project_id!=time_record.project.id){return false}if(wrapper.find('tr[record_id="'+time_record.id+'"][record_type="'+time_record["class"]+'"]').length){return false}add_item.apply(wrapper_dom,[time_record]);if(project_time_expenses_wrapper.is(":hidden")){project_time_expenses_wrapper.show().siblings("#empty_slate_time_expenses").hide()}});App.Wireframe.Content.bind("expense_created.content",function(event,expense){if(wrapper_dom.tel_data.settings.project_id!=expense.project.id){return false}add_item.apply(wrapper_dom,[expense]);if(project_time_expenses_wrapper.is(":hidden")){project_time_expenses_wrapper.show().siblings("#empty_slate_time_expenses").hide()}App.Wireframe.Flash.success("Expense has been successfully logged")});App.Wireframe.Content.bind("expense_updated.content",function(event,expense){if(wrapper_dom.tel_data.settings.project_id!=expense.project.id){return false}if(wrapper.find('tr[record_id="'+expense.id+'"][record_type="'+expense["class"]+'"]').length){return false}add_item.apply(wrapper_dom,[expense]);if(project_time_expenses_wrapper.is(":hidden")){project_time_expenses_wrapper.show().siblings("#empty_slate_time_expenses").hide()}});App.Wireframe.Content.bind("project_time_deleted.content project_expense_deleted.content",function(event,tracking_object){if(wrapper_dom.tel_data.settings.project_id!=tracking_object.project.id){return false}if(!wrapper.find("table.date_wrapper").length){project_time_expenses_wrapper.hide().siblings("#empty_slate_time_expenses").show()}})};var plugin_name="timeExpensesLog";$.fn[plugin_name]=function(method){if(public_methods[method]){return public_methods[method].apply(this,Array.prototype.slice.call(arguments,1))}else{if(typeof method==="object"||!method){return public_methods.init.apply(this,arguments)}else{$.error("Method "+method+" does not exist in jQuery."+plugin_name)}}}})(jQuery);