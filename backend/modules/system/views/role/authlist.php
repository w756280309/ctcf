<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta charset="UTF-8">
        <script type=text/javascript src="/js/jquery.js"></script>
        <script type="text/javascript" src="/js/jNotify/jNotify.jquery.js"></script> 
         <link rel="stylesheet" type="text/css" href="/js/jNotify/jNotify.jquery.css" /> 
        <script type="text/javascript" src="/js/showres.js"></script> 
        <link href="/js/rolepoplayer/reveal.css" rel="stylesheet" position="1">
        <link href="/js/ztree/zTreeStyle.css" rel="stylesheet" position="1">
        <script src="/js/rolepoplayer/jquery.reveal.js"></script>
        <script src="/js/ztree/jquery.ztree.core-3.5.js"></script>
        <script src="/js/ztree/jquery.ztree.excheck-3.5.js"></script> 
        <script type="text/javascript" src="/js/layer/layer.min.js"></script>
        <script type="text/javascript" src="/js/layer/extend/layer.ext.js"></script>
    </head>
    <body>
<div class="page_form">
        <ul id="treeDemo" class="ztree"></ul>
        <div align="center"><input type="button" value="选择带回并关闭"  id="closeBtn"></div>
</div>

<SCRIPT type="text/javascript">

    var setting = {
        check: {
            enable: true
        },
        data: {
            simpleData: {
                enable: true
            }
        },
        callback: {
            onCheck: onCheck
        }
    };

    var zNodes = [
		{id: 0, pId: 0, name: "权限列表", open: true},
<?php foreach ($power as $key => $val) { ?>
	        {
	            id: "<?= $val['sn'] ?>",
	                    pId: "<?= $val['psn'] ?>",
						name: "<?= $val['auth_name'] ?>"<?php if($val['sn'] == '0'){echo ",open: true";}else{echo ",open:false";}?>  <?php
	if ($val['checked']) {
		echo ", checked: true";
	}
	?>
	        },
<?php } ?>
    ];

    var code;

    function setCheck() {
        var zTree = $.fn.zTree.getZTreeObj("treeDemo"),
                py = $("#py").attr("checked") ? "p" : "p",
                sy = $("#sy").attr("checked") ? "s" : "s",
                pn = $("#pn").attr("checked") ? "p" : "p",
                sn = $("#sn").attr("checked") ? "s" : "s",
                type = {"Y": py + sy, "N": pn + sn};
        zTree.setting.check.chkboxType = type;
        showCode('setting.check.chkboxType = { "Y" : "' + type.Y + '", "N" : "' + type.N + '" };');
    }
    function showCode(str) {
        if (!code)
            code = $("#code");
        code.empty();
        code.append("<li>" + str + "</li>");
    }
    function onCheck(e, treeId, treeNode) {
        var treeObj = $.fn.zTree.getZTreeObj("treeDemo"),
                nodes = treeObj.getCheckedNodes(true),
                v = "";
        for (var i = 0; i < nodes.length; i++) {
            v += nodes[i].id + '-' + nodes[i].name + ",";
            //   alert(nodes[i].id); //获取选中节点的值
        }
        window.parent.document.getElementById('role-auths').value=v;

    }
    $(document).ready(function () {
        val = $('#role-auths',window.parent.document).val();
        if(val!=''){
            arr = val.split(","); 
            len = arr.length;
            for(var i =0; i<zNodes.length;i++){//
                for(var j= 0;j<len;j++){
                    ga = arr[j].split('-');
                    if(ga[0]==zNodes[i].id){
                        zNodes[i].checked=true;
                        zNodes[i].open=true;
                    }
                }
            }
        }
        
        $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        setCheck();
        $("#py").bind("change", setCheck);
        $("#sy").bind("change", setCheck);
        $("#pn").bind("change", setCheck);
        $("#sn").bind("change", setCheck);
        
        $('#closeBtn').bind('click',function(){
            parent.layer.closeAll()
        })
    });
    //-->
</SCRIPT>

</body></html>
