<?php

namespace DaimonDove\Transcription\Speech;

use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\StreamingRecognitionConfig;
use OpenAI\Client as OpenAIClient;
use Google\Cloud\Speech\V1\StreamingRecognizeResponse;
use Google\Protobuf\Internal\RepeatedField;

final readonly class Client implements SpeechToTextInterface
{

    /**
     * @var StreamingRecognizeResponse[]
     */
    protected array $responses;

    protected RepeatedField $recognizeResponse;

    public function __construct(
        public SpeechClient $speechClient,
        public OpenAIClient $openAIClient
    ) {
    }

    public function recognizeAudioStream(StreamingRecognitionConfig $config, $audioResource, $options = []): SpeechToTextInterface
    {
       $this->responses  = $this->speechClient->recognizeAudioStream($config, $audioResource);

       return $this;
    }

    public function recognize(RecognitionConfig $config, RecognitionAudio $audio): SpeechToTextInterface
    {
        $this->recognizeResponse = $this->speechClient->recognize($config, $audio)->getResults();

        return $this;
    }

    public function summary(): string
    {
        $text = $this->getText();

        $result = $this->openAIClient->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $text,
            'temperature' => 0.7,
            'max_tokens' => 60,
            'top_p' => 1.0,
            'frequency_penalty' => 0.0,
            'presence_penalty' => 1,
        ]);

       return $result['choices'][0]['text'];
    }

    public function summaryText(string $text): string
    {
        $result = $this->openAIClient->completions()->create([
            'model' => 'text-davinci-003',
            'prompt' => $text,
            'temperature' => 0.7,
            'max_tokens' => 60,
            'top_p' => 1.0,
            'frequency_penalty' => 0.0,
            'presence_penalty' => 1,
        ]);

        return $result['choices'][0]['text'];
    }

    /**
     * @throws \Exception
     */
    private function getText(): string
    {
        if (isset($this->responses)) {
            foreach ($this->responses as $response) {
                foreach ($response->getResults() as $result) {
                    $alternatives = $result->getAlternatives();
                    $mostLikely = $alternatives[0];
                    return $mostLikely->getTranscript();
                }
            }
        }

        foreach ($this->recognizeResponse as $result) {
            $alternatives = $result->getAlternatives();
            $mostLikely = $alternatives[0];
            return $mostLikely->getTranscript();
        }

        throw new \Exception('There are not transcript option');
    }

    public function getRecognizeText(): RepeatedField
    {
        return $this->recognizeResponse;
    }

    /**
     * @return StreamingRecognizeResponse[]
     */
    public function getRecognizeAudioStreamText(): array
    {
        return $this->responses;
    }

    public function __destruct()
    {
        $this->speechClient->close();
    }
}
