<?php
    namespace System\App\MVC;

    use Windwalker\Renderer\BladeRenderer;

    use mPDF;

    class View
    {
        private $bladeFile;
        private $data;

        public function __construct($bladeFile='',$data=[])
        {
            $this->bladeFile = $bladeFile;
            $this->data = $data;
        }

        public function getBladeFile()
        {
            return $this->bladeFile;
        }

        public function getData()
        {
            return $this->data;
        }

        public function make($bladeFile,$data)
        {
            $this->bladeFile = str_replace('::','.',$bladeFile);
            $this->data = $data;

            return $this;
        }

        private function getRenderer()
        {
            $views_path = new \SplPriorityQueue;
            $views_path->insert(ROOT_PATH.'app/Views',100);
            $cache_path = ROOT_PATH.'app/Cache/';

            return new BladeRenderer($views_path, array('cache_path' => $cache_path));
        }

        public function toPDF()
        {
            $renderer = $this->getRenderer();

            $html = $renderer->render($this->bladeFile, $this->data);

            $pdf = new mPDF();

            $pdf->WriteHTML($html);

            $pdf->Output();
        }

        public function __toString()
        {
            return $this->render();
        }

        public function render()
        {
            $renderer = $this->getRenderer();

            return $renderer->render($this->bladeFile, $this->data);
        }
    }
?>
