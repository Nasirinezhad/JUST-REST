<?php

use Nasirinezhad\JustRest\Request;

    class middleware
    {
        public function method(Request $request)
        {
            if (!$request->header('Authorization')) {
                throw new Exception('Error! Middleware not acceptes!');
                // or just return false;
            }
            return true;
        }
    }
    