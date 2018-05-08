<?php

namespace Hypocenter\LaravelVitamin\Transformer;


class CompositeTransformer extends AbstractTransformer
{
    private $transforms = [];

    public static function create($data, $transformers = [])
    {
        $tr = parent::create($data);
        $tr->setTransforms($transformers);
        return $tr;
    }

    public function setTransforms($transforms)
    {
        $this->transforms = $transforms;
    }

    public function transform()
    {
        $data = $this->get();

        foreach ($this->transforms as $k => $tr) {
            if (is_numeric($k)) {
                $data = $this->handle($data, $tr);
            } else if (is_string($k)) {
                $data[$k] = $this->handle($data[$k], $tr);
            }
        }

        return $data;
    }

    private function handle(&$data, $tr)
    {
        if (is_callable($tr)) {
            return $tr($data);
        } else if (is_string($tr)) {
            return $tr::create($data)->transform();
        }

        throw new \InvalidArgumentException('Invalid Transformer: ' . $tr);
    }
}