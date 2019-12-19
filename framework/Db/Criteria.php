<?php

namespace EF2\Db;

class Criteria
{
	public  $alias;
	public  $select;
	public  $orderBy;
	public  $limit;
	public  $offset;
	public  $groupBy;
	public  $having;
	private $conditions = [];
	private $joins      = [];

	public function condition($condition, ...$params)
	{
		if (!$params && is_array($condition)) {
			$params       = [];
			$newCondition = '';
			foreach ($condition as $key => $param) {
				$newCondition       .= $key . '=:' . $key . ' && ';
				$params[':' . $key] = $param;
			}
			$newCondition = rtrim($newCondition, ' && ');
			$params=[$params];
		}
		$this->conditions[] = [
			"condition" => isset($newCondition) ? $newCondition : $condition,
			"params"    => $params
		];
//		pre($this->conditions);
	}

	public function getConditions()
	{
		return $this->conditions;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function leftJoin(...$params)
	{
		$this->joins["left"][] = $params;
	}

	public function innerJoin(...$params)
	{
		$this->joins["inner"][] = $params;
	}

	public function getJoins()
	{
		return $this->joins;
	}
}