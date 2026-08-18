<?php

namespace QmediaBy\Helpers\Support;

use Illuminate\Support\Collection;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

trait PaginationTrait
{
    /**
     * @param  Collection  $collection
     * @param  int  $limit
     * @param  int  $page
     * @param  string  $key
     * @return Collection
     */
    public function slice(Collection $collection, int $limit, int $page = 0, string $key = "page"): Collection
    {
        if (empty($page)) {
            try {
                $page = request()->get($key, 1);
            } catch (NotFoundExceptionInterface|ContainerExceptionInterface $e) {
                $page = 1;
            }
        }

        return $collection->slice(($page - 1) * $limit, $limit)->values();
    }

    /**
     * @param  Collection  $collection
     * @param  int  $limit
     * @param  int  $total
     * @return LengthAwarePaginator
     */
    protected function paginator(Collection $collection, int $limit, int $total): LengthAwarePaginator
    {
        $limit = $limit ?? 10;
        $documentId = (int) $_GET['document']['id'] ?? evo()->documentIdentifier;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $path = !empty($documentId) ? \UrlProcessor::makeUrl($documentId) : LengthAwarePaginator::resolveCurrentPath();

        parse_str(request()->getQueryString(), $query);

        if (!empty($query)) {
            foreach ($query as $query_item_key => $query_item) {
                if (str_contains($query_item_key, "amp;")) {
                    $query_item_new_key = str_replace("amp;", "", $query_item_key);
                    $query[$query_item_new_key] = $query_item;
                    unset($query[$query_item_key]);
                }
            }
        }

        unset($query["q"], $query["page"], $query["document"], $query["site"]);

        return new LengthAwarePaginator(
            $collection->values(),
            $total,
            $limit,
            $currentPage,
            [
                'pageName' => "page",
                'path' => $path,
                'query' => $query,
                'fragment' => null
            ]
        );
    }

    /**
     * @return int
     */
    protected function getCurrentPage(): int
    {
        return LengthAwarePaginator::resolveCurrentPage();
    }
}
