<?php

namespace common\widgets;

use yii\widgets\LinkPager;
use yii\helpers\Html;

class Pager extends LinkPager
{
    public $internalButtonCount = 5;
    public $ellipsis = '...';
    public $prevPageLabel = '上一页';
    public $nextPageLabel = '下一页';
    public $ellipsisClass = 'ellipsis';

    public function run()
    {
        if ($this->registerLinkTags) {
            $this->registerLinkTags();
        }
        echo $this->renderPageButtons();
    }

    public function renderPageButtons()
    {
        $pageCount = $this->pagination->getPageCount();
        if ($pageCount < 2 && $this->hideOnSinglePage) {
            return '';
        }
        $currentPage = $this->pagination->getPage();
        $currentPage++;
        $buttons = [];
        $endPage = $pageCount;
        $startPage = 1;
        // prev page
        if ($this->prevPageLabel !== false) {
            $page = max($currentPage - 1, $startPage);
            $buttons[] = $this->renderPageButton($this->prevPageLabel, $page - 1, $this->prevPageCssClass, $currentPage <= $startPage, false);
        }

        // internal pages
        $offset = ceil(($this->internalButtonCount - 1) / 2);
        $linkOptions = $this->linkOptions;
        //总页数没有超过 $this->internalButtonCount + 1 的时候，打印全部
        if ($this->internalButtonCount + 1 >= $endPage) {
            for ($i = 1; $i <= $endPage; $i++) {
                $buttons[] = $this->renderPageButton($i, $i - 1, null, false, $i == $currentPage);
            }
        } else {
            if ($currentPage >= 1 && $currentPage <= $startPage + $offset) {
                for ($i = $startPage; $i <= $this->internalButtonCount; $i++) {
                    $buttons[] = $this->renderPageButton($i, $i - 1, null, false, $i == $currentPage);
                }
                if (max($currentPage + $offset, $startPage - 1 + $this->internalButtonCount) < $endPage - 1) {
                    $buttons[] = Html::tag('li', Html::a($this->ellipsis, null, array_merge(['class' => $this->ellipsisClass], $linkOptions)), $linkOptions);
                }
                $buttons[] = $this->renderPageButton($endPage, $endPage - 1, null, false, $endPage == $currentPage);
            } elseif ($currentPage > $startPage + $offset && $currentPage < $endPage - $offset) {
                //第一页
                $buttons[] = $this->renderPageButton($startPage, $startPage - 1, null, false, $startPage == $currentPage);
                //判断开始省略号
                if ($currentPage - $offset > $startPage + 1) {
                    $buttons[] = Html::tag('li', Html::a($this->ellipsis, null, array_merge(['class' => $this->ellipsisClass], $linkOptions)), $linkOptions);
                }
                for ($i = max($startPage + 1, $currentPage - $offset); $i <= min($endPage - 1, $currentPage + $offset); $i++) {
                    $buttons[] = $this->renderPageButton($i, $i - 1, null, false, $i == $currentPage);
                }
                if ($currentPage + $offset < $endPage - 1) {
                    $buttons[] = Html::tag('li', Html::a($this->ellipsis, null, array_merge(['class' => $this->ellipsisClass], $linkOptions)), $linkOptions);
                }
                //最后一页
                $buttons[] = $this->renderPageButton($endPage, $endPage - 1, null, false, $endPage == $currentPage);
            } elseif ($currentPage >= $endPage - $offset && $currentPage <= $endPage) {
                $buttons[] = $this->renderPageButton($startPage, $startPage - 1, null, false, 1 == $currentPage);
                if (min($currentPage - $offset, $endPage + 1 - $this->internalButtonCount) > $startPage + 1) {
                    $buttons[] = Html::tag('li', Html::a($this->ellipsis, null, array_merge(['class' => $this->ellipsisClass], $linkOptions)), $linkOptions);
                }
                for ($i = min($endPage + 1, $endPage - ($this->internalButtonCount - 1)); $i <= $endPage; $i++) {
                    $buttons[] = $this->renderPageButton($i, $i - 1, null, false, $i == $currentPage);
                }
            }
        }


        // next page
        if ($this->nextPageLabel !== false) {
            $page = $currentPage + 1;
            $page = min($page, $endPage);
            $buttons[] = $this->renderPageButton($this->nextPageLabel, $page - 1, $this->nextPageCssClass, $currentPage >= $pageCount, false);
        }

        return Html::tag('ul', implode("\n", $buttons), $this->options);
    }
}
