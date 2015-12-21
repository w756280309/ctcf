<?php

use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = "会员视图";
?>
<?php $this->beginBlock('blockmain'); ?>
<div class="page_form">
    <style>
        .uv th{
            width: 10%;
            font-weight: bold;
            font-size: 14px;
        }
        .uv td{
            width: 40%;
        }
        
        .button{
            margin:0;
        }
    </style>
       
        <?php $form = ActiveForm::begin(['id'=>'admin_form', 'action' =>'','enableClientScript' => true,'enableClientValidation' => true]); ?>
            <div class="page_table form_table uv">
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <th> <?= $form->field($model, 'usercode', ['template' => '{label}']);?></th>
                        <td><?= $model['usercode']?></td>
                        <th><?= $form->field($model, 'org_name', ['template' => '{label}']);?></th>
                        <td><?= $model['org_name']?></td>

                    </tr>
                    <tr>
                        <th> <?= $form->field($model, 'username', ['template' => '{label}']);?></th>
                        <td><?= $model['username']?></td>
                        <th><?= $form->field($model, 'real_name', ['template' => '{label}']);?></th>
                        <td><?= $model['real_name']?></td>

                    </tr>
                    <tr>
                        <th><?= $form->field($model, 'user_pass', ['template' => '{label}']);?></th>
                        <td>
                            ******
                        </td>
                        <th><?= $form->field($model, 'mobile', ['template' => '{label}']);?></th>
                        <td><?= $model['mobile']?></td>
                    </tr>
                    
                    <tr>
                        <th><?= $form->field($model, 'tel', ['template' => '{label}']);?></th>
                        <td>
                            <?= $model['tel']?>
                        </td>
                        <th><?= $form->field($model, 'in_time', ['template' => '{label}']);?></th>
                        <td><?= date('Y-m-d H:i',$model['in_time']);?></td>
                    </tr>
                    
                    <tr>
                        <th><?= $form->field($model, 'cat_id', ['template' => '{label}']);?></th>
                        <td>
                            <?= $type?>
                        </td>
                        <th><?= $form->field($model, 'org_code', ['template' => '{label}']);?></th>
                        <td><?= $model['org_code'];?></td>
                    </tr>
                    
                     <tr>
                        <th><?= $form->field($model, 'shui_code', ['template' => '{label}']);?></th>
                        <td>
                            <?= $model['shui_code'];?>
                        </td>
                        <th><?= $form->field($model, 'law_master', ['template' => '{label}']);?></th>
                        <td><?= $model['law_master'];?></td>
                    </tr>
                    
                    <tr>
                        <th><?= $form->field($model, 'law_master_idcard', ['template' => '{label}']);?></th>
                        <td>
                            <?= $model['law_master_idcard'];?>
                        </td>
                        <th><?= $form->field($model, 'email', ['template' => '{label}']);?></th>
                        <td><?= $model['email'];?></td>
                    </tr>
                    
                    <tr>
                        <th><?= $form->field($model, 'org_url', ['template' => '{label}']);?></th>
                        <td>
                            <?= $model['org_url'];?>
                        </td>
                        <th>审核状态</th>
                        <td style="color:red"><?=$examin ?></td>
                    </tr>
                    
                    <tr>
                        <th></th>
                        <td><button type="button" class="button" data-bind="1" onclick="examin(1);"> 通 过 </button></td>
                        <th><button type="button" class="button" data-bind="-1" onclick="examin(-1);"> 驳 回 </button></th>
                        <td></td>
                    </tr>
                    
                </table>
            </div>

        <?php $form->end(); ?>
    
    <script type="text/javascript">
        var id = '<?= $model['id'];?>';
        var csrftoken= '<?= Yii::$app->request->getCsrfToken(); ?>';
    function examin(val){
        $.post("/user/user/examin?id="+id,{field:'examin_status',val:val,_csrf:csrftoken},function(data)
        {
            var da = eval(("("+data+")"));
            if(da['result']!="0"){
                location.reload();
            }else{
                alert(da['msg'])
            }
        });
    }
    </script>
    </div>
<?php $this->endBlock(); ?>