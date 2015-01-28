<?php

    namespace thebuggenie\core\modules\pchart;

    defined('PIE_VALUES') || define("PIE_VALUES", 4);
    defined('PIE_PERCENTAGE_AND_VALUES') || define("PIE_PERCENTAGE_AND_VALUES", 5);

    class Components extends \thebuggenie\core\framework\ActionComponent
    {

        public function componentLineGraph()
        {
            $DataSet = new \pData;
            $maxvals = array();
            foreach ($this->datasets as $ds_id => $dataset)
            {
                $DataSet->AddPoint($dataset['values'], "Serie" . $ds_id, array_keys($dataset['values']));
                $maxvals[] = max($dataset['values']);
                if (isset($dataset['burndown']))
                {
                    $burndownValues = array();
                    $amount = (count($dataset['values']) > 1) ? $dataset['burndown']['maxEstimation'] / (count($dataset['values']) - 1) : 0;
                    for ($i = 0; $i < count($dataset['values']); $i++)
                        $burndownValues[] = $dataset['burndown']['maxEstimation'] - ($i * $amount);
                    $DataSet->AddPoint($burndownValues, "Burndown" . $ds_id, $dataset['burndown']['maxEstimation']);
                }
            }
            $DataSet->AddAllSeries();
            if (isset($this->labels))
            {
                $DataSet->AddPoint($this->labels, "Labels");
                $DataSet->SetAbsciseLabelSerie("Labels");
            }
            else
            {
                $DataSet->SetAbsciseLabelSerie();
            }

            foreach ($this->datasets as $ds_id => $dataset)
            {
                $DataSet->SetSerieName($dataset['label'], "Serie" . $ds_id);
                if (isset($dataset['burndown']))
                {
                    $DataSet->SetSerieName($dataset['burndown']['label'], "Burndown" . $ds_id);
                }
            }

            if (isset($this->values_title))
            {
                $DataSet->SetYAxisName($this->values_title);
            }

            if (isset($this->labels_title))
            {
                $DataSet->SetXAxisName($this->labels_title);
            }

            // Initialise the graph
            $Test = new \pChart($this->width, $this->height);
            $Test->setFixedScale(0, (ceil(max($maxvals) / 5) * 5));
            $Test->setFontProperties(THEBUGGENIE_INTERNAL_MODULES_PATH . 'pchart' . DS . 'fonts' . DS . 'OpenSans-Regular.ttf', 8);
            if (isset($this->labels_title))
            {
                $Test->setGraphArea(50, 30, $this->width - 30, $this->height - 45);
            }
            else
            {
                $Test->setGraphArea(50, 30, $this->width - 30, $this->height - 30);
            }
            $Test->drawFilledRoundedRectangle(2, 2, $this->width - 3, $this->height - 3, 5, 240, 240, 240);
            $Test->drawRoundedRectangle(0, 0, $this->width - 1, $this->height - 1, 5, 230, 230, 230);
            $Test->drawGraphArea(255, 255, 255, TRUE);
            $Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2);
            $Test->drawGrid(4, TRUE, 230, 230, 230, 50);

            // Draw the 0 line
            $Test->setFontProperties(THEBUGGENIE_INTERNAL_MODULES_PATH . 'pchart' . DS . 'fonts' . DS . 'OpenSans-Regular.ttf', 6);
            $Test->drawTreshold(0, 143, 55, 72, TRUE, TRUE);

            // Draw the cubic curve graph
            if (isset($this->style) && $this->style == 'curved')
            {
                $Test->drawCubicCurve($DataSet->GetData(), $DataSet->GetDataDescription());
            }
            elseif (isset($this->style) && $this->style == 'filled_line')
            {
                $Test->drawFilledLineGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 50, true);
            }
            elseif (isset($this->style) && $this->style == 'stacked_bar')
            {
                $Test->drawStackedBarGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 50, true);
            }
            elseif (isset($this->style) && $this->style == 'single_bar')
            {
                $Test->drawBarGraph($DataSet->GetData(), $DataSet->GetDataDescription(), TRUE);
            }
            else
            {
                $Test->drawLineGraph($DataSet->GetData(), $DataSet->GetDataDescription());
            }
            if (isset($this->include_plotter) && $this->include_plotter)
            {
                $Test->drawPlotGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 3, 2, 255, 255, 255);
            }

            // Finish the graph
            $Test->setFontProperties(THEBUGGENIE_INTERNAL_MODULES_PATH . 'pchart' . DS . 'fonts' . DS . 'OpenSans-Regular.ttf', 8);
            //$Test->drawLegend(600, 30, $DataSet->GetDataDescription(), 255, 255, 255);
            $Test->drawLegend(55, 35, $DataSet->GetDataDescription(), 255, 255, 255);
            $Test->setFontProperties(THEBUGGENIE_INTERNAL_MODULES_PATH . 'pchart' . DS . 'fonts' . DS . 'OpenSans-Bold.ttf', 10);
            $Test->drawTitle(50, 22, $this->title, 50, 50, 50, $this->width - 30);
            $Test->Stroke(); //("example2.png");
        }

        public function componentPieChart()
        {
            $DataSet = new \pData;

            if (count($this->values) > 0)
            {
                $DataSet->AddPoint($this->values, "Serie1");
                $DataSet->AddPoint($this->labels, "Serie2");
                $DataSet->AddAllSeries();
                $DataSet->SetAbsciseLabelSerie("Serie2");
            }

            // Draw the pie chart
            // Initialise the graph
            $Test = new \pChart($this->width, $this->height);
            $Test->setFontProperties(THEBUGGENIE_INTERNAL_MODULES_PATH . 'pchart' . DS . 'fonts' . DS . 'OpenSans-Regular.ttf', 8);

            $Test->drawFilledRoundedRectangle(2, 2, $this->width - 3, $this->height - 3, 5, 240, 240, 240);
            $Test->drawRoundedRectangle(0, 0, $this->width - 1, $this->height - 1, 5, 230, 230, 230);
            foreach ($this->colors as $id => $color)
            {
                $Test->setColorPalette($id, $color['r'], $color['g'], $color['b']);
            }

            if ($this->height > 200 && $this->width > 250)
            {
                if (count($this->values) > 0)
                {
                    $Test->drawPieLegend($this->width / 3 + $this->width / 3, 40, $DataSet->GetData(), $DataSet->GetDataDescription(), 250, 250, 250);
                }
                $title_font_size = 10;
                $left = $this->width / 3;
                $pie_labels = PIE_PERCENTAGE_AND_VALUES;
            }
            else
            {
                $title_font_size = 7;
                $left = $this->width / 2;
                $pie_labels = PIE_NOLABEL;
            }

            if (isset($this->style) && $this->style == '3d' && count($this->values) > 0)
            {
                $Test->drawPieGraph($DataSet->GetData(), $DataSet->GetDataDescription(), floor($left), floor($this->height / 2), floor(($this->width + $this->height) / 6), $pie_labels, TRUE, 40, 10, 3);
            }

            $Test->setFontProperties(THEBUGGENIE_INTERNAL_MODULES_PATH . 'pchart' . DS . 'fonts' . DS . 'OpenSans-Bold.ttf', $title_font_size);
            $Test->drawTitle(50, 22, $this->title, 50, 50, 50, $this->width - 30);
            $Test->Stroke(); //("example2.png");
        }

    }
