<?php
    namespace System\App\MVC;

    class ViewFactory
    {
        public function __construct()
        {
        }

        public function make($view, $data = [], $mergeData = [])
        {
            $view = str_replace('::','.',$view);

            $data = array_merge($mergeData, $data);

            return new View($view,$data);
        }
    }
?>
