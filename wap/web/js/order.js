﻿/**
 * Created by lcl on 2015/11/16.
 */
var csrf;
$(function(){
   csrf = $("meta[name=csrf-token]").attr('content');
   $('#buybtn').bind('click',function(){
       if($('#money').val()==''){
           toast(this,'投资金额不能为空');
                    return false;
       }
       if($('#trade_pwd').val()==''){
           toast(this,'交易密码不能为空');
                    return false;
       }
       vals = $("#orderform").serialize();
       //console.log(vals);
       $.post($("#orderform").attr("action"), vals, function (data) { 
           if(data.code==0){
               toastWithMastUrl('/user/user/myorder','购买成功')
           }else{
               toast(this,data.message);
           }
          
           if(data.tourl!=undefined){
               location.href=data.tourl;
            }
        }); 
   });
   $('#money').blur(function(){
       money = $(this).val();
       if (isNaN(money)) { 
            toast(this,'交易密码不能为空');
            money = 0;
            $(this).focus();
            return false;
        } 
        
        $('.yuqishouyi').html(accDiv(accMul(accMul(money,yr),qixian),360).toFixed(2)+"元");
   }); 
})


