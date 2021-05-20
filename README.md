# laminas-handlers
JsonResponse implementation of laminas/diactoros with a structured data and Data Transfer Object class.

### Built With

* [PHP](https://www.php.net)
* [Diactoros](https://docs.laminas.dev/laminas-diactoros)
* [http-status](https://github.com/lukasoppermann/http-status)
* [http-message-util](https://github.com/php-fig/http-message-util)


### Usage

You can use class JsonResponse for produce ResponseInterface

```php
use StructuredHandlers\JsonResponse;
```

then you can use like this, produces a response 200 by default

```php
return new JsonResponse(
        $responseData,
        'This is a cool message'
);
```

Or in a more specific case something like this

```php
return new JsonResponse(
        $responseData, // Data to client
        'This is a bad message', // Personalized message
        500, // Http status code
        true, // is error
        $arrayHeaders // Array of headers
);
```


You can use class DataTransferObject for extends in your DTO

```php
class MyDtoRequest extends DataTransferObject {
    public $className;
    public $version;
    public $property;
}
```

To construct an object of the class, an array can be used thanks to the use of reflection in DataTransferObject class

```php

$myDtoArray = [
    'className' => 'Example',
    'version' => 1.0,
    'property' => 'hi!' 
];

$myDto = new MyDtoRequest($myDtoArray);
```

You can build an array based on a single value or also excluding some value from the entire class

```php
$myDto->only('className')->toArray();
```

Only get class_name, the names are obtained in snake case if the false parameter is not specified to the toArray function

```php
[
    'class_name' => 'Example'
]
```

You can exclude a property from the class using the except function

```php
$myDto->except('className')->toArray();
```

produces

```php
[
    'class_name' => 'Example',
    'property' => 'hi!'
]
```
