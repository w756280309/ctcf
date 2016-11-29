<?=
\yii\grid\GridView::widget([
    'id' => 'grid_view_draw',
    'dataProvider' => $dataProvider,
    'layout' => '{items} <center><div class="pagination draw_pager">{pager}</div></center>',
    'tableOptions' => ['class' => 'draw_record_list table table-hover table-striped'],
    'columns' => [
        [
            'label' => '流水号',
            'value' => function ($record) {
                return $record['sn'];
            }
        ],
        [
            'label' => '金额',
            'value' => function ($record) {
                return number_format($record['money'], 2);
            }
        ],
        [
            'label' => '银行',
            'value' => function ($record) {
                return $record['bankName'];
            }
        ],
        [
            'label' => '手续费',
            'value' => function ($record) {
                return $record['fee'];
            }
        ],
        [
            'label' => '交易时间',
            'value' => function ($record) {
                return date('Y-m-d H:i:s',$record['created_at']);
            }
        ],
        [
            'label' => '状态',
            'value' => function ($record) {
                return isset(Yii::$app->params['draw_status'][$record['status']]) ? Yii::$app->params['draw_status'][$record['status']] : '---';
            }
        ],
        [
            'label' => '联动状态',
            'format' => 'raw',
            'value' => function ($record) {
                return '<button class="btn btn-primary get_order_status" drawid="'.$record['id'].'">查询流水在联动状态</button>';
            }
        ],
    ],
])
?>

<script>
    $(function(){
        $('.draw_pager ul li').on('click', 'a', function(e) {
            e.preventDefault();
            getDrawList($(this).attr('href'));
        });

        //点击获取流水在联动的状态
        $('.get_order_status').on('click', function () {
            var _this = $(this);
            var id = _this.attr('drawid');
            if (id) {
                var xhr = $.get('/user/drawrecord/ump-status?id='+id, function (data) {
                    _this.parent().html(data.message);
                });

                xhr.fail(function() {
                    newalert(0, '联动接口请求失败');
                });
            }
        });
    })
</script>

