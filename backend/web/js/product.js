var editor_company;
KindEditor.ready(function() {
    editor_company = KindEditor.create(
            '#company', {
                cssPath: '/kindeditor/plugins/code/prettify.css',
                fileManagerJson: '',
                uploadJson: '/kindeditor/editor.php',
                allowFileManager: true,
                filterMode: false,
                afterUpload: function(url, data) {
                    $('#file_id').val($('#file_id').val() + data.id + ',');
                },
                afterCreate : function() {
                    this.sync();
                },
                afterBlur:function(){
                    this.sync();
                }
        });
            
});
function createTemp(){
    var html =  '<table class="special_table" border=1 style="width:100%;">'+
                        '<tr><td style="width:15%;" >资产详情</td><td colspan=5></td></tr>'+
                        '<tr><td>资产评估</td><td colspan=5></td></tr>'+
                        '<tr><td>转让方</td><td colspan=5></td></tr>'+
                        '<tr><td>受让方要求</td><td colspan=5></td></tr>'+
                        '<tr><td>特别提示</td><td colspan=5></td></tr>'+
                        '<tr><td rowspan=3>保证金要求</td>'+
                        '<td>收款账号</td>'+
                        '<td>户名</td>'+
                        '<td>开户行</td>'+
                        '<td>保证金</td>'+
                        '</tr>'+
                        '<tr>'+
                        '<td><p></p></td>'+
                        '<td><p></p></td>'+
                        '<td><p></p></td>'+
                        '<td><p></p></td>'+
                        '</tr>'+
                        '<tr>'+
                        '<td>转账备注</td>'+
                        '<td colspan=3></td>'+
                        '</tr>'+
                        '</table>';
            editor_company.html(html);
}
//KindEditor.ready(function() {
//    editor_projdes = KindEditor.create(
//            '#projdes',
//            {
//                cssPath: '/xyxqinmo/template/public/kindeditor/plugins/code/prettify.css', 
//                fileManagerJson: '/xyxqinmo/index.php/editor_file_manage/index.html', 
//                uploadJson: '/xyxqinmo/index.php/editor_upload/index.html?key=HN03B_zs',
//                allowFileManager: true,
//                filterMode: false,
//                afterUpload: function(url, data) {
//                    $('#file_id').val($('#file_id').val() + data.id + ',');
//                }
//            });
//});

function copyRow(obj) {
    var html = '<tr class="copy" style="background-color: #eef1f8">' +
            '         <td></td>' +
            '         <td colspan="5" class="text_left">' +
            '                 <input type="text" name="name[]" class="text_value" style="width: 100px;"  />：' +
            '                 <input type="text" name="content[]" class="text_value" style="width:200px;" value="" /><input name="field_type[]" type="hidden" value="1" />' +
            '         </td>' +
            ' </tr>';
    $('.copy').last().after(html)
}

function copyFileRow(obj){
    var cflen = $('.copyfile_no').length;
    var html = '<tr>'
               +'     <td></td>'
               +'     <td colspan="5" >'
               +'         <div align="left"><input type="text"  class="text_value"  name="name[]" />&emsp;&emsp;<input type="hidden" name="field_type[]" value="2" /><input type="hidden" name="content[]" value="" /></div>'
               +'    </td>'
               +'</tr>';
   $('.copyfile_no').last().after(html)
}


