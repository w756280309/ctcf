<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2016/4/12
 * Time: 10:28
 */

namespace wap\widgets;

use yii\bootstrap\Widget;

class campaign_source extends Widget
{
    private $_html;

    public function init()
    {
        $this->_html = "<script type='text/javascript'>
        $(function(){
            var r = new RegExp(\"(^|&)\"+ 'hmsr' +\"=([^&]*)(&|$)\");
            var result = window.location.search.substr(1).match(r);
            if(result && result[2]){
                $.post('/campaign/track',{'hmsr':result[2],'_csrf':'" . \Yii::$app->request->csrfToken . "'});
            }
        });
</script>";

    }

    public function run()
    {
        return $this->_html;
    }
}