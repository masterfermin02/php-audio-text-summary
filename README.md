# PHP audio to text summary

[![Latest Version on Packagist](https://img.shields.io/packagist/v/masterfermin02/vicidial-recording-transcription.svg?style=flat-square)](https://packagist.org/packages/masterfermin02/vicidial-recording-transcription)
[![Tests](https://github.com/masterfermin02/vicidial-recording-transcription/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/masterfermin02/vicidial-recording-transcription/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/masterfermin02/vicidial-recording-transcription.svg?style=flat-square)](https://packagist.org/packages/masterfermin02/vicidial-recording-transcription)

PHP package speech to text (audio, recordings) and make a summary.

This package is using the [OpenAI](https://github.com/openai-php/client) and [Google cloud speech to text](https://github.com/googleapis/google-cloud-php-speech)

## Google Authentication
Please see our [Authentication](https://github.com/googleapis/google-cloud-php/blob/main/AUTHENTICATION.md) guide for more information on authenticating your client. Once authenticated, you'll be ready to start making requests.

## ChatGPT ApiKey
[OpenAI Api](https://platform.openai.com/docs/api-reference/introduction)

## Requirement
Requires PHP 8.1+

## Installation

You can install the package via composer:

```bash
composer require masterfermin02/php-audio-text-summary
```

## Usage

```php
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;
use DaimonDove\Transcription\Transcriber;

$client = OpenAI::client('YOUR_API_KEY');
$googleCredentials = ['credentials' => 'YOUR_GOOGLE_API_CREDENTAILS'];
$speechToText = Transcriber::create($client, $googleCredentials);

# The name of the audio file to transcribe
$gcsURI = 'gs://cloud-samples-data/speech/brooklyn_bridge.raw';

# set string as audio content
$audio = (new RecognitionAudio())
    ->setUri($gcsURI);
    
# The audio file's encoding, sample rate and language
$config = new RecognitionConfig([
    'encoding' => AudioEncoding::LINEAR16,
    'sample_rate_hertz' => 16000,
    'language_code' => 'en-US'
]);

echo $speechToText->recognize($config, $audio)
->summary();

// Get others alternatives for tests
$response = $speechToText->getRecognizeText();

# Print most likely transcription
foreach ($response->getResults() as $result) {
    $alternatives = $result->getAlternatives();
    $mostLikely = $alternatives[0];
    $transcript = $mostLikely->getTranscript();
    printf('Summary: %s' . PHP_EOL, $speechToText->summaryText($transcript));
}
```

## You can also use a file resource
```php
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\StreamingRecognitionConfig;
use DaimonDove\Transcription\Transcriber;

$recognitionConfig = new RecognitionConfig();
$recognitionConfig->setEncoding(AudioEncoding::FLAC);
$recognitionConfig->setSampleRateHertz(44100);
$recognitionConfig->setLanguageCode('en-US');
$config = new StreamingRecognitionConfig();
$config->setConfig($recognitionConfig);

$audioResource = fopen('path/to/audio.flac', 'r');

$client = OpenAI::client('YOUR_API_KEY');
$googleCredentials = ['credentials' => 'YOUR_GOOGLE_API_CREDENTAILS'];
$speechToText = Transcriber::create($client, $googleCredentials);

echo $speechToText->recognizeAudioStream($config, $audioResource)->summary();

$responses $speechToText->getRecognizeAudioStreamText();

foreach ($responses as $element) {
    // doSomethingWith($element);
}
```
## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Fermin Perdomo](https://github.com/masterfermin02)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
