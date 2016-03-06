jQuery.fn.trackingReportResults=function(s){var settings=jQuery.extend({records:null,currencies:null,show_user_column:true,sum_by_user:false,group_by:"dont",show_time:true,show_expenses:true,show_no_records_message:false,no_records_message:App.lang("No tracked records to show")},s);return this.each(function(){var wrapper=$(this).addClass("tracking_report_result");if(jQuery.isArray(settings.records)||(typeof(settings.records)=="object"&&settings.records)){if(settings.sum_by_user){for(var group_id in settings.records){if(typeof(settings.records[group_id]["records"])=="object"&&!jQuery.isEmptyObject(settings.records[group_id]["records"])){var group_label=typeof(settings.records[group_id]["label"])=="string"?settings.records[group_id]["label"]:"--";var group_wrapper=$('<div class="tracking_report_result_group_wrapper"><p>'+App.clean(group_label)+'</p><div class="tracking_report_result_group_inner_wrapper"></div></div>').appendTo(wrapper);var group_table=$('<table class="common auto summarized" cellspacing="0"><thead><tr><th class="user">'+App.lang("User")+'</th></tr></thead><tbody></tbody><tfoot><tr><td class="total">'+App.lang("Total")+":</td></tr></tfoot></table>").appendTo(group_wrapper.find("div.tracking_report_result_group_inner_wrapper"));if(settings.show_time||settings.show_expenses){var header=group_table.find("thead tr");var footer=group_table.find("tfoot tr");if(settings.show_time){header.append('<th class="time right">'+App.lang("Time")+"</th>");footer.append('<td class="time right"></td>')}if(settings.show_expenses){for(var currency_id in settings.currencies){header.append('<th class="expenses center" currency_id="'+currency_id+'">'+App.lang("Expenses (:currency_code)",{currency_code:settings.currencies[currency_id]["code"]})+"</th>");footer.append('<td class="expenses center" currency_id="'+currency_id+'"></td>')}}}var group_table_body=group_table.find("tbody");var total_time=0;var total_expenses={};if(settings.show_expenses){for(var currency_id in settings.currencies){total_expenses[currency_id]=0}}for(var user_email in settings.records[group_id]["records"]){if(settings.show_time){total_time+=settings.records[group_id]["records"][user_email]["time"]}var row='<tr class="record summarized" user_id="'+settings.records[group_id]["records"][user_email]["user_id"]+'" user_email="'+App.clean(user_email)+'">';row+='<td class="user">'+App.clean(settings.records[group_id]["records"][user_email]["user_name"])+"</td>";if(settings.show_time){row+='<td class="time right">'+App.hoursFormat(settings.records[group_id]["records"][user_email]["time"])+"</td>"}if(settings.show_expenses){for(var currency_id in settings.currencies){var expenses_for_currency=settings.records[group_id]["records"][user_email]["expenses_for_"+currency_id];row+='<td class="expenses center" currency_id="'+currency_id+'">'+App.moneyFormat(expenses_for_currency)+"</td>";total_expenses[currency_id]+=expenses_for_currency}}group_table_body.append(row+"</tr>")}if(group_table_body.find("tr.record").length>0){if(settings.show_time){footer.find("td.time").text(App.hoursFormat(total_time))}if(settings.show_expenses){for(var currency_id in settings.currencies){footer.find("td.expenses[currency_id="+currency_id+"]").text(App.moneyFormat(total_expenses[currency_id]))}}}else{group_table.find("tfoot").remove()}}}}else{var render_date=function(record){return typeof(record.record_date)=="object"&&record.record_date?App.clean(record.record_date["formatted_date_gmt"]):"--"};var render_user=function(record){return typeof(record.user_name)=="string"&&record.user_name?record.user_name:record.user_email};var render_value=function(record){if(record.type=="TimeRecord"){if(typeof(record.group_name)!="undefined"&&record.group_name){return App.lang(":hours of :job_type",{hours:App.hoursFormat(record.value),job_type:record.group_name})}else{return App.hoursFormat(record.value)}}else{var currency_id=typeof(record.currency_id)!="undefined"?record.currency_id:0;if(currency_id&&typeof(settings.currencies[currency_id])=="object"){var currency_code=settings.currencies[currency_id]["code"]}else{var currency_code=""}if(typeof(record.group_name)!="undefined"&&record.group_name){return App.lang(":amount in :category",{amount:App.clean(currency_code)+" "+App.moneyFormat(record.value),category:record.group_name})}else{return App.clean(currency_code)+App.moneyFormat(record.value)}}};var render_summary=function(record){if(record.parent_type=="Task"&&record.parent_name&&record.parent_url){var parent_text='<a href="'+App.clean(record.parent_url)+'" class="quick_view_item">'+App.clean(record.parent_name)+"</a>"}else{var parent_text=""}if(typeof(record.summary)=="string"&&record.summary){var summary_text=App.clean(record.summary)}else{var summary_text=""}if(parent_text&&summary_text){return parent_text+" ("+summary_text+")"}else{if(parent_text){return parent_text}else{if(summary_text){return summary_text}else{return""}}}};var render_status=function(record){switch(record.billable_status){case 0:return App.lang("Not Billable");case 1:return App.lang("Billable");case 2:return App.lang("Pending Payment");case 3:return App.lang("Paid");default:return App.lang("Unknown Status")}};var render_project=function(record){return typeof(record.project_name)=="string"&&record.project_name&&typeof(record.project_url)=="string"&&record.project_url?'<a href="'+App.clean(record.project_url)+'" class="quick_view_item">'+App.clean(record.project_name)+"</a>":App.lang("Unknown Project")};for(var group_id in settings.records){if(jQuery.isArray(settings.records[group_id]["records"])&&settings.records[group_id]["records"].length){var group_label=typeof(settings.records[group_id]["label"])=="string"?settings.records[group_id]["label"]:"--";var group_wrapper=$('<div class="tracking_report_result_group_wrapper"><p>'+App.clean(group_label)+'</p><div class="tracking_report_result_group_inner_wrapper"></div></div>').appendTo(wrapper);var group_table=$('<table class="common records_list" cellspacing="0"><thead></thead><tbody></tbody></table>').appendTo(group_wrapper.find("div.tracking_report_result_group_inner_wrapper"));var group_table_head=group_table.find("thead");var group_table_body=group_table.find("tbody");switch(settings.group_by){case"date":group_table_head.append('<tr><th class="value">'+App.lang("Value")+"</th>"+(settings.show_user_column?'<th class="user">'+App.lang("User")+"</th>":"")+'<th class="summary">'+App.lang("Summary")+'</th><th class="status center">'+App.lang("Status")+'</th><th class="project right">'+App.lang("Project")+"</th></tr>");var columns_count=settings.show_user_column?5:4;break;case"project":group_table_head.append('<tr><th class="date left">'+App.lang("Date")+'</th><th class="value">'+App.lang("Value")+"</th>"+(settings.show_user_column?'<th class="user">'+App.lang("User")+"</th>":"")+'<th class="summary">'+App.lang("Summary")+'</th><th class="status center">'+App.lang("Status")+"</th></tr>");var columns_count=settings.show_user_column?5:4;break;default:group_table_head.append('<tr><th class="date left">'+App.lang("Date")+'</th><th class="value">'+App.lang("Value")+"</th>"+(settings.show_user_column?'<th class="user">'+App.lang("User")+"</th>":"")+'<th class="summary">'+App.lang("Summary")+'</th><th class="status center">'+App.lang("Status")+'</th><th class="project right">'+App.lang("Project")+"</th></tr>");var columns_count=settings.show_user_column?6:5;break}var total_time=0;var total_expenses={};App.each(settings.records[group_id]["records"],function(record_id,record){if(record.type=="TimeRecord"){var record_type="time_record";total_time+=record.value}else{var record_type="expense";var currency_id=record.currency_id;if(typeof(total_expenses[currency_id])=="undefined"){total_expenses[currency_id]=0}total_expenses[currency_id]+=record.value}var row='<tr class="record '+record_type+'" record_id="'+record_id+'" user_id="'+record.user_id+'" currency_id="'+record.currency_id+'">';switch(settings.group_by){case"date":row+='<td class="value">'+render_value(record)+"</td>";if(settings.show_user_column){row+='<td class="user">'+render_user(record)+"</td>"}row+='<td class="summary">'+render_summary(record)+"</td>";row+='<td class="status center">'+render_status(record)+"</td>";row+='<td class="project right">'+render_project(record)+"</td>";break;case"project":row+='<td class="date left">'+render_date(record)+"</td>";row+='<td class="value">'+render_value(record)+"</td>";if(settings.show_user_column){row+='<td class="user">'+render_user(record)+"</td>"}row+='<td class="summary">'+render_summary(record)+"</td>";row+='<td class="status center">'+render_status(record)+"</td>";break;default:row+='<td class="date left">'+render_date(record)+"</td>";row+='<td class="value">'+render_value(record)+"</td>";if(settings.show_user_column){row+='<td class="user">'+render_user(record)+"</td>"}row+='<td class="summary">'+render_summary(record)+"</td>";row+='<td class="status center">'+render_status(record)+"</td>";row+='<td class="project right">'+render_project(record)+"</td>";break}group_table_body.append(row+"</tr>")});var total_time_string=App.lang("Total Time: :time",{time:App.hoursFormat(total_time)});var total_expenses_by_currency=[];for(var currency_id in total_expenses){if(currency_id&&typeof(settings.currencies[currency_id])=="object"){var currency_code=settings.currencies[currency_id]["code"]}else{var currency_code=""}total_expenses_by_currency.push(App.clean(currency_code)+" "+total_expenses[currency_id])}if(total_expenses_by_currency.length<1){var total_expenses_string=App.lang("Total Expenses: :expenses",{expenses:0})}else{var total_expenses_string=App.lang("Total Expenses: :expenses",{expenses:total_expenses_by_currency.join(", ")})}if(settings.show_time&&settings.show_expenses){var totals=App.clean(total_time_string)+". "+App.clean(total_expenses_string)}else{if(settings.show_time){var totals=App.clean(total_time_string)}else{if(settings.show_expenses){var totals=App.clean(total_expenses_string)}else{var totals=""}}}group_table_body.after('<tfoot><tr><td colspan="'+columns_count+'" class="center">'+totals+"</td></tr></tfoot>")}}}}else{if(settings.show_no_records_message&&typeof(settings.no_records_message)=="string"){wrapper.append('<p class="empty_page">'+App.clean(settings.no_records_message)+"</p>")}}})};