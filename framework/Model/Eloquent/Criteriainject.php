<?php

namespace EF2\Model\Eloquent;

use Illuminate\Database\Capsule\Manager as DB;
use EF2\Db\Criteria;

class Criteriainject
{
	private $table_name;

	public function __construct($table_name)
	{
		$this->table_name = $table_name;
	}

	public function find(Criteria $criteria = null)
	{
		$db = $this->getFrom($criteria);
		$db = $this->build($db, $criteria);

		return $db->first();
	}

	public function findAll(Criteria $criteria = null)
	{
		$db = $this->getFrom($criteria);
		$db = $this->build($db, $criteria);

		return $db->get();
	}

	public function count(Criteria $criteria = null)
	{
		$db = $this->getFrom($criteria);
		$db = $this->build($db, $criteria);

		return $db->count();
	}

	public function delete(Criteria $criteria = null)
	{
		$db = $this->getFrom($criteria);
		$db = $this->build($db, $criteria);

		return $db->delete();
	}

	public function update(Criteria $criteria = null, $params = [])
	{
		$params = array_merge(["updated_at" => date("Y-m-d H:i:s")],$params);
		$db     = $this->getFrom($criteria);
		$db     = $this->build($db, $criteria);

		return $db->update($params);
	}

	private function getFrom($criteria)
	{
		if (isset($criteria->alias)) {
			return DB::table($this->table_name . " as " . $criteria->alias);
		}
		return DB::table($this->table_name);
	}

	private function build($db, Criteria $criteria = null)
	{
		if ($criteria != null) {
			if ($criteria->select != null) {
				$db = $this->addSelect($db, $criteria->select);
			}

			if (count($criteria->getConditions()) > 0) {
				$conditions = $criteria->getConditions();
				foreach ($conditions as $key => $value) {
					$db = $this->addWhere($db, $value["condition"], $value["params"]);
				}
			}

			if ($criteria->limit != null) {
				$db = $this->addLimit($db, $criteria->limit);
			}

			if ($criteria->offset != null) {
				$db = $this->addOffset($db, $criteria->offset);
			}

			if ($criteria->orderBy != null) {
				$db = $this->addOrderBy($db, $criteria->orderBy);
			}

			if ($criteria->groupBy != null) {
				$db = $this->addGroupBy($db, $criteria->groupBy);
			}

			if ($criteria->having != null) {
				$db = $this->addHaving($db, $criteria->having);
			}


			if (count($criteria->getJoins()) > 0) {
				$db = $this->addJoin($db, $criteria->getJoins());
			}
		}

		return $db;
	}

	private function addSelect($db, $select)
	{
		return $db->selectRaw($select);
	}

	private function addWhere($db, $condition, $params)
	{
		if (count($params) == 0)
			return $db->whereRaw($condition);
		else
			return $db->whereRaw($condition, ...$params);
	}

	private function addLimit($db, $limit)
	{
		return $db->limit($limit);
	}

	private function addOffset($db, $offset)
	{
		return $db->offset($offset);
	}

	private function addOrderBy($db, $orderBy)
	{
		return $db->orderByRaw($orderBy);
	}

	private function addHaving($db, $having)
	{
		return $db->havingRaw($having);
	}

	private function addGroupBy($db, $groupBy)
	{
		return $db->groupBy($groupBy);
	}

	private function addJoin($db, $joins)
	{
		if (isset($joins["left"])) {
			foreach ($joins["left"] as $key => $value) {
				$db = $this->addLeftJoin($db, $value);
			}
		}

		if (isset($joins["inner"])) {
			foreach ($joins["inner"] as $key => $value) {
				$db = $this->addInnerJoin($db, $value);
			}
		}

		return $db;
	}

	private function addLeftJoin($db, $join)
	{
		return $db->leftJoin(...$join);
	}

	private function addInnerJoin($db, $join)
	{
		return $db->join(...$join);
	}
}