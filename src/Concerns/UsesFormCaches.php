<?php


namespace Wefabric\FormCache\Concerns;


use Wefabric\FormCache\FormCache;

trait UsesFormCaches
{

    /**
     * Gets removed before saving
     * @var string[]
     */
    protected array $protectedFormData = [
        'password',
        'password_confirmation',
        'passwordConfirmation'
    ];

    /**
     * @param string $form
     * @return bool
     * @throws \Exception
     */
    private function saveToFormCache(string $form): bool
    {
        $formCache = new FormCache($form);
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
     * @param string $form
     * @throws \Exception
     */
    private function fillFromFormCache(string $form)
    {
        $formCache = new FormCache($form);
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
     * @param string $form
     * @return bool|null
     * @throws \Exception
     */
    public function deleteFormData(string $form)
    {
        $formCache = new FormCache($form);
        return $formCache->delete();
    }
}
