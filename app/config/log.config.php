<?php

declare(strict_types=1);

use Tempcord\Logging\ConsoleLogChannel;
use Tempest\Log\Config\MultipleChannelsLogConfig;

return new MultipleChannelsLogConfig(
    channels: [
        new ConsoleLogChannel(
            except: [],
        ),
    ],
    prefix: null,
);
