<?php

$this->title = '联系我们';
$this->registerCssFile(ASSETS_BASE_URI.'css/help/contact.css', ['depends' => 'frontend\assets\FrontAsset']);

?>
<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=jfMUQzWO1Wlf9jc9bFBBEXWbLVzlPNIZ"></script>
<div class="wdjf-body">
    <div class="wdjf-ucenter clearfix">
        <div class="leftmenu">
            <?= $this->render('@frontend/views/news/left_nav.php') ?>
        </div>
        <div class="rightcontent">
            <div class="contact-box">
                <div class="contact-header">
                    <span class="contact-header-font">联系我们</span>
                </div>
                <div class="contact-content">
                    <div class="location">
                        <!--<img style="width: 664px;"  src="<?= ASSETS_BASE_URI ?>ctcf/images/help/location.png">-->
                        <!--百度地图容器-->
                        <div style="width:100%;margin-bottom:30px;height:350px;border:#ccc solid 1px;font-size:12px" id="map"></div>
                    </div>
                    <p>公司地址 : 武汉市武昌区东湖路181号楚天文化创意产业园区1号楼6层</p>
                    <p>工作时间 : 9:00-17:30（周一至周五）</p>
                    <p>客服电话 : <?= \Yii::$app->params['platform_info.contact_tel'] ?></p>
                    <p>客服时间 : 9:00-20:00（周一至周日，节假日例外）</p>
                </div>
            </div>
        </div>
    </div>
    <div class="clear"></div>
</div>
<script type="text/javascript">
    //创建和初始化地图函数：
    function initMap(){
        createMap();//创建地图
        setMapEvent();//设置地图事件
        addMapControl();//向地图添加控件
        addMapOverlay();//向地图添加覆盖物
    }
    function createMap(){
        map = new BMap.Map("map");
        map.centerAndZoom(new BMap.Point(114.372143,30.572199),16);
    }
    function setMapEvent(){
        map.enableScrollWheelZoom();
        map.enableKeyboard();
        map.enableDragging();
        map.enableDoubleClickZoom()
    }
    function addClickHandler(target,window){
        target.addEventListener("click",function(){
            target.openInfoWindow(window);
        });
    }
    function addMapOverlay(){
        var markers = [
            {content:"武汉市武昌区东湖路181号楚天文化创意产业园区8号楼",title:"楚天财富",imageOffset: {width:0,height:3},position:{lat:30.572945,lng:114.371559}}
        ];
        for(var index = 0; index < markers.length; index++ ){
            var point = new BMap.Point(markers[index].position.lng,markers[index].position.lat);
            var marker = new BMap.Marker(point,{icon:new BMap.Icon("http://api.map.baidu.com/lbsapi/createmap/images/icon.png",new BMap.Size(20,25),{
                    imageOffset: new BMap.Size(markers[index].imageOffset.width,markers[index].imageOffset.height)
                })});
            var label = new BMap.Label(markers[index].title,{offset: new BMap.Size(25,5)});
            var opts = {
                width: 200,
                title: markers[index].title,
                enableMessage: false
            };
            var infoWindow = new BMap.InfoWindow(markers[index].content,opts);
            marker.setLabel(label);
            addClickHandler(marker,infoWindow);
            map.addOverlay(marker);
        };
        var labels = [
        ];
        for(var index = 0; index < labels.length; index++){
            var opt = { position: new BMap.Point(labels[index].position.lng,labels[index].position.lat )};
            var label = new BMap.Label(labels[index].content,opt);
            map.addOverlay(label);
        };
    }
    //向地图添加控件
    function addMapControl(){
        var navControl = new BMap.NavigationControl({anchor:BMAP_ANCHOR_TOP_LEFT,type:BMAP_NAVIGATION_CONTROL_LARGE});
        map.addControl(navControl);
    }
    var map;
    initMap();
</script>

