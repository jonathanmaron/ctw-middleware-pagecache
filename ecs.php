<?php
declare(strict_types=1);

use Ctw\Qa\EasyCodingStandard\Config\ECSConfig\DefaultFileExtensions;
use Ctw\Qa\EasyCodingStandard\Config\ECSConfig\DefaultIndentation;
use Ctw\Qa\EasyCodingStandard\Config\ECSConfig\DefaultLineEnding;
use Ctw\Qa\EasyCodingStandard\Config\ECSConfig\DefaultRules;
use Ctw\Qa\EasyCodingStandard\Config\ECSConfig\DefaultRulesWithConfiguration;
use Ctw\Qa\EasyCodingStandard\Config\ECSConfig\DefaultSets;
use Ctw\Qa\EasyCodingStandard\Config\ECSConfig\DefaultSkip;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {

    $fileExtensions = new DefaultFileExtensions();
    $indentation    = new DefaultIndentation();
    $lineEnding     = new DefaultLineEnding();
    $rules          = new DefaultRules();
    $rulesConfig    = new DefaultRulesWithConfiguration();
    $sets           = new DefaultSets();
    $skip           = new DefaultSkip();

    $ecsConfig->fileExtensions($fileExtensions());

    $ecsConfig->indentation($indentation());

    $ecsConfig->lineEnding($lineEnding());

    $ecsConfig->paths(
        [
            sprintf('%s/src', __DIR__),
            sprintf('%s/test', __DIR__),
            sprintf('%s/ecs.php', __DIR__),
            sprintf('%s/rector.php', __DIR__),
        ]
    );

    $ecsConfig->sets($sets());

    $ecsConfig->rules($rules());

    $ecsConfig->rulesWithConfiguration($rulesConfig());

    $ecsConfig->skip($skip());
};
