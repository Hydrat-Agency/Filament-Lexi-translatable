# Lexi Translate Examples

### First Example 

the following controller handle update or create translations from request

```php 
namespace App\Http\Controllers;

use App\Models\Service;

class SetTranslationsController extends Controller
{
    public function handle()
    {
        $service = Service::create([
            'name' => 'original name',
            'description' => 'original description',
        ]);

        $service->setTranslations([
            'en' => [
                'name' => 'English Name'
                'description' => 'English description'
            ],
            'ar' => [
                'name' => 'اسم باللغة العربية'
                'description' => 'وصف باللغة العربية'
            ],
        ]);

        return view('set_translations', compact('service'));
    }
}

```

---

### second example from blade file to controller

#### blade file

```php
<form action="{{ route('translations.store', $post->id) }}" method="POST">
    @csrf

    @foreach (lexi_locales() as $locale)
        <h4>{{ strtoupper($locale) }}</h4>

        @foreach ($post->getTranslatableFields() as $field)
            <div>
                <label for="{{ $field }}_{{ $locale }}">{{ ucfirst($field) }} ({{ $locale }}) </label>
                <input type="text" name="translations[{{ $locale }}][{{ $field }}]" 
                       value="{{ $post->transAttr($field, $locale) }}" />
            </div>
        @endforeach
    @endforeach

    <button type="submit">Save Translations</button>
</form>
```

### in controller
```php
use Illuminate\Http\Request;
use App\Models\Service;

class TranslationsController extends Controller
{
    public function store(Request $request, $id)
    {
        $translations = $request->input('translations');
        $service = Service::findOrFail($id);
        $service->setTranslations($translations);

        return redirect()->back()->with('success', 'Translations updated successfully!');
    }
}

```

**Validations will coming Soon** .

