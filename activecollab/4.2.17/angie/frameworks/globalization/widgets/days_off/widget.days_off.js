App.widgets.daysOff=function(){return{init:function(wrapper_id){var wrapper=$("#"+wrapper_id);var days_off_table=wrapper.find("table.form");var new_row_counter=0;var init_day_off_row=function(row){row.find("td.options a.remove_day_off").click(function(){if(confirm(App.lang("Are you sure that you want to delete this event?"))){row.remove();if(days_off_table.find("tr.day_off_row").length<1){days_off_table.hide();$("#no_days_off_message").show()}}return false})};days_off_table.find("tr.day_off_row").each(function(){init_day_off_row($(this))});wrapper.find("a.button_add").click(function(){new_row_counter++;var row=$('<tr class="day_off_row"><td class="name"><input name="workweek[days_off]['+new_row_counter+'][name]" type="text" /></td><td class="date"><div class="select_date"><input class="input_text input_date" name="workweek[days_off]['+new_row_counter+'][event_date]" value="" autocomplete="off" /></div></td><td class="yearly center"><input name="workweek[days_off]['+new_row_counter+'][repeat_yearly]" type="checkbox" value="1" class="inline" /></td><td class="options right"><a href="#" title="'+App.lang("Remove Day Off")+'" class="remove_day_off"><img src="'+App.Wireframe.Utils.imageUrl("/icons/12x12/delete.png","environment")+'" alt="" /></a></td></tr>');days_off_table.append(row);var date_options={dateFormat:"yy/mm/dd",minDate:new Date("2000/01/01"),maxDate:new Date("2050/01/01"),showAnim:"blind",duration:0,showOn:"both",buttonImage:App.Wireframe.Utils.imageUrl("icons/16x16/calendar.png","system"),buttonImageOnly:true,buttonText:App.lang("Select Date"),firstDay:$("#workweekFirstWeekDay").val(),changeYear:true,hideIfNoPrevNext:true,yearRange:"2000:2050"};row.find("td.date input").datepicker(date_options);init_day_off_row(row);days_off_table.oddEven({selector:"tr.day_off_row"}).show();$("#no_days_off_message").hide();row.find("td.name input")[0].focus();return false})}}}();