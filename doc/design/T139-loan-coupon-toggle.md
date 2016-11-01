- 在online_product表上面新增字段,标记该标的是否可以使用代金券,如果为真,则该标的可以使用代金券,具体设计如下:
```
allowUseCoupon tinyint(1) default 0 NOT NULL
```
- 该任务主要是在建标的时候新增一个标志位,标志该标的是否可以使用代金券,如果勾选,则可以使用代金券,否则,不可以使用代金券,并在购买的时候隐藏
使用代金券的相关模块,同时在controller里面增加相应的控制,如果该标的不允许使用代金券且传了代金券ID,则提示用户该标的不允许使用代金券,修改
文件位置如下:
```
backend/modules/product/controllers/ProductonlineController.php
backend/modules/product/views/productonline/edit.php
wap/modules/order/controllers/OrderController.php
wap/modules/order/views/order/index.php
wap/web/css/xiangqing.css
frontend/modules/deal/controllers/DealController.php
frontend/modules/deal/views/deal/detail.php
```
- 需要新增migration文件,以及修改OnlineProduct类和PayService类,以及UserCoupon类,在其中增加标的是否可用代金券限制的控制,具体修改路径如下:
```
common/models/coupon/UserCoupon.php
common/models/product/OnlineProduct.php
common/service/PayService.php
console/migrations/m161031_093818_alter_online_product_table.php
```
- 测试的时候,新建一个允许使用代金券的标的和一个不允许使用代金券的标的,然后按照正常流程去购买,看是否可以使用代金券;前者可以使用代金券,后
者没有选择代金券的入口。