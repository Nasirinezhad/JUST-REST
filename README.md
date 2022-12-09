# JUST-REST
This is a PHP library to building RESTful webservices.

Deploy your controller class
e.g
```
class Test {
    public function index($request)
    {
        return [];
    }
    public function find($request)
    {
        return [];
    }
    public function insert($request)
    {
        return [];
    }
    public function save($request)
    {
        return [];
    }
    public function remove($request)
    {
        return [];
    }
}
```
Bind your controller class to Router
```
    Router::Bind('test', Test::class);
```
Or add Routes one by one
```
    Router::Get('test', [Test::class, 'index']);
    Router::Get('test/{id}', [Test::class, 'find']);
    Router::Post('test', [Test::class, 'insert']);
    Router::Put('test', [Test::class, 'save']);
```


# Usage
you just can clone repo and include src/ files to your project

or use packagist
`composer require nasirinezhad/just-rest`
