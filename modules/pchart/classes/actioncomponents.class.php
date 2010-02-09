<?php

	class pchartActionComponents extends TBGActionComponent
	{

		public function componentLineGraph()
		{
			$DataSet = new pData;
			$maxvals = array();
			foreach ($this->datasets as $ds_id => $dataset)
			{
				//var_dump(array_keys($dataset['values']));die();
				$DataSet->AddPoint($dataset['values'], "Serie" . $ds_id, array_keys($dataset['values']));
				$maxvals[] = max($dataset['values']);
			}
			$DataSet->AddAllSeries();
			if (isset($this->labels))
			{
				$DataSet->AddPoint($this->labels,"Labels");
				$DataSet->SetAbsciseLabelSerie("Labels");
			}
			else
			{
				$DataSet->SetAbsciseLabelSerie();
			}
			foreach ($this->datasets as $ds_id => $dataset)
			{
				$DataSet->SetSerieName($dataset['label'], "Serie" . $ds_id);
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
			$Test = new pChart($this->width, $this->height);
			$Test->setFixedScale(0, max($maxvals));
			//$Test->setFixedScale(-2, 8);
			$Test->setFontProperties(TBGContext::getIncludePath() . 'modules/pchart/fonts/DroidSans.ttf', 8);
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
			$Test->setFontProperties(TBGContext::getIncludePath() . 'modules/pchart/fonts/DroidSans.ttf', 6);
			$Test->drawTreshold(0, 143, 55, 72, TRUE, TRUE);

			// Draw the cubic curve graph
			if (isset($this->style) && $this->style == 'curved')
			{
				$Test->drawCubicCurve($DataSet->GetData(), $DataSet->GetDataDescription());
			}
			if (isset($this->style) && $this->style == 'filled_line')
			{
				$Test->drawFilledLineGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 50, true);
			}
			if (isset($this->style) && $this->style == 'stacked_bar')
			{
				$Test->drawStackedBarGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 50, true);
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
			$Test->setFontProperties(TBGContext::getIncludePath() . 'modules/pchart/fonts/DroidSans.ttf', 8);
			//$Test->drawLegend(600, 30, $DataSet->GetDataDescription(), 255, 255, 255);
			$Test->drawLegend(55, 35, $DataSet->GetDataDescription(), 255, 255, 255);
			$Test->setFontProperties(TBGContext::getIncludePath() . 'modules/pchart/fonts/DroidSans.ttf', 10);
			$Test->drawTitle(50, 22, $this->title, 50, 50, 50, $this->width - 30);
			$Test->Stroke();//("example2.png");
		}

	}