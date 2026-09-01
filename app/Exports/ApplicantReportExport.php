<?php

namespace App\Exports;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ApplicantReportExport implements FromQuery, WithHeadings
{
    public function __construct(
        protected int|string $districtId,
        protected string $status = 'all_applicants',
        protected string $phase = 'all',
        protected int|string|null $villageId = null,
        protected ?string $search = null,
    ) {
    }

    public function query(): Builder
    {
        /*
        |--------------------------------------------------------------------------
        | Registry Mobile Subquery
        |--------------------------------------------------------------------------
        | DISTINCT लगाने से एक mobile की multiple registry entries होने पर
        | applicant duplicate नहीं होगा।
        */

        $registryMobileQuery = DB::table('registary')
            ->select('SecondPartyMobile')
            ->whereNotNull('SecondPartyMobile')
            ->where('SecondPartyMobile', '<>', '')
            ->distinct();

        $query = DB::table('ownermaster as o')
            ->join('villagemaster as v', function ($join) {
                $join->on('v.VillageId', '=', 'o.VillageId')
                    ->on('v.DistrictId', '=', 'o.DistrictId');
            })
            ->leftJoin('flatmaster as f', 'f.FlatId', '=', 'o.FlatId')
            ->leftJoinSub($registryMobileQuery, 'r', function ($join) {
                $join->on('r.SecondPartyMobile', '=', 'o.MobileNo');
            })

            /*
            |--------------------------------------------------------------------------
            | सबसे जरूरी District Filter
            |--------------------------------------------------------------------------
            */

            ->where('o.DistrictId', $this->districtId)
            ->where('v.DistrictId', $this->districtId)

            /*
            |--------------------------------------------------------------------------
            | Excel Columns
            |--------------------------------------------------------------------------
            */

            ->select([
                'o.RegistrationNo',
                'o.OwnerName',
                'o.Relation',
                'o.FatherHusbandName',
                'o.Gender',
                DB::raw("""
                    CASE
                        WHEN o.Caste IS NULL OR TRIM(o.Caste) = '' THEN 'Others'
                        WHEN LOWER(TRIM(o.Caste)) = 'sc' THEN 'SC'
                        WHEN LOWER(TRIM(o.Caste)) = 'ghumantu' THEN 'Ghumantu'
                        WHEN LOWER(TRIM(o.Caste)) = 'widow' THEN 'Widow'
                        WHEN LOWER(TRIM(o.Caste)) IN ('general', 'others') THEN 'Others'
                        ELSE 'Others'
                    END AS Caste
                """),
                'o.MobileNo',
                'o.PPPId',
                'o.MemberId',
                'v.VillageName',
                'o.Phase',
                'f.FlatNo',
            ])

            ->selectRaw("
                CASE
                    WHEN f.FlatId IS NOT NULL
                        THEN 'Allotted'
                    ELSE 'Not Allotted'
                END AS AllotmentStatus
            ")

            ->selectRaw("
                CASE
                    WHEN COALESCE(o.IsAllotmentCancelled, 0) = 1
                        THEN 'Cancelled'

                    WHEN COALESCE(o.IsRejected, 0) = 1
                        THEN 'Rejected'

                    WHEN f.FlatId IS NOT NULL
                        AND COALESCE(o.IsApproved, 0) = 1
                        AND COALESCE(o.IsPaid, 0) = 1
                        AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                        THEN 'Approved & Paid'

                    WHEN f.FlatId IS NOT NULL
                        AND COALESCE(o.IsApproved, 0) = 1
                        AND COALESCE(o.IsPaid, 0) = 0
                        AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                        THEN 'Approved & Unpaid'

                    WHEN f.FlatId IS NOT NULL
                        AND COALESCE(o.IsApproved, 0) = 0
                        AND COALESCE(o.IsPaid, 0) = 0
                        AND COALESCE(o.IsRejected, 0) = 0
                        AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                        THEN 'Yet to be Approved'

                    ELSE 'Pending'
                END AS ApplicantStatus
            ")

            ->selectRaw("
                CASE
                    WHEN f.FlatId IS NOT NULL
                        AND COALESCE(o.IsApproved, 0) = 1
                        AND COALESCE(o.IsPaid, 0) = 1
                        AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                        THEN 'Paid'

                    WHEN f.FlatId IS NOT NULL
                        AND COALESCE(o.IsApproved, 0) = 1
                        AND COALESCE(o.IsPaid, 0) = 0
                        AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                        THEN 'Unpaid'

                    ELSE 'Not Applicable'
                END AS PaymentStatus
            ")

            ->selectRaw("
                CASE
                    WHEN f.FlatId IS NOT NULL
                        AND COALESCE(o.IsApproved, 0) = 1
                        AND COALESCE(o.IsPaid, 0) = 1
                        AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                        AND r.SecondPartyMobile IS NOT NULL
                        THEN 'Registry Done'

                    WHEN f.FlatId IS NOT NULL
                        AND COALESCE(o.IsApproved, 0) = 1
                        AND COALESCE(o.IsPaid, 0) = 1
                        AND COALESCE(o.IsAllotmentCancelled, 0) = 0
                        AND r.SecondPartyMobile IS NULL
                        THEN 'Registry Yet to be Done'

                    ELSE 'Not Applicable'
                END AS RegistryStatus
            ")

            ->addSelect([
                'o.Remarks',
                'o.DCRemarks',
                'o.OwnerAddress',
                'o.CreatedDate',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Phase Filter
        |--------------------------------------------------------------------------
        */

        if ($this->phase !== 'all') {
            $query
                ->where('o.Phase', $this->phase)
                ->where('v.phase', $this->phase);
        } else {
            $query->whereColumn('o.Phase', 'v.phase');
        }

        /*
        |--------------------------------------------------------------------------
        | Village Filter
        |--------------------------------------------------------------------------
        */

        if ($this->villageId !== null && $this->villageId !== '') {
            $query->where('o.VillageId', $this->villageId);
        }

        /*
        |--------------------------------------------------------------------------
        | Search Filter
        |--------------------------------------------------------------------------
        */

        if ($this->search !== null && trim($this->search) !== '') {
            $search = trim($this->search);

            $query->where(function (Builder $subQuery) use ($search) {
                $subQuery
                    ->where('o.OwnerName', 'like', "%{$search}%")
                    ->orWhere('o.RegistrationNo', 'like', "%{$search}%")
                    ->orWhere('o.MobileNo', 'like', "%{$search}%")
                    ->orWhere('o.PPPId', 'like', "%{$search}%")
                    ->orWhere('o.MemberId', 'like', "%{$search}%")
                    ->orWhere('f.FlatNo', 'like', "%{$search}%")
                    ->orWhere('v.VillageName', 'like', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status Filter
        |--------------------------------------------------------------------------
        */

        $this->applyStatusFilter($query);

        /*
        |--------------------------------------------------------------------------
        | Export Ordering
        |--------------------------------------------------------------------------
        | OwnerId indexed/primary key होने के कारण heavy alphabetical sorting
        | से बेहतर performance मिलेगी।
        */

        return $query->orderBy('o.OwnerId');
    }

    private function applyStatusFilter(Builder $query): void
    {
        switch ($this->status) {
            case 'allotted':
                $query->whereNotNull('f.FlatId');
                break;

            case 'approved_paid':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsApproved', 1)
                    ->where('o.IsPaid', 1)
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0');
                break;

            case 'approved_unpaid':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsApproved', 1)
                    ->whereRaw('COALESCE(o.IsPaid, 0) = 0')
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0');
                break;

            case 'pending':
                $query
                    ->whereNotNull('f.FlatId')
                    ->whereRaw('COALESCE(o.IsApproved, 0) = 0')
                    ->whereRaw('COALESCE(o.IsPaid, 0) = 0')
                    ->whereRaw('COALESCE(o.IsRejected, 0) = 0')
                    ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0');
                break;

            case 'rejected':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsRejected', 1);
                break;

            case 'cancelled':
                $query
                    ->whereNotNull('f.FlatId')
                    ->where('o.IsAllotmentCancelled', 1);
                break;

            case 'registry_allotted':
                $this->applyRegistryBaseFilter($query);
                break;

            case 'registry_done':
                $this->applyRegistryBaseFilter($query);

                $query->whereNotNull('r.SecondPartyMobile');
                break;

            case 'registry_pending':
                $this->applyRegistryBaseFilter($query);

                $query->whereNull('r.SecondPartyMobile');
                break;

            case 'all_applicants':
            default:
                break;
        }
    }

    private function applyRegistryBaseFilter(Builder $query): void
    {
        $query
            ->whereNotNull('f.FlatId')
            ->where('o.IsApproved', 1)
            ->where('o.IsPaid', 1)
            ->whereRaw('COALESCE(o.IsAllotmentCancelled, 0) = 0');
    }

    public function headings(): array
    {
        return [
            'Registration No',
            'Applicant Name',
            'Relation',
            'Father/Husband Name',
            'Gender',
            'Caste',
            'Mobile No',
            'PPP ID',
            'Member ID',
            'Village',
            'Phase',
            'Flat No',
            'Allotment Status',
            'Applicant Status',
            'Payment Status',
            'Registry Status',
            'Remarks',
            'DC Remarks',
            'Address',
            'Created Date',
        ];
    }
}