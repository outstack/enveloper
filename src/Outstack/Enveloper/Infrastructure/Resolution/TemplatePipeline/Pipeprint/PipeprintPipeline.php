<?php

namespace Outstack\Enveloper\Infrastructure\Resolution\TemplatePipeline\Pipeprint;

use League\Flysystem\Filesystem;
use Outstack\Enveloper\Domain\Resolution\Templates\Pipeline\Exceptions\PipelineFailed;
use Outstack\Enveloper\Domain\Resolution\Templates\Pipeline\TemplatePipeline;

class PipeprintPipeline implements TemplatePipeline
{
    private $extensionsWithoutSemantics = ['txt', 'html', 'text'];

    /**
     * @var string
     */
    private $pipeprintUrl;
    /**
     * @var Filesystem
     */
    private $templateFilesystem;

    public function __construct(Filesystem $templateFilesystem, string $pipeprintUrl)
    {
        $this->pipeprintUrl = $pipeprintUrl;
        $this->templateFilesystem = $templateFilesystem;
    }

    public function render(string $templateName, string $templateContents, object $parameters): string
    {
        $pipeline = [];
        $parts = explode('.', $templateName);
        foreach (array_reverse(array_slice($parts, 1)) as $part) {
            if (in_array($part, $this->extensionsWithoutSemantics)) {
                continue;
            }
            $pipeline[] = ['engine' => $part];
        }

        $pipeline[0]['template'] = "template/$templateName";

        $files = [
            "template/$templateName" => $templateContents
        ];
        foreach ($this->templateFilesystem->listContents('./_includes/') as ['path' => $include]) {
            $files[$include] = $this->templateFilesystem->read($include);
        }

        $pipeprintRequest = json_encode(
            [
                'files' => $files,
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
        }
        return $response;
    }
}