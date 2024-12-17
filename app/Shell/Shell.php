<?php

namespace App\Shell;

class Shell
{
    public static bool $debug = false;

    public static function enableDebug(): void
    {
        static::$debug = true;
    }

    public static function exec(string $command): array
    {
        if (config('app.debug') || static::$debug) {
            $command = str_replace(' 2>&1', '', $command);
        }

        exec($command, $output, $returnCode);

        if ($returnCode) {
            throw new ShellCommandException($command, $output);
        }

        return $output;
    }
}
