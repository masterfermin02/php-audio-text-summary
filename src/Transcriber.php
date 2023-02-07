<?php

namespace DaimonDove\Transcription;

use Google\Cloud\Speech\V1\SpeechClient;
use DaimonDove\Transcription\Speech\Client;
use OpenAI\Client as OpenAIClient;

final class Transcriber
{
    /**
     * @param array<int,string> $options
     * @return Client
     * @throws \Google\ApiCore\ValidationException
     */
    public static function create(OpenAIClient $openAIClient, array $options = []): Client
    {
        return new Client(
            new SpeechClient($options),
            $openAIClient
        );
    }
}
