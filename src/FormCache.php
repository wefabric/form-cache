<?php


namespace Wefabric\FormCache;


use Carbon\Carbon;
use Illuminate\Support\Facades\Cookie;
use Wefabric\FormCache\Models\FormCache as FormCacheModel;

class FormCache
{
    const CACHE_PREFIX = 'form_cache';

    protected string $type = '';

    /**
     * FormCache constructor.
     * @param string $type
     */
    public function __construct(
        string $type
    ){
        $this->type = $type;
    }

    /**
     *
     */
    public function init(): void
    {
        $this->get();
    }

    /**
     * @return FormCacheModel
     */
    public function get(): FormCacheModel
    {
        if(!$id = $this->getIdFromCache()) {
            $this->createFormCache();
        }

        if(!$formCache = FormCacheModel::query()->where('id', $id)->first()) {
            $formCache = $this->createFormCache();
            $id = $formCache->id;
        }

        return FormCacheModel::query()->where('id', $id)->first();
    }

    /**
     * @return FormCacheModel
     */
    private function createFormCache(): FormCacheModel
    {
        $formCache = new FormCacheModel();
        $formCache->type = $this->type;
        $formCache->form_data = [];
        if($ipAddress = request()->ip()) {
            $formCache->ip_address = $ipAddress;
        }
        $formCache->save();
        $id = $formCache->id;
        $this->saveIdInCache($id);
        return $formCache;
    }

    /**
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        if($formCacheModel = $this->get()) {
            $formCacheModel->delete();
            $this->deleteIdFromCache();
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return self::CACHE_PREFIX.'_'.$this->type;
    }

    /**
     * @return string|null
     */
    public function getIdFromCache(): ?string
    {
        switch (self::getCacheMethod()) {
            case 'cookies':
                return Cookie::get($this->getKey());
            default:
                return session()->get($this->getKey());
        }

    }

    /**
     * @param string $id
     */
    public function saveIdInCache(string $id): void
    {
        switch (self::getCacheMethod()) {
            case 'cookies':
                $now = new Carbon();
                $difference = $now->diff($now->modify(config('form-cache.expires_after')));
                Cookie::queue($this->getKey(), $id, $difference->i);
                break;
            default:
                session()->put($this->getKey(), $id);
        }

    }


    public function deleteIdFromCache(): void
    {
        switch (self::getCacheMethod()) {
            case 'cookies':
                Cookie::queue(Cookie::forget($this->getKey()));
                break;
            default:
                session()->remove($this->getKey());
        }

    }

    /**
     * @return string
     */
    public static function getCacheMethod(): string
    {
        return config('form-cache.cache_method', 'session');
    }

}
