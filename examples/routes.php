<?php

    use Nasirinezhad\JustRest\Router;
    use Nasirinezhad\JustRest\RException;
    /**
     * Bind full class to router
     * Router will automaticaly refer requests ro controller methods
     */
    Router::Bind('product', Product::class);

    /**
     * Add routes one by one with closure
     * 
     */
    Router::Get('test/{id}', function ($r)
    {
        return [
            'message' => 'you\'re requested test/id',
            'id'=> $r->id
        ];
    });
    
    /**
     * Add routes one by one with Class:Method
     * 
     */
    Router::Get('user', 'User:me');
    Router::Put('user', [User::class, 'update']);
    Router::Post('user', ['User', 'newUser']);

    /**
     * add some method to midleware
     * 
     */
    Router::middleware('Midclass:method')->get('users', 'User:me');
    Router::middleware([Midclass::class,'method'])->post('users', 'User:me');
    Router::middleware(function ($request)
    {
        if (!$request->header('Authorization')) {
            throw new RException('Error! Unauthorized!', 401);
            // or just return false;
        }
        return true;
    })->get('me', 'User:me');
    