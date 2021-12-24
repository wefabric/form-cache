# Form Cache

Laravel package to cache form data for a period of time. 


## Installation
This package can be installed through Composer.
```bash
composer require wefabric/form-cache`
```
  
Publish the Configuration and migration files with the following command:

```bash
php artisan vendor:publish --tag="wefabric_form_cache"
```


Migrate the Database:

```bash
php artisan migrate
```

Add the form cache garbage collector command to App\\Console\\Kernel. It will be executed on the default job connection. Add the parameter '--now' to run it immediately.
```php
// Form cache
$schedule->command('form-cache:garbage-collection')->everyFiveMinutes();
```

## Usage
In this example we will use a Livewire component to save form data to the database cache.

In the construct (or for Livewire mount method) we call the 'fillFromFormCache' with the form cache key property.
It automatically fills all the public properties with the saved data.

When saving the form you can call the method 'saveToFormCache' with the form cache key property.
This retrieves all public properties from the class and saves them to the database.

To exclude properties you can add the property name to the protected property 'excludeFromFormCache'.

Example code:

```php

use Livewire\Component;
use Wefabric\FormCache\Concerns\UsesFormCaches;

class MyForm extends Component
{
    use UsesFormCaches;

    protected string $formCacheKey = 'my-form';

    public array $formData = [
        'first_name' => '',
        'last_name' => ''
    ];
    
    /**
     * Exclude properties from the form cache
     * @var array
     */
    protected array $excludeFromFormCache = [

    ];
    
    public function mount()
    {
        $this->formHash = $formHash;

        $this->fillFromFormCache();
    } 
    
    public function saveFormData()
    {
        $this->saveToFormCache();
    }
    
    public function send()
    {
        // Your send logic
        $this->deleteFormData();
    }
}
```

To set the cache expire date, edit the 'expires_after' in the form-cache config.
