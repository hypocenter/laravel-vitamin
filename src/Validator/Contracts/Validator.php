<?php

namespace Hypocenter\LaravelVitamin\Validator\Contracts;


use Illuminate\Validation\ValidationException;

interface Validator
{
    const SCENE_SAVE   = 'save'; // 模型保存的时候*实际字段*必须要达到的验证标准，包括外健
    const SCENE_CREATE = 'create';
    const SCENE_UPDATE = 'update';

    public function rules($scene = null): array;

    public function messages($scene = null): array;

    /**
     * @param      $modelOrAttributes
     * @param null $scene
     *
     * @return array
     * @throws ValidationException
     */
    public function validate($modelOrAttributes, $scene = null);
}