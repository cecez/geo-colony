<?php

class Controller_Qgram extends Controller
{

	public function action_landholders()
	{
        Qgram::generate('landholders', '*');
	}

}
