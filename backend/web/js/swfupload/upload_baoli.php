<?php
        $type = $_GET['type'];
        if(empty($type)){
            echo "未知图片！";
	    exit(0);
        }
	if (isset($_POST["PHPSESSID"])) {
		session_id($_POST["PHPSESSID"]);
	}

	session_start();
	ini_set("html_errors", "0");

	if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
		echo "ERROR:invalid upload";
		exit(0);
	}


	if (!isset($_SESSION["file_info"])) {
		$_SESSION["file_info"] = array();
	}
	
	$fileName = md5(rand()*1000) . ".jpg";
	move_uploaded_file($_FILES["Filedata"]["tmp_name"], "../../upload/".$type."/" . $fileName);
	$file_id = md5(rand()*1000);
	
	$_SESSION["file_info"][$file_id] = $fileName;

	echo "FILEIB:" . $fileName;	// Return the file id to the script
	
?>