<?php
    use Nasirinezhad\JustRest\Request;

    class User
    {
        
        public function me(Request $request)
        {
            return ['profile'=>[
                'FirstName' => 'Mahdi',
                'LastName' => 'Nasirinezhad'
            ]];
        }
    }
    