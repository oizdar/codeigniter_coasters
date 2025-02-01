<?php

namespace App\Libraries\Coasters;

class CoasterStatusData
{
    public function __construct(
        private array $errors = []
    ) {
    }

    public function addError(string $error): void
    {
        $this->errors[] = $error;
    }

    public function isOk(): bool
    {
        return count($this->errors) === 0;
    }

    public function getErrorMessage(): string
    {
        return lang('Messages.coaster.status.problem') . ucfirst(implode(", ", $this->errors));
    }

    public function getOkMessage(): string
    {
        return lang('Messages.coaster.status.ok');
    }
}