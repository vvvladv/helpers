# qmedia-by/helpers

Хелперы для проектов на Evolution CMS 3.

Пакет рассчитан на установку через Composer в `core/` проекта.

## Требования

- PHP 8.2+
- Evolution CMS 3 (Illuminate 8)
- Для `ThumbHelper` / `GlideHelper`: расширение `imagick` или `gd`
- Для `ListerHelper`: сниппет DocLister
- Для `BuilderHelper`: сниппет PageBuilder
- Для `ThumbHelper` с `thumbs.engine = phpthumb`: сниппет phpthumb

## Установка

```bash
composer require qmedia-by/helpers
```

Composer сам поставит зависимости пакета (`league/glide` и связанные библиотеки). Дефолтный конфиг из пакета мержится автоматически.

Публиковать конфиг нужно только если хотите переопределить значения в проекте:

```bash
php artisan vendor:publish --provider="QmediaBy\\Helpers\\HelpersServiceProvider" --tag=helpers-config
```

## Использование

```php
use QmediaBy\Helpers\ThumbHelper;
use QmediaBy\Helpers\ListerHelper;
use QmediaBy\Helpers\FunctionsHelper;
use QmediaBy\Helpers\MultiFieldsHelper;
use QmediaBy\Helpers\CacheHelper;
use QmediaBy\Helpers\UTMHelper;
use QmediaBy\Helpers\BuilderHelper;

ThumbHelper::make('assets/images/photo.jpg', 'w=400,h=300,zc=1,f=webp,q=80');

$items = ListerHelper::run([
    'parents' => 10,
    'display' => 12,
])->toArray();

FunctionsHelper::url(12, true);
MultiFieldsHelper::toCollection($tvValue);
CacheHelper::getWithCallback(CacheHelper::createKey('block', $id), fn () => $payload);
UTMHelper::set();
```

Кастомная обработка блоков PageBuilder:

```php
use QmediaBy\Helpers\BuilderHelper;
use QmediaBy\Helpers\Contracts\ItemTransformerInterface;

$blocks = BuilderHelper::run(['docid' => 12])
    ->withTransformer('hero', new class implements ItemTransformerInterface {
        public function transform(array &$item): void
        {
            $item['title'] = trim((string) ($item['title'] ?? ''));
        }
    })
    ->toArray();
```

## Конфиг

После публикации файл `helpers.php` можно править в конфиге проекта.

| Ключ | Назначение | По умолчанию |
| --- | --- | --- |
| `noimage` | Заглушка, если исходного файла нет | `theme/images/noimage.png` |
| `thumbs.engine` | `glide` или `phpthumb` | `glide` |
| `glide.driver` | `imagick`, `gd` или `null` (авто) | `null` |
| `glide.cache` | Каталог кэша превью относительно public | `assets/cache/thumbs` |
| `cache.client_key` | Суффикс ключей кэша | `evo()->config['client_cache_key']` |

## Классы

- `ThumbHelper` — превью через Glide или phpthumb
- `GlideHelper` / `GlideAdapter` — генерация изображений и маппинг параметров phpthumb
- `ListerHelper` — DocLister в API-режиме, опционально с пагинацией Laravel
- `BuilderHelper` — PageBuilder в массив + трансформеры блоков
- `MultiFieldsHelper` — разбор MultiTV/MultiFields JSON
- `FunctionsHelper` — URL документа и русские названия месяцев
- `CacheHelper` — ключи и чтение/запись с учётом `enable_cache`
- `UTMHelper` — сохранение UTM-меток в сессию
