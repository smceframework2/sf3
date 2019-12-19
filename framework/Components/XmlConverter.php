<?php

namespace EF2\Components;

use EF2\Components\XmlConverter\XmlToArray;
use SimpleXMLElement;
use EF2\Components\XmlConverter\ArrayToXml;

class XmlConverter
{
	public function arrayToXml($arr)
	{

		$arraytoxml=new ArrayToXml($arr);
		return $arraytoxml->toXml();

	}


	public function xmlToArray($xml)
	{
		$xmlToArray=new XmlToArray($xml);
		return $xmlToArray->data;
	}



}