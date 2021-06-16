<?php


    namespace App\classes;


    use JetBrains\PhpStorm\Pure;

    class MyErrors implements \Countable
    {
        protected array $errors = [];
        protected string $string = '';

        public function count() : int
        {
            return count($this->errors);
        }

        public function __toString() : string
        {
            if ($this->count() !== 0) {
                foreach ($this->errors as $error) {
                    $this->string .= $error . ' <br> ';
                }
            }
            return $this->string;
        }

        public function add(string $add) : void {
            $this->errors[] = $add;
        }

        public function reset() : void {
            $this->errors = [];
        }

        #[Pure] public function getFirst() : string {
            if ($this->count() !== 0) {
                return $this->errors[0];
            } else {
                return '418';
            }
        }


        /**
         * Replace respective words in array with string $errors if those strings have keywords
         * @param array $search - array with searched keywords
         * @param array $substitutions - array with words for replacement
         */
//        public function replace(array $search, array $substitutions) {
//            if ($this()) {
//                foreach ($this->errors as $index => $item) {
//
//                }
//            }
//        }

        /**
         * Return true if array $errors content some messages
         * @return bool
         */
        #[Pure] public function __invoke() : bool
        {
            return $this->count() !== 0;
        }

    }

//    TODO перенести часть фукционала из Govno и Uploader в Errors
