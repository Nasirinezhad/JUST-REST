<?php

    use Nasirinezhad\JustRest\Router;
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
     * TODO: midleware
     */
    /*
    Router::Midleware('midclass:method')->get('user', 'User:me');
    Router::Midleware([midclass::class,'method'])->get('user', 'User:me');
    Router::Midleware(['midclass','method'])->get('user', 'User:me');
    */