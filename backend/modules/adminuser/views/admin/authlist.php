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
            <ul id="treeDemo" class="ztree">加载中……</ul>
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

            var zNodes;

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
                window.parent.document.getElementById('admin-auths').value = v;

            }

            var admin_id =<?php echo empty($model->id) ? 0 : $model->id; ?>;
            var source_role_id = '<?php echo $model->role_sn; ?>'
            var role_id = '<?php echo Yii::$app->request->get('rsn'); ?>';
            jQuery(document).ready(function() {

                tree(admin_id, role_id,1);

                $('#closeBtn').bind('click',function(){
                    parent.layer.closeAll()
                })
        
            })
            
            
            function tree(aid, rid, o) {
                    if (aid && rid == source_role_id) {
                    } else {
                        aid = 0;
                    }
                    $.get("/adminuser/admin/roles?aid=" + aid + "&rid=" + rid, function(result) {
                        //zNodes=result;
                        var a = new Array();
                        var obj0 = {id: '0', pId: '0', name: '所有权限', checked: false, open: true};
                        a[0] = obj0;
                        v = "";
                        for (i = 0; i < result.length; i++) {
                            bool = false;
                            if (result[i]['checked']) {
                                bool = true;
                            }
                            bopen = false;
                            if (result[i]['sn'] == "0") {
                                bopen = true;
                            }
                            if (bool == true) {
                                v += result[i]['sn'] + '-' + result[i]['auth_name'] + ",";
                            }
                            var obj = {id: result[i]['sn'], pId: result[i]['psn'], name: result[i]['auth_name'], checked: bool, open: bopen};
                            a[i + 1] = obj;
                        }
                        if (admin_id == 0) {
                            $("#admin-auths",window.parent.document).val(v);
                        }
                        //console.log(a);
                        zNodes = a;
                        //加载树
                        $.fn.zTree.init($("#treeDemo"), setting, zNodes);
                        setCheck();
                        $("#py").bind("change", setCheck);
                        $("#sy").bind("change", setCheck);
                        $("#pn").bind("change", setCheck);
                        $("#sn").bind("change", setCheck);
                    });
                }
        </SCRIPT>

    </body></html>
