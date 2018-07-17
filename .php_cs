<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/common')
    ->in(__DIR__.'/frontend')
    ->in(__DIR__.'/wap')
    ->in(__DIR__.'/api')
    ->in(__DIR__.'/backend')
    ->in(__DIR__.'/console')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'ordered_imports' => true,
        'phpdoc_align' => false,
        'phpdoc_summary' => false,
        'phpdoc_to_comment' => false,
    ])
    ->setFinder($finder)
;
