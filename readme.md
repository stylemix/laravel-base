### Installation

In composer.json:
```
{
  "repositories": [
    {
      "type": "vcs",
      "url": "git@git.stylemix.net:azamatx/laravel-base.git"
    }
  ]
}
```

Require packages:
```bash
composer require stylemix/laravel-base
```

### Form API

##### Creating a simple form

```php
// app/Http/Resources/ContactForm.php
namespace App\Http\Resources;

use Stylemix\Base\FormResource;

class ContactForm extends FormResource
{
	public function fields()
	{
		return [
			// field definitions will be here
		];
	}
}
```

```php
namespace App\Http\Requests;

use App\Http\Resources\ContactForm;
use Stylemix\Base\FormRequest;

class ContactFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

	/**
	 * @param mixed $resource
	 *
	 * @return \Stylemix\Base\FormResource
	 */
	protected function formResource($resource = null)
	{
		return new ContactForm($resource);
	}
}
```

Usage in controller:
```php
class ContactController extends Controller
{
	public function create() 
	{
		return new ContactForm();
	}
	
	public function store(ContactFormRequest $request) 
	{
		/** @var object $values Processed form values */  
		$values = $request->fill();
		
		// whatever you want to do with values. i.e. send mail 
	}
}
```

Routes api.php:
```php
Route::get('contact', 'ContactController@create');
Route::post('contact', 'ContactController@store');
```

##### Field types
```php
return [
	Email::make('to')
		->required()
		->placeholder('Recipient...'),

	Text::make('subject'),
	
	Password::make('password')
		->required(),
]
```

##### Creating an eloquent form

```php
// app/Http/Resources/UserProfileForm.php
namespace App\Http\Resources;

use Stylemix\Base\FormResource;

class UserProfileForm extends FormResource
{
	public function fields()
	{
		return [
			// field definitions will be here
		];
	}
}
```

```php
namespace App\Http\Requests;

use App\Http\Resources\UserProfileForm;
use Stylemix\Base\FormRequest;

class UserProfileRequest extends FormRequest
{

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * @param mixed $resource
	 *
	 * @return \Stylemix\Base\FormResource
	 */
	protected function formResource($resource = null)
	{
		return new UserProfileForm();
	}
}
```

In controller:
```php
class UsersController extends Controller
{
	public function create() 
	{
		return new UserProfileForm(new User());
	}

	public function store(UserProfileRequest $request) 
	{
		$user = $request->fill(new User);
        $user->save();
	
		return new UserResource($user);
	}

    public function edit(User $user)
    {
        return new UserProfileForm($user);
    }

    public function update(UserProfileRequest $request, User $user)
    {
        $request->fill($user)->update();

        return new UserResource($user);
    }
}
```

Routes api.php:
```php
Route::resource('users', 'UsersController');
```
