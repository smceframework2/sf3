<?php

namespace SF3\Components;

use SF3\Components\XmlConverter\XmlToArray;
use SimpleXMLElement;
use SF3\Components\XmlConverter\ArrayToXml;

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