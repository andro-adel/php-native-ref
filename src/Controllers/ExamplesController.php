<?php

namespace App\Controllers;

use App\Http\Request;
use App\Http\Response;
use App\Domain\Examples\FeatureShowcase;

class ExamplesController
{
    /**
     * أمثلة سريعة على مزايا PHP 8.3
     */
    public function features(Request $request): void
    {
        $jsonSample = $request->rawBody ?: '{"name":"A","age":30}';
        $isValidJson = function_exists('json_validate')
            ? json_validate($jsonSample)
            : (json_decode($jsonSample) !== null);

        Response::json([
            'typed_constant' => FeatureShowcase::typedConstantExample(),
            'override_attribute' => FeatureShowcase::overrideExample(),
            'readonly_clone' => FeatureShowcase::readonlyCloneExample(),
            'randomizer_token' => FeatureShowcase::randomizerExample(),
            'enum_example' => FeatureShowcase::enumExample(),
            'intl_number' => FeatureShowcase::intlNumberExample(),
            'intl_date' => FeatureShowcase::intlDateExample(),
            'streams' => FeatureShowcase::streamsExample(),
            'streams_file' => FeatureShowcase::streamsFileExample(),
            'fiber' => FeatureShowcase::fiberExample(),
            'fiber_steps' => FeatureShowcase::fiberStepsExample(),
            'attribute' => FeatureShowcase::attributeExample(),
            'opcache' => FeatureShowcase::opcacheInfo(),
            'json_validate' => [
                'input' => $jsonSample,
                'is_valid' => $isValidJson,
            ],
        ]);
    }

    /**
     * مسار مخصص لفحص JSON بسرعة باستخدام json_validate
     */
    public function validateJson(Request $request): void
    {
        $body = $request->rawBody;
        if ($body === '') {
            Response::json(['error' => 'body required'], 400);
            return;
        }

        $isValid = function_exists('json_validate')
            ? json_validate($body)
            : (json_decode($body) !== null);

        Response::json([
            'is_valid' => $isValid,
            'length' => strlen($body),
        ], $isValid ? 200 : 422);
    }

    /**
     * مسار لتوليد توكن آمن باستخدام Random\Randomizer
     */
    public function random(Request $request): void
    {
        $token = FeatureShowcase::randomizerExample();
        Response::json(['token' => $token]);
    }

    /**
     * مسار يعرض الـ Enum
     */
    public function enum(Request $request): void
    {
        Response::json(FeatureShowcase::enumExample());
    }

    /**
     * مسار تنسيقات رقمية باستخدام intl NumberFormatter
     */
    public function intl(Request $request): void
    {
        Response::json(FeatureShowcase::intlNumberExample());
    }

    public function intlDate(Request $request): void
    {
        Response::json(FeatureShowcase::intlDateExample());
    }

    /**
     * مسار Streams بسيط
     */
    public function streams(Request $request): void
    {
        Response::json(FeatureShowcase::streamsExample());
    }

    public function streamsFile(Request $request): void
    {
        Response::json(FeatureShowcase::streamsFileExample());
    }

    /**
     * مسار Fiber بسيط
     */
    public function fiber(Request $request): void
    {
        Response::json(FeatureShowcase::fiberExample());
    }

    public function fiberSteps(Request $request): void
    {
        Response::json(FeatureShowcase::fiberStepsExample());
    }

    /**
     * مسار Attribute + Reflection
     */
    public function attribute(Request $request): void
    {
        Response::json(FeatureShowcase::attributeExample());
    }

    /**
    * مسار Opcache/JIT info
    */
    public function opcache(Request $request): void
    {
        Response::json(FeatureShowcase::opcacheInfo());
    }
}
