jQuery.fn.selectClientWithPermissions=function(s){var settings=jQuery.extend({client_company_managers_url:null,value:null,optional:null,company_select_id:null,skip_owners_without_finances:null,require_all_permissions:true},s);return this.each(function(){var wrapper=$(this);var company_select=$("#"+wrapper.attr("company_select_id"));var init=function(){if($("select#companyId option").length>0){populate_select();company_select.change(function(){populate_select()})}else{App.Wireframe.Flash.error("There are no client companies available")}};var populate_select=function(){var company_id=company_select.val();var populate_select_url=settings.client_company_managers_url.replace("--COMPANY-ID--",company_id);$.ajax({url:App.extendUrl(populate_select_url,{permissions:settings.permissions.join(","),require_all_permissions:settings.require_all_permissions?1:0,type_filter:"Client"}),type:"get",data:{},success:function(response){if(settings.optional==true){wrapper.empty().append(new Option(App.lang("-- None --"),""));wrapper.append(new Option(" "," "))}else{wrapper.empty()}if(response){var str,selected;$.each(response,function(key,value){$.each(value,function(user_id,user_name){if(user_id==settings.value){selected="selected=selected"}else{selected=""}str+='<option value="'+user_id+'" '+selected+">"+user_name+"</option>"})});wrapper.append(str)}},error:function(){App.Wireframe.Flash.error("Failed to populate select box")}})};init()})};