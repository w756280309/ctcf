<?php

use yii\helpers\Html;

if (!isset($tagsCount) || !is_integer($tagsCount)) {
    $tagsCount = 2;
}

if ($loan->pointsMultiple > 1) {
    echo '<span class="red">积分'.Html::encode($loan->pointsMultiple).'倍</span>';
    --$tagsCount;
}

if (null !== $loan->tags) {
    $tags = explode('，', $loan->tags);
    $tagKey = array_search('慈善专属', $tags);

    if (false !== $tagKey) {
        unset($tags[$tagKey]);
        echo '<span class="red">慈善专属</span>';
        --$tagsCount;
    }

    if ($tagsCount > 0) {
        foreach($tags as $tag) {
            if ($tagsCount-- > 0 && !empty($tag)) {
                echo '<span>'.Html::encode($tag).'</span>';
            }
        }
    }
}

?>