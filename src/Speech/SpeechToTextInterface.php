<?php

namespace DaimonDove\Transcription\Speech;

use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\StreamingRecognitionConfig;

interface SpeechToTextInterface
{
    /**
     * @param StreamingRecognitionConfig $config
     * @param resource         $audioResource
     * @param array         $options {
     *                                                   Optional.
     *
     *     @type int $timeoutMillis
     *          Timeout to use for this call.
     * }
     *
     * @return $this
     */
   public function recognizeAudioStream(StreamingRecognitionConfig $config, $audioResource, $options = []): self;

   public function recognize(RecognitionConfig $config, RecognitionAudio $audio): self;

   public function summary(): string;
}
