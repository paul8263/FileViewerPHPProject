<?php
	require_once 'file.class.php';

/*
$path = '/Applications/XAMPP/htdocs';
$file = scandir($path);

$files = array();
$dotPattern = '/^(\.){1,2}$/i';
//questionable
//$suffixPattern = '/((?<=\.)[A-Za-z0-9_]+)$/i';
//$ptn = '/^(?:[^\.]+)(?:.*)(?<=\.)([a-zA-z0-9_]+)$/i';
$ptn2 = '/(?:^[^\.].*(?<=\.)([a-zA-z0-9_]+)$)|(?:^\..+(?<=\.)([a-zA-Z0-9_]+)$)/i';

foreach ($file as $val) {
	if(!preg_match($dotPattern, $val)) {
		$fullName = $path.'/'.$val;
		$f = new file();
		$f->name = $val;
		$f->fullName = $fullName;
		$f->type = filetype($fullName);
		$f->fileSize = filesize($fullName);
		$f->accessTime = fileatime($fullName);
		$f->modifyTime = filemtime($fullName);
		
		$matches = array();
		preg_match($ptn2, $val, $matches);
		$f->suffix = @$matches[count($matches) - 1];
		unset($matches);
		$files[] = $f;
	}
}
$file = testFiltrateSuffix($files, 'PHP');
echo '<pre>';
print_r($file);
echo '</pre>';

	$ptn = '/^\S*htdocs(\S*)$/i';
	$str = '/Applications/XAMPP/htdocs/MySite';
	$str1 = 'hah.x';
	$matches = array();
	$res = preg_match($ptn, $str,$matches);
	echo '<pre>';
	print_r($matches);
	echo '</pre>';
*/
	/*
	testFileDownload('test.php', '/Applications/XAMPP/htdocs/FileViewer');
	
	function testFileDownload($fileName,$filePath) {
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
	
	*/
	/*
	function fileUpload($uploadedPath) {
		$fileElementName = 'dialog_upload_file';
		if(!empty($_FILES[$fileElementName]['error'])) {
			//Uploaded error!
		} else if(empty($_FILES[$fileElementName]['tmp_name'])) {
			//No file has been uploaded!
		} else {
			move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $uploadedPath);
		}
	}
	*/
	/*
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
	*/
	$arr = array();
	$arr['username'] = 'Paul';
	$arr['password'] = '123456';
	$path = '/Applications/XAMPP/xamppfiles/htdocs/testUpload';
	$fileName = 'testIni.txt';
	function saveToIni($arr,$fileName,$path) {
		$content = '';
		foreach($arr as $key=>$val) {
			$content .= $key.'='.$val."\r\n";
		}
		$fullName = $path.'/'.$fileName;
		file_put_contents($fullName, $content);
	}
	
	function changePassword($password) {
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'FileViewer/config.ini';
		$content = parse_ini_file($filePath);
		if(isset($password) && '' != $password) {
			$content['password'] = md5($password);
			saveToIni($content, 'config.ini', $_SERVER['DOCUMENT_ROOT'].'/'.'FileViewer');
		} else {
			echo 'Password incorrect';
		}
	}
//	$file = parse_ini_file('/Applications/XAMPP/xamppfiles/htdocs/testUpload/testIni.txt');
//	echo '<pre>';
//	print_r($file);
//	echo '</pre>';
//	changePassword('123456');
//	verify('123456');
	function verify($password = '') {
		$filePath = $_SERVER['DOCUMENT_ROOT'].'/'.'FileViewer/config.ini';
		$content = parse_ini_file($filePath);
		if(!isset($password) || '' == $password) {
			echo 'No password entered';
		} else if(md5($password) == $content['password']) {
			//Todo
			echo 'Password correct';
		} else {
			
			echo 'Password incorrect';
		}
	}
	function setTokenInClient() {
		setcookie('token',generateToken());
	}
	function generateToken() {
		return md5(time());
	}
	
	echo generateToken();
	
?>