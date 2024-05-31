# DTO Mapper
A DTO Mapper library and Symfony bundle

# Installation
## Requirements
- **PHP**: >=8.2
- **doctrine/collections**: ^2.0|^3.0 (Optional)
- **doctrine/orm**: ^2.0|^3.0 (Optional)

## Using Composer 
```bash
composer require controlbit/dto
```

## For Symfony users
Add to your `bundles.php` line:

```php
ControlBit\Dto\Bridge\Symfony\DtoBundle::class                               => ['all' => true],
```

# Usage
## Getting Mapper instance
### As independent library
```php
$mapper = ControlBit\Dto\Factory::create();
$mapper->map($source, $destination);
```

### As Symfony Bundle
You can use Mapper service directly by service ID `dto_bundle.mapper` or inject autowired using
`ControlBit\Dto\Contract\Mapper\MapperInterface`

```php
use ControlBit\Dto\Contract\Mapper\MapperInterface;

class YourService {
    public function __construct(private readonly MapperInterface $mapper) {      
    }
    
    private function foo() {
        $this->mapper->map($yourSource, YourDto::class)
    }
}
```

Argument resolver will resolve your `POST`, `GET` parameters to DTO, as well as `FILES` and `route params`

## Usage of Mapper

Mapper supports next cases of mapping:
- Mapping from `array` to `object` created by providing `classname`.
- Mapping from `array` to already instantiated `object`.
- Mapping from `Request`  to `object` created by providing `classname`.
- Mapping from `DTO Object` to `object` created by providing `classname`.
- Mapping from `DTO Object` to already instantiated `object`.
- Mapping from `Object` to `DTO object` created by providing DTO `classname`.
- Mapping from `Object` to already instantiated `DTO object` (Could be done, but not good practice)

### From array to DTO Object
It's basically a DeNormalizer
if you use it as Array to DTO object, it can go nested.
```php
$source = [
    'bar' => 1,
    'baz' => 2,
    'nested' => [
        'bar' => 3,
        'baz' => 4,
    ],
    'arrayOfFoo' => [
        [ 'bar' => 5,'baz' => 6],
        [ 'bar' => 7,'baz' => 8],
    ],
];

class Dto {
    private int $bar;
    private int $baz;
    private ?Dto $nested;
    
    #[ControlBit\Dto\Attribute\Dto(Dto::class)] /* In this case, we know it's */
    private array $arrayOfFoo;
}

$newObject = $mapper->map($source, Foo::class);
```

### From DTO to Some other object
```php

class Dto {
    private int $bar;
    private int $baz;
    private Dto $nested;
    
    #[ControlBit\Dto\Attribute\Dto(Dto::class)]
    /** @var Dto (This line is not required.) */
    private array $arrayOfFoo;
}

$source = new Dto(/* This is where you populate tour DTO */);

class Foo {
    private int $bar;
    private int $baz;
    private ?Foo $nested;
    
    private array $arrayOfFoo;
}

$newObject = $mapper->map($source, Foo::class);
```

You can achieve this without passing destination, by putting `#[Dto(Foo::class)]` on top of your Dto class,
and calling just:
```php
    $newObject = $mapper->map($source);
```
This is useful if you want to constrain your DTO to be mapped only to certain type of object.

### From Object to DTO
Similar to previous one, but reversed:
```php

class Foo {
    private int $bar;
    private int $baz;
    private ?Foo $nested;
    private array $arrayOfFoo;
}

$source = new Foo(/* let's assume you populated with data you want */);

class Dto {
    private int $bar;
    private int $baz;
    private Dto $nested;
    
    #[ControlBit\Dto\Attribute\Dto(Dto::class)]
    /** @var Dto (This line is not required.) */
    private array $arrayOfFoo;
}

$newObject = $mapper->map($source, Dto::class);
```

## Request to DTO

### for those who use Symfony Request component ONLY (for example. Laravel)
```php
use Symfony\Component\HttpFoundation\Request;

/**
* @var Request $request
 */
$request = new Request(/* Your existing request */);
$mappedObject = $this->mapper->map($request, YourDto::class);
```

### Symfony Users
If you want to use in controller, it's advisable to use Argument Resolver that bundle provides by adding attribute to
argument like this:

```php
use ControlBit\Dto\Attribute\Dto;

final class YourController {

    private function fooAction(#[Dto] YourDtoClass $dto) {
        /* Your request data will be mapped to Dto object*/
    }
}
```

⚠️ IMPORTANT!
 - DTO Argument resolver validate your DTO if `Symfony Validator` is installed and throw `ControlBit\Dto\Exception\ValidationException`.
 - Mentioned exception will be automatically rendered as JSON response with error message unless `validation_json_bad_request` is set to false (default is true)

## Useful, special cases handling
### Using custom Setter when mapping on destination object
Let's say you want to map an DTO object to another object, 
and you don't want to map to property directly, but use 
setter on destination object. You can achieve this 
by adding `ControlBit\Dto\Attribute\Setter` to property on DTO object.

```php

use ControlBit\Dto\Attribute\Setter;

class OrderDto {
    #[Setter('setPriceWithVAT')]
    private float $price;
}

class Order {
    private float $price;
    private float $priceIncludingVat;
    private float $vatOnly;
    
    /* This method will be called by Mapper to set DTO value to */
    public function setPriceIncludingVAT(int $price) {
        $this->prioce            = $price;    
        $this->priceIncludingVat = $price * 1.2;
        $this->vatOnly           = $price * 0.2;    
        /* This is just an example, in real life, be careful with floats :) */
    }
}
```

### Using different destination property name
In some very rare cases, we don't want to map directly to value of same name,
and we don't want to use custom made setter on destination just for mapping to property of different name.
In that case we can use `ControlBit\Dto\Attribute\MapTo` attribute on source property.

```php

use ControlBit\Dto\Attribute\MapTo;

class OrderDto {
    #[MapTo('priceWithoutVat')]
    private float $price;
}

class Order {
    /* `Price` prop on DTO will be mapped to shi property as specified by #[MapTo]. */
    private float $priceWithoutVat;
}
```

### Ignoring properties
Sometimes we want to ignore property for mapping from DTO to object, 
but we still need it in DTO for some other reason (Like for other application layer usage).

For example, we have default one, or we want to assign it later.
In that case we can use `ControlBit\Dto\Attribute\Ignore` attribute on source property.

```php

use ControlBit\Dto\Attribute\Ignore;

class UserDto {
    private string $username; 

    #[Ignore]
    private int $secretCode;    
}

class User {
    private string $username;
    
    /* THIS WILL NOT BE MAPPED! */
    private int $secretCode;   
}
```

⚠️ This would violate a principle, that DTO should be always in valid state, so use with caution.

### Transforming value
In some cases, we want to transform values right before mapping from DTO to other object.

In that case we can use `ControlBit\Dto\Attribute\Transformer` attribute on source property.
Attribute requires a constructor argument that must be 
a class (FQCN) that implements `ControlBit\Dto\Contract\TransformerInterface`

For example, transformer could look like this:

```php

use ControlBit\Dto\Contract\Transformer\TransformerInterface;

final class PriceWithVatTransformer implements TransformerInterface
{
    /**
     * @param  float  $value
     */
    public static function transform(mixed $value): mixed
    {
        return $value * 1.2;
    }
}

```

```php
use ControlBit\Dto\Attribute\Transformer;

class OrderDto {
    #[Transformer(PriceWithVatTransformer::class)]
    private float $price;
}

class Order {
    /* `Price` will have value that is previously transformed, in this case with VAT included. */
    private float $price;
}
```

### Doctrine ArrayCollection
For Symfony and generally Doctrine users, there is a pre-compiled transformer that you can use
when you want to map an `array` into doctrine's `ArrayCollection`.

In that case we can use `ControlBit\Dto\Attribute\Transformers\Collection` attribute on source property.

```php
use ControlBit\Dto\Attribute\Transformers\Collection;

class FooDto {
    #[Collection]
    private array $bars;
}

class FooEntity {
    /* Usually it's OneToMany, ManyToMany relation that is assigned to property as ArrayCollection type */
    private ArrayCollection $bars;
}
```

# Symfony configuration reference
You don't need it unless you want to override something.
```yaml
dto_bundle:
  # Throws JSON error message When DTO is invalid
  validation_json_bad_request: true
  map_private_properties: true # Should map private properties
  # Currently ony one case transformer is available, but you can easily override with your own
  # This is purely by assumption on best practice, 
  # that you are using REST API Snake case, and PascalCase in your DTO object props.  
  case_transformer: ControlBit\Dto\Adapter\CaseTransformer\SnakeCaseToCamelcaseTransformer
```

# TODO (Upcoming):
- Mapping UploadedFile into \SplFileInfo (could be that half of it is working already)
- DTO To Object Using Constructor via `#[Constructor]` Attribute on DTO.
- Caching Mapping Metadata
