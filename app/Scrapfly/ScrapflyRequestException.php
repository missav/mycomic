<?php

namespace App\Scrapfly;

use Exception;

class ScrapflyRequestException extends Exception
{
    public function __construct(protected array $scrapflyResponse)
    {
        parent::__construct(
            $this->scrapflyResponse['message'],
            $this->scrapflyResponse['http_code'],
        );
    }

    public function getResponse(): array
    {
        return $this->scrapflyResponse;
    }
}
