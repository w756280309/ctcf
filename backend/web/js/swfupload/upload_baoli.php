<?php
    $type = $_GET['type'];

    if(empty($type)){
        echo "未知图片！";
        exit(0);
    }

    if ($type === 'adv' && isset($_FILES['Filedata']['tmp_name']) && '' !== $_FILES['Filedata']['tmp_name']) {
        $shebei = $_GET['shebei'];
        if (!in_array($shebei, ['wap', 'pc'])) {
            exit("参数错误!");
        }

        if ($_FILES['Filedata']['size'] > 2097152) {
            exit('图片大小应小于2MB!');
        }

        $imageSize = getimagesize($_FILES['Filedata']['tmp_name']);

        if ('wap' === $shebei && $imageSize[1] !== 350 && $imageSize[0] !== 750) {
            exit("图片尺寸应为：高350px，宽750px!");
        }

        if ('pc' === $shebei && $imageSize[1] !== 340 && $imageSize[0] !== 1920) {
            exit("图片尺寸应为：高340px，宽1920px!");
        }
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