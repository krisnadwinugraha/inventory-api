<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ReportService
{
    public function purchaseSummary(array $filters): array
    {
        $sql = "
            SELECT
                v.id                    AS vendor_id,
                v.name                  AS vendor_name,
                COUNT(DISTINCT p.id)    AS total_purchases,
                SUM(pi.qty)             AS total_quantity,
                SUM(pi.qty * pi.price)  AS total_nominal
            FROM vendors v
            INNER JOIN purchases p
                ON p.vendor_id = v.id
            INNER JOIN purchase_items pi
                ON pi.purchase_id = p.id
            WHERE p.purchase_date BETWEEN :start_date AND :end_date
        ";

        $bindings = [
            'start_date' => $filters['start_date'],
            'end_date'   => $filters['end_date'],
        ];

        if (!empty($filters['vendor_id'])) {
            $sql .= " AND v.id = :vendor_id";
            $bindings['vendor_id'] = $filters['vendor_id'];
        }

        $sql .= " GROUP BY v.id, v.name
                  ORDER BY total_nominal DESC";

        return DB::select($sql, $bindings);
    }
}
