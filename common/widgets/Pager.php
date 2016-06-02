<?php

namespace common\widgets;

use yii\widgets\LinkPager;
use yii\helpers\Html;

class Pager extends LinkPager
{
    public $internalButtonCount = 5;
    public $ellipsis = '……';

    public function run()
    {
        $this->view->registerCss('.active a{color:red;}');
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

        $buttons = [];
        $currentPage = $this->pagination->getPage();

        // internal pages
        $endPage = $pageCount;
        $currentPage++;
        if ($endPage <= $this->internalButtonCount + 2) {
            for ($i = 1; $i <= $endPage; $i++) {
                $buttons[] = $this->renderPageButton($i, $i - 1, null, false, $i == $currentPage);
            }
        } else {
            //偏移量
            $offset = ceil(($this->internalButtonCount - 1) / 2);
            $linkOptions = $this->linkOptions;
            if ($currentPage >= 1 && $currentPage <= 1 + $offset) {
                for ($i = 1; $i <= $this->internalButtonCount; $i++) {
                    $buttons[] = $this->renderPageButton($i, $i - 1, null, false, $i == $currentPage);
                }
                if ($this->internalButtonCount < $endPage - 1) {
                    $buttons[] = Html::tag('li', Html::a($this->ellipsis, null, $linkOptions), $linkOptions);
                    $buttons[] = $this->renderPageButton($endPage, $endPage - 1, null, false, $endPage == $currentPage);
                }
            } elseif ($currentPage > 1 + $offset && $currentPage < $endPage - $offset) {
                if ($currentPage == 2 + $offset) {
                    for ($i = 1; $i <= min($currentPage + $offset, $this->internalButtonCount); $i++) {
                        $buttons[] = $this->renderPageButton($i, $i - 1, null, false, $i == $currentPage);
                    }
                }
                if ($currentPage > 2 + $offset && $currentPage < $endPage - 1 - $offset) {
                    $buttons[] = $this->renderPageButton(1, 1 - 1, null, false, 1 == $currentPage);
                    if ($currentPage - $offset > 1 + 1) {
                        $buttons[] = Html::tag('li', Html::a($this->ellipsis, null, $linkOptions), $linkOptions);
                    }
                    for ($i = $currentPage - $offset; $i <= $currentPage + $offset; $i++) {
                        $buttons[] = $this->renderPageButton($i, $i - 1, null, false, $i == $currentPage);
                    }
                }
                if ($currentPage == $endPage - 1 - $offset) {
                    $buttons[] = $this->renderPageButton(1, 1 - 1, null, false, 1 == $currentPage);
                    if ($currentPage - $offset > 1 + 1) {
                        $buttons[] = Html::tag('li', Html::a($this->ellipsis, null, $linkOptions), $linkOptions);
                    }
                    for ($i = $endPage - $this->internalButtonCount + 1; $i < $endPage; $i++) {
                        $buttons[] = $this->renderPageButton($i, $i - 1, null, false, $i == $currentPage);
                    }
                }
                if ($currentPage + $offset < $endPage - 1) {
                    $buttons[] = Html::tag('li', Html::a($this->ellipsis, null, $linkOptions), $linkOptions);
                }
                $buttons[] = $this->renderPageButton($endPage, $endPage - 1, null, false, $endPage == $currentPage);
            } elseif ($currentPage >= $endPage - $offset && $currentPage <= $endPage) {
                $buttons[] = $this->renderPageButton(1, 1 - 1, null, false, 1 == $currentPage);
                $buttons[] = Html::tag('li', Html::a($this->ellipsis, null, $linkOptions), $linkOptions);
                for ($i = $endPage - $this->internalButtonCount + 1; $i <= $endPage; $i++) {
                    $buttons[] = $this->renderPageButton($i, $i - 1, null, false, $i == $currentPage);
                }
            }
        }

        return Html::tag('ul', implode("\n", $buttons), $this->options);
    }
}