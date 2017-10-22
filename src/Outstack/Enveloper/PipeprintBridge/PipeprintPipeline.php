<?php

namespace Outstack\Enveloper\PipeprintBridge;

use Outstack\Enveloper\PipeprintBridge\Exceptions\PipelineFailed;
use Outstack\Enveloper\Templates\TemplatePipeline;

class PipeprintPipeline implements TemplatePipeline
{
    private $extensionsWithoutSemantics = ['txt', 'html', 'text'];

    /**
     * @var string
     */
    private $pipeprintUrl;

    public function __construct(string $pipeprintUrl)
    {
        $this->pipeprintUrl = $pipeprintUrl;
    }

    public function render(string $templateName, string $templateContents, array $parameters): string
    {
        $pipeline = [];
        $parts = explode('.', $templateName);
        foreach (array_slice($parts, 1) as $part) {
            if (in_array($part, $this->extensionsWithoutSemantics)) {
                continue;
            }
            $pipeline[] = ['engine' => $part];
        }

        $pipeline[0]['template'] = $templateName;

        $pipeprintRequest = json_encode(
            [
                'files' => [
                    $templateName => $templateContents
                ],
                'pipeline' => $pipeline,
                'parameters' => $parameters
            ]
        );

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "{$this->pipeprintUrl}/render/pipeline",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $pipeprintRequest,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);

        curl_close($curl);

        if ($err || $statusCode < 200 || $statusCode > 299) {
            $errorData = null;
            if ($contentType == 'application/problem+json') {
                $errorData = json_decode($response, true);
            }
            throw new PipelineFailed($err, $errorData);
        } else {
            return $response;
        }
    }
}