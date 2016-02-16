<!DOCTYPE unspecified PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>File Viewer</title>
		<link rel="stylesheet" type="text/css" href="./css/ui-lightness/jquery-ui-1.10.3.custom.min.css">
		<link rel="stylesheet" type="text/css" href="./css/included.css">
		<script type="text/javascript" src="./js/jquery-1.9.1.js"></script>
		<script type="text/javascript" src="./js/jquery-ui-1.10.3.custom.min.js"></script>
		<script type="text/javascript" src="./js/ajaxfileupload.js"></script>
		<script type="text/javascript" src="./js/included.js"></script>
	</head>
	<body>
	<div class="page">
		<div class="title" id="title">file path</div>
		<div class="toolbar">
			<button id="button_up">Up</button>
			<button id="button_goto">Go To</button>
			<button id="button_baseFolder">Base Folder</button>
			<button id="button_filter">Filter</button>
			<button id="button_sort">Sort</button>
			<button id="button_search">Search</button>
			<button id="button_refresh">Refresh</button>
			<button id="button_open">Open</button>
			<button id="button_download">Download</button>
		</div>
		<div class="content">
			<div class="operator">
				<div id="sideBar">
					<h3>Advanced operations</h3>
					<div>
						<button id="button_upload">Upload</button><br>
						<button id="button_newFolder">New Folder</button><br>
						<button id="button_rename">Rename</button><br>
						<button id="button_delete">Delete</button>
						<button id="button_changePassword">Password</button>
					</div>
					<h3>Information</h3>
					<div>
						<p>info</p>
					</div>
				</div>
			</div>
			<div class="main" id="main">
				<table id="fileList" cellpadding="8px"></table>
			</div>
		</div>
		<div class="foot">
			<p>File Viewer. Version: 1.0.1 Paul's micro software(TM). Programmed by Paul. Copyright reserved. &nbsp;&nbsp;&nbsp;Jul 7, 2013.</p>
		</div>
	</div>
	
	<div id="dialog_goto" title="Go to folder">
		<p id="dialog_goto_hint">Please input the path</p>
		<form action="#">
			<input type="hidden" name="operation" value="gotoFolder"/>
			<label for="dialog_goto_path">Path:</label><input type="text" name="dialog_goto_path" id="dialog_goto_path" size="55"/>
		</form>
	</div>
	
	<div id="dialog_newFolder" title="New Folder">
		<p id="dialog_newFolder_hint"></p>
		<form action="#">
			<p>Please input the folder name</p>
			<label for="dialog_newFolder_name">Name:</label><input type="text" name="dialog_newFolder_name" id="dialog_newFolder_name"/>
		</form>
	</div>
	
	<div id="dialog_search" title="Search">
		<p>Search files or folders in the current path</p>
		<form action="#">
			<label for="dialog_search_keyword">Keyword:</label><input type="text" name="dialog_search_keyword" id="dialog_search_keyword" size="30"/>
			<p>Case sensitive:</p>
			<div id="dialog_search_case_div">
				<input type="radio" id="dialog_search_case_on" name="dialog_search_case_radio" value="true" checked="checked"/><label for="dialog_search_case_on">ON</label>
				<input type="radio" id="dialog_search_case_off" name="dialog_search_case_radio" value="false"/><label for="dialog_search_case_off">OFF</label>
			</div>
		</form>
	</div>
	
	<div id="dialog_sort" title="Sort files">
		<p>Please sort the criteria listed below</p>
		<form action="#">
			<p>Sort order:</p>
			<div id="dialog_sort_asc_div">
				<input type="radio" id="dialog_sort_asc_radio_asc" name="dialog_sort_asc_radio" value="true" checked="checked"/><label for="dialog_sort_asc_radio_asc">ASC</label>
				<input type="radio" id="dialog_sort_asc_radio_desc" name="dialog_sort_asc_radio" value="false"/><label for="dialog_sort_asc_radio_desc">DESC</label>
			</div>
			<p>Sort by:</p>
			<div id="dialog_sort_sortBy_div">
				<input type="radio" id="dialog_sort_sortBy_sortByName" name="dialog_sortBy_radio" value="name" checked="checked"/><label for="dialog_sort_sortBy_sortByName">Name</label>
				<input type="radio" id="dialog_sort_sortBy_sortBySuffix" name="dialog_sortBy_radio" value="suffix"/><label for="dialog_sort_sortBy_sortBySuffix">Suffix</label>
				<input type="radio" id="dialog_sort_sortBy_sortByFileSize" name="dialog_sortBy_radio" value="fileSize"/><label for="dialog_sort_sortBy_sortByFileSize">File Size</label>
				<input type="radio" id="dialog_sort_sortBy_sortByType" name="dialog_sortBy_radio" value="type"/><label for="dialog_sort_sortBy_sortByType">Type</label>
				<input type="radio" id="dialog_sort_sortBy_sortByAccessTime" name="dialog_sortBy_radio" value="accessTime"/><label for="dialog_sort_sortBy_sortByAccessTime">Access Time</label>
				<input type="radio" id="dialog_sort_sortBy_sortByModifyTime" name="dialog_sortBy_radio" value="modifyTime"/><label for="dialog_sort_sortBy_sortByModifyTime">Modify Time</label>
			</div>
		</form>
	</div>
	
	<div id="dialog_filter" title="Filter">
		<p>Please select the conditions listed below</p>
		<form action="#">
			<span>Display invisible file:</span><input type="checkbox" id="dialog_filter_invisible" checked="checked"/><label for="dialog_filter_invisible">ON</label>
			<br><br>
			<span>Suffix filter:</span><input type="text" id="dialog_filter_suffixfilter"/>
		</form>
	</div>
	
	<div id="dialog_download" title="File Download">
		<div id="dialog_download_innerdiv" class="ui-state-highlight ui-corner-all">
			<p id="dialog_download_hint"></p>
		</div>
	</div>
	
	<div id="dialog_upload" title="Upload File">
		<p id="dialog_upload_hint"></p>
		<form action="fileViewerController.php?operation=upload" method="post" enctype="multipart/form-data" id="dialog_upload_form">
			<p>Choose a file to upload:</p>
			<input type="file" name="dialog_upload_file" id="dialog_upload_file"/><br>
		</form>
	</div>
	
	<div id="dialog_uploadFeedback" title="Upload File">
		<p id="dialog_uploadFeedback_hint"></p>
	</div>
	
	<div id="dialog_rename" title="Rename">
		<p id="dialog_rename_hint"></p>
		<form action="#">
			<br>
			Original name:<span id="dialog_rename_originalName"></span><br><br>
			<label for="dialog_rename_newName">New name:</label><input type="text" id="dialog_rename_newName"/>
		</form>
	</div>
	
	<div id="dialog_delete" title="Are you sure to delete?">
		<br>
		<span>Are you sure to delete "</span><span id="dialog_delete_name"></span><span>" ?</span><br>
		<p>Deleted files cannot be restored!</p>
	</div>
	
	<div id="dialog_noFileSelected" title="Warning">
		<p>No file has been selected!</p>
	</div>
	
	<div id="dialog_hint" title="Hint">
		<p id="dialog_hint_text"></p>
	</div>
	
	<div id="dialog_login" title="Password Required">
		<p id="dialog_login_hint"></p>
		<form action="#">
			<p>Please enter password</p>
			<input type="password" id="dialog_login_password" maxlength="8"/>
		</form>
	</div>
	
	<div id="dialog_changePassword" title="Change Password">
		<p id="dialog_changePassword_hint"></p>
		<form action="#">
			<label for="dialog_changePassword_oldPassword">Old password:</label><input type="password" id="dialog_changePassword_oldPassword" maxlength="8"/><br>
			<label for="dialog_changePassword_newPassword">New password:</label><input type="password" id="dialog_changePassword_newPassword" maxlength="8"/><br>
			<label for="dialog_changePassword_repassword">Re-enter new password:</label><input type="password" id="dialog_changePassword_repassword" maxlength="8"/>
		</form>
	</div>
	
	</body>
</html>
