<?php

	class pchartActionComponents extends TBGActionComponent
	{

		public function componentLineGraph()
		{
			$DataSet = new pData;
			foreach ($this->datasets as $ds_id => $dataset)
			{
				$DataSet->AddPoint($dataset['values'], "Serie" . $ds_id);
			}
			$DataSet->AddAllSeries();
			$DataSet->SetAbsciseLabelSerie();
			foreach ($this->datasets as $ds_id => $dataset)
			{
				$DataSet->SetSerieName($dataset['label'], "Serie" . $ds_id);
			}

			// Initialise the graph
			$Test = new pChart($this->width, $this->height);
			//$Test->setFixedScale(-2, 8);
			$Test->setFontProperties(TBGContext::getIncludePath() . 'modules/pchart/fonts/DroidSans.ttf', 8);
			$Test->setGraphArea(50, 30, $this->width - 30, $this->height- 30);
			$Test->drawFilledRoundedRectangle(2, 2, $this->width - 3, $this->height - 3, 5, 240, 240, 240);
			$Test->drawRoundedRectangle(0, 0, $this->width - 1, $this->height - 1, 5, 230, 230, 230);
			$Test->drawGraphArea(255, 255, 255, TRUE);
			$Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2);
			$Test->drawGrid(4, TRUE, 230, 230, 230, 50);

			// Draw the 0 line
			$Test->setFontProperties(TBGContext::getIncludePath() . 'modules/pchart/fonts/DroidSans.ttf', 6);
			$Test->drawTreshold(0, 143, 55, 72, TRUE, TRUE);

			// Draw the cubic curve graph
			if (isset($this->curved) && $this->curved)
			{
				$Test->drawCubicCurve($DataSet->GetData(), $DataSet->GetDataDescription());
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