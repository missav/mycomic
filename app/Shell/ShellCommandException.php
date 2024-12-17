<?php

namespace App\Shell;

use Exception;
use Spatie\FlareClient\Contracts\ProvidesFlareContext;

class ShellCommandException extends Exception implements ProvidesFlareContext
{
    protected string $command;

    protected array $output;

    protected string $body;

    public function __construct(string $command, array $output)
    {
        $this->command = $command;
        $this->output = $output;
        $this->body = implode("\n", $output);

        $outputTail = substr($this->body, -200);

        parent::__construct("Shell command [{$this->command}] failed.  Output: [{$outputTail}]");
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getOutput(): array
    {
        return $this->output;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function context(): array
    {
        return [
            'Shell output' => $this->getOutput(),
        ];
    }
}
