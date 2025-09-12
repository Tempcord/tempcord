<?php

declare(strict_types=1);

use Tempcord\Logging\ConsoleLogChannel;
use Tempest\Log\LogConfig;

return new LogConfig(
    channels: [
        new ConsoleLogChannel(
            except: [],
        ),
    ],
);
