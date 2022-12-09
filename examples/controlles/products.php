<?php
    use Nasirinezhad\JustRest\Request;

    class Product
    {
        // example list of products
        private $products = [
            [
                'id' => 1,
                'name' => 'SunGlasses',
                'price' => '128.99$',
                //etc...
            ],
            [
                'id' => 2,
                'name' => 'SomthingChip',
                'price' => '0.50$',
            ],
            [
                'id' => 3,
                'name' => 'OrAny',
                'price' => '123335$',
            ],
        ];
        /**
         * index method, auto bind
         * usually used to return list of records
         * 
         * uri: GET /
         */
        public function index(Request $request)
        {
            // to response result, you can use return
            return $this->products;
        }

        /**
         * find method, auto bind
         * used to find a record by id
         * 
         * uri: GET /{id}
         */
        public function find(Request $request)
        {
            // to get params, you can call em from $request by name(declared in route)
            $id = $request->id -1;
            // or use `get` method and give number of param
            // $id = $request->get(0);

            if (isset($this->products[$id])) {
                return $this->products[$id];
            }else {
                // to response an error, just throw Exception with your message
                throw new \Exception('Product not found!');
            }
        }

        /**
         * insert method, auto bind
         * use to add new record
         * 
         * uri: POST /
         */
        public function insert(Request $request)
        {
            $data = [
                'id' => $this->newID(),
                'name' => $request->name,
                'price' => $request->price
            ];
            // or you can use `all` method to get all post data
            // $request->all();

            // retunr $this->addProduct($data);

            $this->products[] = $data;

            return $this->products;
        }

        /**
         * save method, auto bind
         * use to update an existing record
         * 
         * uri: PUT /
         */
        public function save(Request $request)
        {
            $id = $request->id-1;
            if(!isset($this->products[$id])) {
                throw new \Exception('product not exist');
            }

            $data = [
                'name' => $request->name,
                'price' => $request->price
            ];

            // return $this->update($id, $data);

            $this->products[$id] = $data;

            return $this->products;
        }

        /**
         * remove method, auto bind
         * use to delete an existing record
         * 
         * uri: DELETE /{id}
         */
        public function remove(Request $request)
        {
            $id = $request->id-1;
            if(!isset($this->products[$id])) {
                throw new \Exception('product not exist');
            }
            unset($this->products[$id]);

            return $this->products;
        }

        private function newID()
        {
            return count($this->products)+1;
        }
    }

    