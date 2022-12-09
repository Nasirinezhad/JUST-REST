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
    Router::Bind('product', Product::class);
```
Or add Routes one by one
```
    Router::Put('user', [User::class, 'update']);
```


# Usage
you just can clone repo and include src/ files to your project

or use packagist
`composer require nasirinezhad/just-rest`
