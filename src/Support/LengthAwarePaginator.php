<?php

namespace QmediaBy\Helpers\Support;

use Illuminate\Pagination\LengthAwarePaginator as BaseLengthAwarePaginator;

class LengthAwarePaginator extends BaseLengthAwarePaginator
{
    /**
     * @param  int  $page
     * @return string
     */
    public function url($page)
    {
        $url = parent::url($page);

        if ($page === 1) {
            $url = preg_replace('/(\?|&)page=1(&|$)/', '$1', $url);
            $url = preg_replace('/\?&/', '?', $url);
            $url = preg_replace('/&&+/', '&', $url);
            $url = rtrim($url, '&');
            $url = rtrim($url, '?');
            return $url;
        }
        return $url;
    }
}
