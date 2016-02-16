<?php 
	require_once 'file.class.php';
	
	session_start();
	//The session will become invalid soon after the browser shuts down.
	session_cache_expire(0);
	
	//The controller executes different functions based on the value contains in the variable operation.
	$operation = @$_POST['operation'];
	if(!isset($operation) || '' == $operation) {
		
		//Login URL pattern: controller.php?operation=login&password=???
	} else if('login' == $operation) {
		login($_POST['password']);
		exit();
	}
	if(verify()) {
		//goto URL pattern: controller.php?operation=gotoFolder&dialog_goto_path=???
		if('gotoFolder' == $operation) {
			if(isset($_POST['dialog_goto_path']) || '' == $_POST['dialog_goto_path']) gotoPath($_POST['dialog_goto_path']);
			
			//up URL pattern: controller.php?operation=up&dialog_goto_path=???
		} else if('up' == $operation) {
			up();
			
			//changePathDisplay URL pattern: controller.php?operation=changePathDisplay
		} else if('changePathDisplay' == $operation) {
			echo getCurrentPath();
			
			//search URL pattern: controller.php?operation=search&keyword=???&caseInsensitive=???
		} else if('search' == $operation) {
			search($_POST['keyword'],$_POST['caseInsensitive']);
			//Sort URL pattern: controller.php?operation=sort&column=???&asc=???
		} else if('sort' == $operation) {
			_sort($_POST['column'], $_POST['asc']);
			//Filtrate URL pattern: controller.php?operation=filter&suffix=???&displayInvisible=???
		} else if('filter' == $operation) {
			filter($_POST['suffix'],$_POST['displayInvisible']);
			//Delete URL pattern: controller.php?operation=delete&fileName=???
		} else if('delete' == $operation) {
			deleteFile($_POST['fileName'], getCurrentPath());
			//Rename URL pattern: controller.php?operation=rename&originalFileName=???&newFileName=???
		} else if('rename' == $operation) {
			renameFile($_POST['originalFileName'],$_POST['newFileName'],getCurrentPath());
			//New Folder URL pattern: controller.php?operation=newFolder$folderName=??? 
		} else if('newFolder' == $operation) {
			newFolder($_POST['folderName'], getCurrentPath());
			//Change Readonly Password URL patterm: controller.php?operation=changePassword&oldPassword=???&newPassword=???
		} else if('changePassword' == $operation) {
			changePassword($_POST['oldPassword'], $_POST['newPassword']);
		}
	} else {
		setcookie('token','Time out',time() - 3600);
	}
	
	
	//Download URL pattern: controller.php?operation=download&fileName=???
	if('download' == @$_GET['operation']) {
		fileDownload($_GET['fileName'], getCurrentPath());
	//Upload URL pattern: controller.php?operation=upload
	} else if('upload' == @$_GET['operation']) {
		fileUpload(getCurrentPath());
	}
	
	
// Button click action 
	/**
	 * Go to a assigned path, and send the array of file objects with JSON format to client.
	 * @param unknown_type $path
	 */
	function gotoPath($path) {
		setCurrentPath($path);
		
		$file = @scandir($path);
		
		$files = array();
		$dotPattern = '/^(\.){1,2}$/i';
		$suffixPattern = '/^[^\.]\S*(?<=\.)(\S+)$|^\.\S+(?<=\.)(\S+)$/i';
		
		//Add all files in the current path to the array $files
		foreach($file as $val) {
			if(!preg_match($dotPattern, $val)) {
			$fullName = $path.'/'.$val;
			$f = new file();
			$f->name = $val;
			$f->type = filetype($fullName);
			$f->fileSize = filesize($fullName);
			$f->accessTime = fileatime($fullName);
			$f->modifyTime = filemtime($fullName);
		
			$matches = array();
			preg_match($suffixPattern, $val, $matches);
			$f->suffix = @$matches[count($matches) - 1];
			unset($matches);
			$files[] = $f;
			}
		}
		
		//filters
		
		//Search filter
		$fk = getSearchKeyword();
		$caseInsensitive = ('true' == $fk[1])?true: false;
		$files = filtrateFileNameByKeyword($files, $fk[0], $caseInsensitive);
		
		//Sort filter
		$sc = getSortCriteria();
		$asc = ('true' == $sc[1])? true: false;
		sortFiles($files, $sc[0],$asc);
		
		//Suffix and invisible filter
		$f = getFilter();		
		$invisible = ('true' == $f[1])? true: false;
		if(!$invisible) {
			$files = filtrateHidden($files);
		}
		$files = filtrateSuffix($files, $f[0]);
		
		//Send the file list to the client
		echo json_encode($files);
	}
	
	/**
	 * Executes when the button "up" is pressed.
	 */
	function up() {
		$path = dirname(getCurrentPath());
		gotoPath($path);
	}
	
	/**
	 * Search
	 * @param unknown_type $keyword
	 * @param unknown_type $caseInsensitive
	 */
	function search($keyword, $caseInsensitive) {
		setSearchKeyword($keyword, $caseInsensitive);
		gotoPath(getCurrentPath());
	}
	
	/**
	 * Sort Need to do
	 * @param unknown_type $column
	 * @param unknown_type $asc
	 */
	
	function _sort($column,$asc) {
		setSortCriteria($column, $asc);
		gotoPath(getCurrentPath());
	}
	function filter($suffix,$displayInvisible) {
		setFilter($suffix, $displayInvisible);
		gotoPath(getCurrentPath());
	}
	
// Utility
	//Change and get the system state
	function setCurrentPath($path) {
		$_SESSION['currentPath'] = $path;
	}
	function getCurrentPath() {
		if(isset($_SESSION['currentPath']) && $_SESSION['currentPath'] != '') {
			return $_SESSION['currentPath'];
		} else {
			return false;
		}
	}
	function setSearchKeyword($keyword,$caseInsensitive) {
		$_SESSION['searchKeyword'] = array($keyword,$caseInsensitive);
	}
	function getSearchKeyword() {
		if(isset($_SESSION['searchKeyword']) && $_SESSION['searchKeyword'] != '') {
			return $_SESSION['searchKeyword'];
		} else {
			return false;
		}
	}
	function setSortCriteria($column,$asc) {
		$_SESSION['sortCriteria'] = array($column,$asc);
	}
	function getSortCriteria() {
		if(isset($_SESSION['sortCriteria']) && $_SESSION['sortCriteria'] !='') {
			return $_SESSION['sortCriteria'];
		} else {
			return false;
		}
	}
	function setFilter($suffix,$displayInvisible) {
		$_SESSION['filter'] = array($suffix,$displayInvisible);
	}
	function getFilter() {
		if(isset($_SESSION['filter']) && $_SESSION['filter'] != '') {
			return $_SESSION['filter'];
		} else {
			return false;
		}
	}
	
	
	
	function filtrateSuffix($files,$suffix) {
		if(!isset($suffix) || '' == $suffix) {
			return $files;
		} else {
			$newFiles = array();
			foreach ($files as $file) {
				if(!(false === stripos($file->suffix, $suffix))) {
					$newFiles[] = $file;
				}
			}
			return $newFiles;
		}
	}
	//remove the files with the name starts with a dot.
	function filtrateHidden($files) {
		$newFiles = array();
		$pattern = '/^\..+$/i';
		foreach($files as $file) {
			if(!preg_match($pattern, $file->name)) {
				$newFiles[] = $file;
			}
		}
		return $newFiles;
	}
	
	//"Search" function
	function filtrateFileNameByKeyword($files,$keyword,$caseInsensitive = false) {
		if(!isset($keyword) || '' == $keyword) {
			return $files;
		} else {
			$newFiles = array();
			if($caseInsensitive) {
				foreach ($files as $file) {
					//if there is no $keyword in the string $file->name, the function strpos will return false. And if the keyword matches the first letter in
					//$file->name, the function will return 0, with the same value as false. So we have to not only judge the value, but also the type of the variable.
					if(!(false === strpos($file->name, $keyword))) {
						$newFiles[] = $file;
					}
				}
			} else {
				foreach ($files as $file) {
					if(!(false === stripos($file->name, $keyword))) {
						$newFiles[] = $file;
					}
				}
			}
			return $newFiles;
		}
	}
	
	/**
	 * Sort file section begin
	 * @param unknown_type $files
	 * @param unknown_type $column
	 * @param unknown_type $asc
	 */
	function sortFiles(&$files,$column,$asc = true) {
		if(!isset($column) || '' == $column) {
			return;
		} else {
			$functionName = $column;
			if($asc) {
				$functionName .= 'ASC';
			} else {
				$functionName .= 'DESC';
			}
			usort($files, $functionName);
		}
	}
	//Callbacks
	function nameASC($file1,$file2) {
		return strcasecmp($file1->name, $file2->name);
	}
	function nameDESC($file1,$file2) {
		return -strcasecmp($file1->name, $file2->name);
	}
	function suffixASC($file1,$file2) {
		return strcasecmp($file1->suffix, $file2->suffix);
	}
	function suffixDESC($file1,$file2) {
		return -strcasecmp($file1->suffix, $file2->suffix);
	}
	function typeASC($file1, $file2) {
		return strcasecmp ( $file1->type, $file2->type );
	}
	function typeDESC($file1, $file2) {
		return - strcasecmp ( $file1->type, $file2->type );
	}
	function fileSizeASC($file1, $file2) {
		return $file1->fileSize - $file2->fileSize;
	}
	function fileSizeDESC($file1, $file2) {
		return $file2->fileSize - $file1->fileSize;
	}
	function modifyTimeASC($file1, $file2) {
		return $file1->modifyTime - $file2->modifyTime;
	}
	function modifyTimeDESC($file1, $file2) {
		return $file2->modifyTime - $file1->modifyTime;
	}
	function accessTimeASC($file1, $file2) {
		return $file1->accessTime - $file2->accessTime;
	}
	function accessTimeDESC($file1, $file2) {
		return $file2->accessTime - $file1->accessTime;
	}
	//Sort file section end
	
	/**
	 * 
	 * @param unknown_type $fileName
	 * @param unknown_type $filePath
	 */
	function fileDownload($fileName,$filePath) {
		$fullPath = $filePath.'/'.$fileName;
		if(file_exists($fullPath)) {
			$file = fopen($fullPath, 'r') or die('File does not exit');
			$size = filesize($fullPath);
			$buff = '';
			$readLength = 0;
			
			header("Content-type: application/octet-stream");
			header("Accept-ranges: bytes");
			header("Accept-Length: $size");
			header("Content-Disposition: attachment; filename=".$fileName);
			
			while(!feof($file) && $readLength <= $size) {
				echo $buff = fread($file, 1024);
				$readLength+=1024;
			}
			
		} else {
			return;
		}
	}
	/**
	 * File upload
	 * Only can upload to the folder with the permission of 777
	 * @param unknown_type $uploadedPath
	 */
	function fileUpload($uploadedPath) {
		$error = "";
		$msg = "";
		$fileElementName = 'dialog_upload_file';
		if(!empty($_FILES[$fileElementName]['error'])) {
			switch($_FILES[$fileElementName]['error']) {
	
				case '1':
					$error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
					break;
				case '2':
					$error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
					break;
				case '3':
					$error = 'The uploaded file was only partially uploaded';
					break;
				case '4':
					$error = 'No file was uploaded.';
					break;
	
				case '6':
					$error = 'Missing a temporary folder';
					break;
				case '7':
					$error = 'Failed to write file to disk';
					break;
				case '8':
					$error = 'File upload stopped by extension';
					break;
				case '999':
				default:
					$error = 'No error code avaliable';
			}
		} else if(empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none') {
			$error = 'No file was uploaded..';
		} else {
				$msg .= " File Name: " . $_FILES[$fileElementName]['name'] . ", ";
				$msg .= " File Size: " . @filesize($_FILES[$fileElementName]['tmp_name']);
				move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $uploadedPath.'/'.$_FILES[$fileElementName]['name']);
				//for security reason, we force to remove all uploaded file
				@unlink($_FILES[$fileElementName]);		
		}		
		echo "{";
		echo				"error: '" . $error . "',\n";
		echo				"msg: '" . $msg . "'\n";
		echo "}";
	}
	/**
	 * Only can delete the files in the folder with the permission of 777
	 * @param unknown_type $fileName
	 * @param unknown_type $path
	 */
	function deleteFile($fileName,$path) {
		$fullPath = $path."/".$fileName;
		if(is_dir($fullPath)) {
			echo rmdir($fullPath);
		} else if(is_file($fullPath)) {
			echo unlink($fullPath);
		}
	}
	/**
	 * Only can rename the files in the folder with the permission of 777
	 * @param unknown_type $originalFileName
	 * @param unknown_type $newFileName
	 * @param unknown_type $path
	 */
	function renameFile($originalFileName,$newFileName,$path) {
		$old = $path."/".$originalFileName;
		$new = $path."/".$newFileName;
		echo rename($old, $new);
	}
	/**
	 * Only can create new folders inside the existing folders with the permission of 777
	 * @param unknown_type $folderName
	 * @param unknown_type $path
	 */
	function newFolder($folderName,$path) {
		$fullName = $path."/".$folderName;
		echo mkdir($fullName);
	}
	
	
	//For security
	//Only if the values of the token in cookie and in the session are the same, then the user can use this system.
	//If there is no cookie called token in cookie, the client will ask the user to login.
	//Password is saved in config.ini, and encrypted by md5
	function setToken($token) {
		$_SESSION['token'] = $token;
	}
	function getToken() {
		if(isset($_SESSION['token']) && $_SESSION['token'] != '') {
			return $_SESSION['token'];
		} else {
			return false;
		}
	}
	function saveToIni($arr,$fileName,$path) {
		$content = '';
		foreach($arr as $key=>$val) {
			$content .= $key.'='.$val."\r\n";
		}
		$fullName = $path.'/'.$fileName;
		file_put_contents($fullName, $content);
	}
	/**
	 * Returns:
	 * 0: Change password success
	 * 1: Old password is null
	 * 2: Old password error
	 * 3: New password illegal
	 * @param unknown_type $oldPassword
	 * @param unknown_type $newPassword
	 */
	function changePassword($oldPassword,$newPassword) {
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'FileViewer/config.ini';
		$content = parse_ini_file($filePath);
		if(!isset($oldPassword) || '' == $oldPassword) {
			echo '1';
		} else if(md5($oldPassword) == $content['password']) {
			
			if(isset($newPassword) && '' != $newPassword && strlen($newPassword) >= 4) {
				$content['password'] = md5($newPassword);
				saveToIni($content, 'config.ini', $_SERVER['DOCUMENT_ROOT'].'/'.'FileViewer');
				echo '0';
			} else {
				echo '3';
			}
		} else {
			echo '2';
		}
		
	}
	/**
	 * Returns:
	 * 0: Password correct
	 * 1: No password entered
	 * 2: Password incorrect
	 * @param unknown_type $password
	 */
	function login($password) {
	$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'FileViewer/config.ini';
		$content = parse_ini_file($filePath);
		if(!isset($password) || '' == $password) {
			echo '1';
		} else if(md5($password) == $content['password']) {
			$token = generateToken();
			setcookie('token',$token);
			setToken($token);
			echo '0';
		} else {
			echo '2';
		}
	}
	/**
	 * Return true only when the token value in cookie corresponds with that saved in the session
	 */
	function verify() {
		if(!isset($_COOKIE['token']) || '' == $_COOKIE['token']) {
			return false;
		} else if($_COOKIE['token'] == $_SESSION['token']) {
			return true;
		} else {
			return false;
		}
	}
	/**
	 * Generated token is based on system time
	 */
	function generateToken() {
		return md5(time());
	}
	
?>