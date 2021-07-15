<?php


namespace Wefabric\FormCache\Actions;


use Carbon\Carbon;
use Wefabric\FormCache\Models\FormCache;

class RemoveOldFormCacheAction
{

    /**
     * @return bool
     */
    public function execute(): bool
    {
        $expiresAfter = (new Carbon())->modify('-'.config('form-cache.expires_after'));
        return FormCache::query()->where('updated_at', '<=', $expiresAfter)->delete();
    }
}
