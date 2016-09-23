$(function() {
    $('.m4 img').on('click',function() {
        $('#chart-box').stop(true,false).fadeToggle();
    });

    function getCountDown(timestamp)
    {
        function daojishi()
        {
            var nowTime = new Date();
            var endTime = new Date(timestamp * 1000);

            if (nowTime >= endTime) {
                d = hour = min = sec = 0;
            } else {
                var t = endTime.getTime()-nowTime.getTime();
                var d = Math.floor(t/1000/60/60/24);
                var hour = Math.floor(t/1000/60/60%24);
                var min = Math.floor(t/1000/60%60);
                var sec = Math.floor(t/1000%60);
            }

            var countDownTime ="距离结束："+d+"天"+hour+"时"+min+"分"+sec+"秒";
            $(".daojishi .col-xs-12 div").html(countDownTime);
        }
        daojishi();
        setInterval(function() {
            daojishi();
        }, 1000);
    }
    getCountDown(remainTime);
});