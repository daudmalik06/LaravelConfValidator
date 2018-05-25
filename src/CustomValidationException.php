<?php

namespace Dawood\LaravelConfValidator;

use RuntimeException;
use Illuminate\Contracts\Support\MessageProvider;

class CustomValidationException extends RuntimeException
{
    /**
     * The message provider implementation.
     *
     * @var \Illuminate\Contracts\Support\MessageProvider
     */
    protected $provider;

    /**
     * Create a new validation exception instance.
     *
     * @param  \Illuminate\Contracts\Support\MessageProvider  $provider
     * @return void
     */
    public function __construct(MessageProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Get the validation error message provider.
     *
     * @return \Illuminate\Contracts\Support\MessageBag
     */
    public function errors()
    {
        return $this->provider->getMessageBag();
    }

    /**
     * Get the validation error message provider.
     *
     * @return \Illuminate\Contracts\Support\MessageProvider
     */
    public function getMessageProvider()
    {
        return $this->provider;
    }
}