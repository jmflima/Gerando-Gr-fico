<?php

namespace SONFin\Repository;

use Illuminate\Support\Collection;
use SONFin\Models\BillPay;
use SONFin\Models\BillReceive;

class StatementRepository implements StatementRepositoryInterface
{

	public function all(string $dateStart, $dateEnd, int $userId): array
	{ // select from bill_pays left joincategory_costs
		$billPays = BillPay::query()
					->selectRaw('bill_pays.*, category_costs.name as category_name')
					->leftJoin('category_costs', 'category_costs.id', '=', 'bill_pays.category_cost_id')
					->whereBetween('date_lance', [$dateStarde,$dateEnd])
					->where('bill_pays.user_id', $userId)
					->get();
		$billReceives = BillReceive::query()
					->whereBetween('date_lance', [$dateStarde,$dateEnd])
					->where('user_id', $userId)
					->get();
					
		$collection = new Collection(array_merge_recursive($billPays->toArray(), $billReceives->toArray()));
		$statements = $collection->sortBydesc('date_lance');
		return [
			'statements' => $statements,
			'total_pays' => $billPays->sum('value'),
			'total_receives' => $billReceives->sum('value')
		];
	}
			
}