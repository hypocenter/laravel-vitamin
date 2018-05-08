<?php

namespace Hypocenter\LaravelVitamin\Transformer;


use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;

class PaginatorTransformer extends AbstractTransformer
{
    public function transform()
    {
        $pager = $this->get();

        $data = [];

        if ($pager instanceof LengthAwarePaginator) {
            $data = [
                'data' => ArrayTransformer::create($pager->items())->transform(),
                'meta' => [
                    'current_page'   => $pager->currentPage(),
                    'first_page_url' => $pager->url(1),
                    'from'           => $pager->firstItem(),
                    'last_page'      => $pager->lastPage(),
                    'last_page_url'  => $pager->url($pager->lastPage()),
                    'next_page_url'  => $pager->nextPageUrl(),
                    'per_page'       => $pager->perPage(),
                    'prev_page_url'  => $pager->previousPageUrl(),
                    'to'             => $pager->lastItem(),
                    'total'          => $pager->total(),
                ],
            ];
        } else if ($pager instanceof Paginator) {
            $data = [
                'data' => ArrayTransformer::create($pager->items())->transform(),
                'meta' => [
                    'current_page'   => $pager->currentPage(),
                    'first_page_url' => $pager->url(1),
                    'from'           => $pager->firstItem(),
                    'next_page_url'  => $pager->nextPageUrl(),
                    'per_page'       => $pager->perPage(),
                    'prev_page_url'  => $pager->previousPageUrl(),
                    'to'             => $pager->lastItem(),
                ],
            ];
        }

        return $data;
    }
}