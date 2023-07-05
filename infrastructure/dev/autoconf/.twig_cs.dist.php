<?php

declare(strict_types=1);

use FriendsOfTwig\Twigcs;

$finder = Twigcs\Finder\TemplateFinder::create()
    ->in(__DIR__.'/templates')
;

return Twigcs\Config\Config::create()
    ->addFinder($finder)
    ->setSeverity('error')
    ->setRuleSet(FriendsOfTwig\Twigcs\Ruleset\Official::class)
    ;
