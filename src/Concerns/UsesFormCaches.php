<?php


namespace Wefabric\FormCache\Concerns;


use Wefabric\FormCache\FormCache;

trait UsesFormCaches
{
    protected string $formCacheKey;

    /**
     * @param string $formCacheKey
     * @return bool
     */
    private function saveToFormCache(string $formCacheKey = ''): bool
    {
        $formCache = $this->getFormCache($formCacheKey);
        $formCache = $formCache->get();
        $formData  = $this->getFormData();

        foreach ($this->protectedFormData as $key) {
            unset($formData[$key]);
        }

        $formCache->form_data = $formData;
        return $formCache->save();
    }

    /**
     * @return array
     */
    public function getFormData(): array
    {
        $reflectionClass = new \ReflectionClass($this);
        $data = [];
        foreach ($reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if(!in_array($property->name, $this->excludeFromFormCache)) {
                $data[$property->name] = $this->{$property->name};
            }

        }
        return $data;
    }

    /**
     * @param string $formCacheKey
     */
    private function fillFromFormCache(string $formCacheKey = ''): void
    {
        $formCache = $this->getFormCache($formCacheKey);
        $formCache = $formCache->get();
        if($formCache->form_data) {
            foreach ($formCache->form_data as $key => $value) {
                if($key === 'terms') {
                    $value = false;
                }
                if(!in_array($key, $this->excludeFromFormCache)) {
                    $this->$key = $value;
                }
            }
        }
    }

    /**
     * @param string $formCacheKey
     * @return bool
     */
    public function deleteFormData(string $formCacheKey = ''): bool
    {
        $formCache = $this->getFormCache($formCacheKey);
        return (bool)$formCache->delete();
    }

    public function getFormCacheKey(): string
    {
        if(!$this->formCacheKey) {
            throw new \Exception('Did you set the "formCacheKey" in your form?');
        }
        return $this->formCacheKey;
    }

    public function getFormCache(string $formCacheKey = ''): FormCache
    {
        if(!$formCacheKey) {
            $formCacheKey = $this->getFormCacheKey();
        }

        return new FormCache($formCacheKey);
    }
}
