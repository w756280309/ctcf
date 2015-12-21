<?php
error_reporting(E_ALL ^ E_NOTICE);
include 'UploadFile.class.php';
include 'Pinyin.class.php';
//输出消息
function error_msg($msg) {
    @header("Content-type:text/html");
    echo json_encode(array('error' => 1, 'message' => $msg));
    exit;
}
//重命名
function filename() {
    foreach ($_FILES as $file) {
        $name = explode('.', $file['name']);
        $ext = end($name);
        $name = $name[0];
    }
    $pinyin = new Pinyin();
    $pattern = '/[^\x{4e00}-\x{9fa5}\d\w]+/u';
    $name = preg_replace($pattern, '', $name);
    $name = substr($pinyin->output($name, true), 0, 80);
    $rand = "";
    if (file_exists('../upload/' . date('Y-m') . '/' . date('d') . '/' . $name . '.' . $ext)) {
        $rand = '-' . substr(cp_uniqid(), -5);
    }
    return $name . $rand;
}

function CheckFolder($filedir)
{
    if (!file_exists($filedir)) {
        if (!CheckFolder(dirname($filedir))) {
            return false;
        }
        if (!mkdir($filedir, 0777)) {
            return false;
        }
    }
    return true;
}

$files = $_FILES;
$ban_ext = array('php', 'asp', 'asp', 'html', 'htm', 'js', 'shtml', 'txt', 'aspx');

if (!empty($files)) {
    foreach ($files as $file) {
        $name = $file['name'];
        $tmpfile = explode('.', $file['name']);
        if($tmpfile==0){
            error_msg('非法文件上传！');
            return;
        }
        $ext = end($tmpfile);
        if (in_array($ext, $ban_ext)) {
            error_msg('非法文件上传！');
            return;
        }
    }
} else {
    error_msg('上传文件不能为空！');
}

//文件路径
$file_path ='../upload/editorUpload/';
//文件URL路径
$file_url = '../upload/editorUpload/';
//文件目录时间
$filetime = date('Y-m') . '/' . date('d');

$bol = CheckFolder($file_url . $filetime);
//var_dump($file_url . $filetime,$bol);exit;

//上传
$upload = new UploadFile();
$upload->maxSize = 1024 * 1024 * 5; //大小
$upload->allowExts = explode(',', 'gif,jpg,png,GIF,JPG,JPEG,PNG,zip,rar'); //格式
$upload->savePath = $file_path . $filetime . '/'; //保存路径
$upload->saveRule = 'filename'; //重命名
//var_dump($_POST,1);exit;
if (!$upload->upload()) {
    error_msg($upload->getErrorMsg()); //输出错误消息
    return;
} else {
    $info = $upload->getUploadFileInfo();
    $info = $info[0];
    //返回信息 Array ( [0] => Array ( [name] => 未命名.jpg [type] => image/pjpeg [size] => 53241 [key] => Filedata [extension] => jpg [savepath] => ../../../upload/2011-12-17/ [savename] => 1112170727041127335395.jpg ) )
    $ext = $info['extension'];
    
    
    if (isset($_POST)&&($_POST['wateradd'])) {
        $waterfile = 'logo.gif';
        if ($_POST['waterpor'] == 0) {
            $por = 5;
        } else {
            $por = $_POST['waterpor'];
        }
        if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
            Image::water($file_path . $filetime . '/' . $info['savename'], $waterfile, $por);
        }
    }
    $thumb ="";
    if (isset($_POST)&&($_POST['thumb'])) {
        //设置高度和宽度
        $thumbwidth = intval($_POST['thumbwidth']);
        $thumbheight = intval($_POST['thumbheight']);
        if (empty($thumbwidth) || $_POST['thumbsys'] == 1) {
            $thumbwidth = 220;
        }
        if (empty($thumbheight) || $_POST['thumbsys'] == 1) {
            $thumbheight = 220;
        }
        //过滤不支持格式进行缩图
        if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif') {
            $thumb = Image::thumb($file_path . $filetime . '/' . $info['savename'], $file_path . $filetime . '/thumb_' . $info['savename'], '', $thumbwidth, $thumbheight, '', true);
        }
    }

    //根据缩图返回数据           
    if ($thumb) {
        $file = $file_url . $filetime . '/thumb_' . $info['savename'];
    } else {
        $file = $file_url . $filetime . '/' . $info['savename'];
    }

    $title = str_replace('.' . $info['extension'], '', $info['name']);
    $json = array('error' => 0, 'url' => '../'.$file, 'original' => '../'.$file_url . $filetime . '/' . $info['savename'], 'file' => '../'.$file, 'title' => $title, 'msg' => '成功');

    //var_dump($json);exit;

    $json['id'] = $id;

    echo json_encode($json);
    exit;
}
    

    