/**
 * 
 */
$(function() {
	//static definition
	var controllerURL = "fileViewerController.php";
	var baseFolder = "/Applications/XAMPP/htdocs";
	//UI definition
	$("button").button();
	$("#dialog_goto").dialog({
		autoOpen: false,
		modal: true,
		width: 550,
		resizable: false,
		buttons: {
			"Open": function() {
				if(!$("#dialog_goto_path").val().match(/^\/\S*[^\/]$/i)) {
					$("#dialog_goto_hint").text("Illegal path.");
					$("#dialog_goto_path").focus();
				} else {
					$.post(controllerURL,{operation:"gotoFolder",dialog_goto_path:$("#dialog_goto_path").val()},function(data){
						displayfile(data);
					});
				
					changePathDisplay();
					changeUpButtonStatus();
					$("#dialog_goto_hint").text("Please input the path");
					$(this).dialog("close");
				}
				
			},
			"Cancel": function() {
				$("#dialog_goto_hint").text("Please input the path");
				$("#dialog_goto_path").val("");
				$(this).dialog("close");
			}
		},
		close: function() {
			$("#dialog_goto_hint").text("Please input the path");
			$("#dialog_goto_path").val("");
			$(this).dialog("close");
		}
	});
	$("#dialog_newFolder").dialog({
		autoOpen: false,
		modal: true,
		buttons: {
			"Create": function() {
				if($("#dialog_newFolder_name").val() == "") {
					$("#dialog_newFolder_hint").text("You forget to enter the new folder name.");
					$("#dialog_newFolder_name").focus();
				} else if(!$("#dialog_newFolder_name").val().match(/^[A-Za-z_][A-Za-z0-9_]*$/i)) {
					$("#dialog_newFolder_hint").text("Illegal folder name.");
					$("#dialog_newFolder_name").focus();
				} else {
					$("#dialog_newFolder_hint").text("");
					$.post(controllerURL,{operation:"newFolder",folderName:$("#dialog_newFolder_name").val()},function(data) {
						if(data == 1) {
							$("#dialog_hint_text").text("Create folder success!");
						} else {
							$("#dialog_hint_text").text("Create folder failed!");
						}
						$("#dialog_hint").dialog("open");
					});
					$(this).dialog("close");
					
					$.post(controllerURL,{operation:"gotoFolder",dialog_goto_path:$("#title").text()},function(data){
						displayfile(data);
					});
				}
			},
			"Cancel": function() {
				$("#dialog_newFolder_name").val("");
				$("#dialog_newFolder_hint").text("");
				$(this).dialog("close");
			}
		},
		close: function() {
			$("#dialog_newFolder_name").val("");
			$("#dialog_newFolder_hint").text("");
			$(this).dialog("close");
		}
	});
	$("#dialog_search").dialog({
		autoOpen: false,
		modal: false,
		width: 400,
		resizable: false,
		buttons: {
			"Search": function() {
				$.post(controllerURL,{operation:"search",keyword:$("#dialog_search_keyword").val(),caseInsensitive:$("#dialog_search_case_on")[0].checked},function(data) {
					displayfile(data);
				})
			},
			"Clear": function() {
				$("#dialog_search_keyword").val("");
				$.post(controllerURL,{operation:"search",keyword:"",caseInsensitive:$("#dialog_search_case_on")[0].checked},function(data) {
					displayfile(data);
				})
			}
		},
		close: function() {
			$(this).dialog("close");
		}
	});
	$("#dialog_sort").dialog({
		autoOpen: false,
		modal: false,
		resizable: false,
		width: 700,
		buttons: {
			"Sort" : function() {
				$.post(controllerURL,{operation:"sort",column:$("#dialog_sort [name='dialog_sortBy_radio']:checked").val(),asc:$("#dialog_sort_asc_radio_asc")[0].checked},function(data) {
					displayfile(data);
				});
				$(this).dialog("close");
			}
		},
		close: function() {
			$(this).dialog("close");
		}
	});
	$("#dialog_sort_asc_div").buttonset();
	$("#dialog_sort_sortBy_div").buttonset();
	$("#dialog_search_case_div").buttonset();
	$("#dialog_filter").dialog({
		autoOpen: false,
		modal: false,
		resizable: false,
		width: 400,
		buttons: {
			"Filtrate": function() {
				$.post(controllerURL,{operation:"filter",suffix:$("#dialog_filter_suffixfilter").val(),displayInvisible:$("#dialog_filter_invisible")[0].checked},function(data) {
					displayfile(data);
				});
			},
			"Clear": function() {
				$("#dialog_filter_suffixfilter").val("");
				$.post(controllerURL,{operation:"filter",suffix:"",displayInvisible:"true"},function(data) {
					displayfile(data);
				});
			}
		},
		close: function() {
			$(this).dialog("close");
		}
	});
	$("#dialog_download").dialog({
		autoOpen: false,
		modal: true,
		resizable: false,
		width: 400,
		buttons: {
			"Close" : function() {
				$(this).dialog("close");
			}
		},
		close: function() {
			$(this).dialog("close");
		}
	});
	$("#dialog_upload").dialog({
		autoOpen: false,
		modal: true,
		resizable: false,
		width: 400,
		buttons: {
			"Upload" : function() {
				if($("#dialog_upload_file").val() != "") {
//					$("#dialog_upload_form").submit();
//					$(this).dialog("close");
					var uploadURL = controllerURL + "?operation=upload";
					$.ajaxFileUpload({
						url:uploadURL,
						secureuri:false,
						fileElementId:'dialog_upload_file',
						dataType: 'json',
						success: function(data, status) {
							
							if(typeof(data.error) != 'undefined') {
								if(data.error != '') {
									$("#dialog_uploadFeedback_hint").text("Success, error:" + data.error);
									//alert(data.error);
								} else {
									$("#dialog_uploadFeedback_hint").text("Success, message:" + data.msg);
									//alert(data.msg);
								}
							} else {
								$("#dialog_uploadFeedback_hint").text("File upload success. Congratulations!");
							}
							$("#dialog_uploadFeedback").dialog("open");
						},
						error: function (data, status, e) {
							$("#dialog_uploadFeedback_hint").text("Error" + e);
							$("#dialog_uploadFeedback").dialog("open");
							//alert(e);
						}
					});
					
					$(this).dialog("close");
				} else {
					$("#dialog_upload_hint").text("You forget to choose a file to upload!");
					$("#dialog_upload_file").focus();
				}
			},
			"Clear" : function() {
				$("#dialog_upload_hint").text("");
				$("#dialog_upload_file").val("");
			}
		},
		close: function() {
			$("#dialog_upload_hint").text("");
			$("#dialog_upload_file").val("");
			$(this).dialog("close");
		}
	});
	$("#dialog_uploadFeedback").dialog({
		autoOpen: false,
		modal: false,
		resizable: false,
		buttons: {
			"OK": function() {
				$("#dialog_uploadFeedback_hint").text("");
				$(this).dialog("close");
			}
		},
		close: function() {
			$("#dialog_uploadFeedback_hint").text("");
			$(this).dialog("close");
		}
	});
	$("#dialog_rename").dialog({
		autoOpen: false,
		modal: true,
		resizable: false,
		width: 400,
		buttons: {
			"OK": function() {
				if($("#dialog_rename_newName").val() == "") {
					$("#dialog_rename_hint").text("Please input new file name.");
					$("#dialog_rename_newName").focus();
				} else if(!$("#dialog_rename_newName").val().match(/^[A-Za-z_][A-Za-z0-9_\.]*$/i)) {
					$("#dialog_rename_hint").text("Illegal file name.");
					$("#dialog_rename_newName").focus();
				} else {
					$("#dialog_rename_hint").text("");
					$.post(controllerURL,{operation:"rename",originalFileName:$(":radio[name='fileList_files']:checked").val(),newFileName:$("#dialog_rename_newName").val()},function(data) {
						if(data == 1) {
							$("#dialog_hint_text").text("Rename success!");
						} else {
							$("#dialog_hint_text").text("Rename failed!");
						}
						$("#dialog_hint").dialog("open");
					});
				}
				
				$.post(controllerURL,{operation:"gotoFolder",dialog_goto_path:$("#title").text()},function(data){
					displayfile(data);
				});
				
				$(this).dialog("close");
			},
			"Cancel": function() {
				$("#dialog_rename_newName").val("");
				$("#dialog_rename_hint").text("");
				$(this).dialog("close");
			}
		},
		close: function() {
			$("#dialog_rename_newName").val("");
			$("#dialog_rename_hint").text("");
			$(this).dialog("close");
		}
	});
	$("#dialog_delete").dialog({
		autoOpen: false,
		modal: true,
		resizable: false,
		width: 450,
		buttons: {
			"No": function() {
				$(this).dialog("close");
			},
			"Yes": function() {
				$.post(controllerURL,{operation:"delete",fileName:$(":radio[name='fileList_files']:checked").val()},function(data) {
					if(data == 1) {
						$("#dialog_hint_text").text("Delete success!");
					} else {
						$("#dialog_hint_text").text("Delete failed!");
					}
					$("#dialog_hint").dialog("open");
				});
				
				$.post(controllerURL,{operation:"gotoFolder",dialog_goto_path:$("#title").text()},function(data){
					displayfile(data);
				});
				
				$(this).dialog("close");
			}
		},
		close: function() {
			$(this).dialog("close");
		}
	});
	$("#dialog_noFileSelected").dialog({
		autoOpen: false,
		modal: true,
		resizable: false,
		buttons: {
			"OK" : function() {
				$(this).dialog("close");
			}
		},
		close: function() {
			$(this).dialog("close");
		}
	});
	$("#dialog_hint").dialog({
		autoOpen: false,
		modal: false,
		resizable: false,
		buttons: {
			"OK": function() {
				$(this).dialog("close");
			}
		},
		close: function() {
			$(this).dialog("close");
		}
	});
	
	$("#dialog_login").dialog({
		autoOpen: false,
		modal: true,
		resizable: false,
		dialogClass: "no-close",
		buttons: {
			"OK": function() {
				if($("#dialog_login_password").val() == "") {
					$("#dialog_login_hint").text("You forget to enter the password!");
					$("#dialog_login_password").focus();
				} else if($("#dialog_login_password").val().length < 4) {
					$("#dialog_login_hint").text("Password must be longer than 4 letters!");
					$("#dialog_login_password").focus();
				} else if(!$("#dialog_login_password").val().match(/^\w+$/)) {
					$("#dialog_login_hint").text("Illegal password!");
					$("#dialog_login_password").focus();
				} else {
					$("#dialog_login_hint").text("");
					$.post(controllerURL,{operation:"login",password:$("#dialog_login_password").val()},function(data) {
						if(data == "0") {
							$("#dialog_hint_text").text("Login success!");
							//Display base folder contents
							
							$("#dialog_hint").dialog("open");
							$("#dialog_login").dialog("close");
							
						} else if(data == "1" || data == "2") {
							$("#dialog_login_hint").text("Password incorrect");
						}
					});
				}
			}
		}
	});
	$("#dialog_changePassword").dialog({
		autoOpen: false,
		modal: true,
		resizable: false,
		dialogClass: "no-close",
		buttons: {
			"OK": function() {
				if($("#dialog_changePassword_oldPassword").val() == "") {
					$("#dialog_changePassword_hint").text("You forget to enter the old password!");
					$("#dialog_changePassword_oldPassword").focus();
				} else if($("#dialog_changePassword_newPassword").val() == "") {
					$("#dialog_changePassword_hint").text("You forget to enter the new password!");
					$("#dialog_changePassword_newPassword").focus();
				} else if($("#dialog_changePassword_oldPassword").val().length < 4) {
					$("#dialog_changePassword_hint").text("Password must be longer than 4 letters!");
					$("#dialog_changePassword_oldPassword").focus();
				} else if($("#dialog_changePassword_newPassword").val().length < 4) {
					$("#dialog_changePassword_hint").text("Password must be longer than 4 letters!");
					$("#dialog_changePassword_newPassword").focus();
				} else if(!$("#dialog_changePassword_oldPassword").val().match(/^\w+$/)) {
					$("#dialog_changePassword_hint").text("Old password illegal!");
					$("#dialog_changePassword_oldPassword").focus();
				} else if(!$("#dialog_changePassword_newPassword").val().match(/^\w+$/)) {
					$("#dialog_changePassword_hint").text("New password illegal!");
					$("#dialog_changePassword_newPassword").focus();
				} else if($("#dialog_changePassword_repassword").val() != $("#dialog_changePassword_newPassword").val()) {
					$("#dialog_changePassword_hint").text("Re-enter your new password!");
					$("#dialog_changePassword_newPassword").focus();
				} else {
					$.post(controllerURL,{operation:"changePassword",oldPassword:$("#dialog_changePassword_oldPassword").val(),newPassword:$("#dialog_changePassword_newPassword").val()},function(data) {
						if(data == "0") {
							$("#dialog_hint_text").text("Change password success!");
							$("#dialog_hint").dialog("open");
							$("#dialog_changePassword").dialog("close");
						} else if(data == "1" || data == "2") {
							$("#dialog_changePassword_hint").text("Old password incorrect!");
						} else {
							$("#dialog_changePassword_hint").text("Change password error");
						}
					});
				}
			},
			"Cancel": function() {
				$("#dialog_changePassword_hint").text("");
				$("#dialog_changePassword_oldPassword").val("");
				$("#dialog_changePassword_newPassword").val("");
				$("#dialog_changePassword_repassword").val("");
				$(this).dialog("close");
			}
		}
	});
	
	
	$("#sideBar").accordion({
		active: false,
		collapsible: true,
		heightStyle: "content"
	});
	
	//Action binding
	$("#button_goto").click(function() {
		$("#dialog_goto_path").val($("#title").text());
		$("#dialog_goto").dialog("open");
		$("#dialog_goto_path").focus();
	});
	$("#button_newFolder").click(function() {
		$("#dialog_newFolder").dialog("open");
		$("#dialog_newFolder_name").focus();
	});
	$("#button_up").click(function() {
		$.post(controllerURL,{operation:"up"},function(data){
			displayfile(data);
		});
		changePathDisplay();
		changeUpButtonStatus();
	});
	$("#button_baseFolder").click(function() {
		$.post(controllerURL,{operation:"gotoFolder",dialog_goto_path:baseFolder},function(data){
			displayfile(data);
		});
		changePathDisplay();
		changeUpButtonStatus();
	});
	$("#button_search").click(function() {
		$("#dialog_search").dialog("open");
	});
	$("#button_sort").click(function() {
		$("#dialog_sort").dialog("open");
	});
	$("#button_filter").click(function() {
		$("#dialog_filter").dialog("open");
		$("#dialog_filter_suffixfilter").focus();
	});
	$("#dialog_filter_invisible").button();
	$("#button_open").click(function() {
		if(typeof $(":radio[name='fileList_files']:checked").val() != "undefined") {
			var name = $("#title").text();
			var link = "http://localhost" + name.match(/^\S*htdocs(\S*)$/i)[1] + "/" + $(":radio[name='fileList_files']:checked").val();
			window.location.href=link;
		}
	});
	$("#button_download").click(function() {
		if(typeof $(":radio[name='fileList_files']:checked").val() == "undefined") {
			$("#dialog_download_hint").empty().text("No file has been selected!");
			$("#dialog_download").dialog("open");
		} else {
			$("#dialog_download_hint").empty().html("<a href="+controllerURL+"?operation=download&fileName="+$(":radio[name='fileList_files']:checked").val()+" target='_blank'>Download Link. Click Me!</a>");
			$("#dialog_download").dialog("open");
//			$.post(controllerURL,{operation:"download",fileName:$(":radio[name='fileList_files']:checked").val()});
		}
	});
	$("#button_upload").click(function() {
		$("#dialog_upload").dialog("open");
	});
	$("#button_rename").click(function() {
		if(typeof $(":radio[name='fileList_files']:checked").val() == "undefined") {
			$("#dialog_noFileSelected").dialog("open");
		} else {
			$("#dialog_rename_originalName").text($(":radio[name='fileList_files']:checked").val());
			$("#dialog_rename_newName").val($(":radio[name='fileList_files']:checked").val());
			$("#dialog_rename").dialog("open");
		}
	});
	$("#button_delete").click(function() {
		if(typeof $(":radio[name='fileList_files']:checked").val() == "undefined") {
			$("#dialog_noFileSelected").dialog("open");
		} else {
			$("#dialog_delete_name").text($(":radio[name='fileList_files']:checked").val());
			$("#dialog_delete").dialog("open");
		}
	});
	$("#button_refresh").click(function() {
		$.post(controllerURL,{operation:"gotoFolder",dialog_goto_path:$("#title").text()},function(data){
			displayfile(data);
		});
	});
	$("#button_changePassword").click(function() {
		$("#dialog_changePassword_oldPassword").val("");
		$("#dialog_changePassword_newPassword").val("");
		$("#dialog_changePassword_repassword").val("");
		$("#dialog_changePassword").dialog("open");
	});
	
	//Check whether user has entered the password when first start up
	//If there is no cookie called token in cookie, the client will ask the user to login.
	function getCookie(name) {
		var cookies = document.cookie.split(";");
		for(var i=0; i<cookies.length; i++) {
			var s = cookies[i].split("=");
			s[0] = s[0].replace(/^\s*/,"").replace(/\s*$/,"");
			if(name == s[0]) {
				return s[1];
			}
		}
		return false;
	}
	if(false == getCookie("token")) {
		$("#dialog_login_hint").text("");
		$("#dialog_login").dialog("open");
	}
	
	//Display the assigned path when login success
	displayPathWhenStartup(baseFolder);
	
	//Utility
	function displayfile(jsonFileList) {
		$("#fileList").empty().append("<tr><th></th><th><a href='#' id='sort_name'>Name</a></th><th><a href='#' id='sort_suffix'>Suffix</a></th><th><a href='a' id='sort_type'>Type</a></th><th><a href='#' id='sort_size'>Size</a></th><th><a href='#' id='sort_accessTime'>Access time</a></th><th><a href='#' id='sort_modifyTime'>Modify time</a></th></tr>");
		$("#sort_name").click(function(e) {
			e.preventDefault();
			$.post(controllerURL,{operation:"sort",column:"name",asc:"true"},function(data) {
				displayfile(data);
			});
		});
		$("#sort_suffix").click(function(e) {
			e.preventDefault();
			$.post(controllerURL,{operation:"sort",column:"suffix",asc:"true"},function(data) {
				displayfile(data);
			});
		});
		$("#sort_type").click(function(e) {
			e.preventDefault();
			$.post(controllerURL,{operation:"sort",column:"type",asc:"true"},function(data) {
				displayfile(data);
			});
		});
		$("#sort_size").click(function(e) {
			e.preventDefault();
			$.post(controllerURL,{operation:"sort",column:"fileSize",asc:"true"},function(data) {
				displayfile(data);
			});
		});
		$("#sort_accessTime").click(function(e) {
			e.preventDefault();
			$.post(controllerURL,{operation:"sort",column:"accessTime",asc:"true"},function(data) {
				displayfile(data);
			});
		});
		$("#sort_modifyTime").click(function(e) {
			e.preventDefault();
			$.post(controllerURL,{operation:"sort",column:"modifyTime",asc:"true"},function(data) {
				displayfile(data);
			});
		});
		
		
		
		var json = eval("(" + jsonFileList + ")");
		$.each(json,function(i,v){
			var accessTime = new Date();
			accessTime.setTime(v.accessTime*1000);
			var modifyTime = new Date();
			modifyTime.setTime(v.modifyTime*1000);
			var suffix = (v.suffix==null)?"":v.suffix;
			if(v.type == "dir" || v.type == "link") {
				//Avoid dot exists in the file name. That will cause a wrong expression like $("#a_.settings").
				var name = v.name;
				var nameReplaced = name.replace(/^(\.)/,"dot_");
				$("#fileList").append("<tr><td><input type='radio' name='fileList_files' value='"+ v.name +"'/></td><td><a href='' id='a_"+nameReplaced+"'>" + v.name +"</a></td><td>"+ suffix +"</td><td>"+ v.type +"</td><td>"+ v.fileSize +"</td><td>"+ accessTime.toLocaleString() +"</td><td>"+ modifyTime.toLocaleString() +"</td></tr>");
				$("#a_"+nameReplaced).click(function(e){
					e.preventDefault();
					$.post(controllerURL,{operation:"gotoFolder",dialog_goto_path:$("#title").text()+"/"+v.name},function(data){
						changePathDisplay();
						displayfile(data);
					});
				});
			} else {
				$("#fileList").append("<tr><td><input type='radio' name='fileList_files' value='"+ v.name +"'/></td><td>"+ v.name +"</td><td>"+ suffix +"</td><td>"+ v.type +"</td><td>"+ v.fileSize +"</td><td>"+ accessTime.toLocaleString() +"</td><td>"+ modifyTime.toLocaleString() +"</td></tr>");
			}
		});
		
		//table display style
		$("#fileList tr:odd").css("background-color","#FFFFFF");
		$("#fileList tr:even").css("background-color","#EEEEEE");
	}
	
	function changePathDisplay() {
		$.post(controllerURL,{operation:"changePathDisplay"},function(data) {
			$("#title").html("").append(data);
		});
	}
	
	function changeUpButtonStatus() {
		$.post(controllerURL,{operation:"changePathDisplay"},function(data) {
			if("/" == data) {
				$("#button_up").button({disabled: true});
			} else {
				$("#button_up").button({disabled: false});
			}
		});
	}
	
	function displayPathWhenStartup(path) {
		if($("#fileList").html() == "") {
			$.post(controllerURL,{operation:"gotoFolder",dialog_goto_path:path},function(data){
				displayfile(data);
			});
		}
		$("#title").html("").append(path);
	}
	
});