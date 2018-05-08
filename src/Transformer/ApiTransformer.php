<?php

namespace Hypocenter\LaravelVitamin\Transformer;


class ApiTransformer extends AbstractTransformer
{
    public function transform()
    {
        return [
            'code'    => (int)$this->get('code') ?: 0,
            'msg'     => (string)$this->get('msg') ?: '',
            'payload' => $this->get('payload'),
        ];
    }
}