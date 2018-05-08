<?php

namespace Hypocenter\LaravelVitamin\Http;


use Hypocenter\LaravelVitamin\Error\Error;
use Hypocenter\LaravelVitamin\Transformer\ApiTransformer;
use Hypocenter\LaravelVitamin\Transformer\Contracts\Transformable;
use Hypocenter\LaravelVitamin\Transformer\Contracts\Transformer;
use Illuminate\Support\MessageBag;

class ApiResponseFactory
{
    /**
     * 返回JSON成功状态
     *
     * @param null|Transformer|Transformable $payload
     * @param string                         $msg
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function success($payload = null, $msg = null)
    {
        $code = 0;

        assert(is_null(
                $payload) ||
            $payload instanceof Transformer ||
            $payload instanceof Transformable
        );

        $result = ApiTransformer::create(compact('code', 'msg', 'payload'));

        return response()->json($result);
    }

    /**
     * 返回JSON失败状态
     *
     * @param int|Error|string               $code
     * @param string|MessageBag              $msg
     * @param null|Transformer|Transformable $payload
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function error($code, $msg = null, $payload = null)
    {
        assert(is_null($payload) || ($payload instanceof Transformer || $payload instanceof Transformable));

        if ($code instanceof Error) {
            $msg  = $msg ?: $code->msg();
            $code = $code->status();
        } else if (is_string($code) && is_null($msg)) {
            $msg  = $code;
            $code = 1;
        }

        return response()->json(ApiTransformer::create(compact('code', 'msg', 'payload')));
    }
}