<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PayrollExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $users;

    // Constructor to receive data
    public function __construct($users)
    {
        $this->users = $users;
    }

    // Return the data collection to be exported
    public function collection()
    {
        return $this->users;
    }

    // Define the headings for the Excel file
    public function headings(): array
    {
        return [
            'User ID',  // Add User ID column
            'Username',
            'Employee ID',
            'Day Off Count',
            'Leave Count',
            'Total Absentees',
            'Absentee Fine',
            'Late Fine',
            'Total Deduction',
        ];
    }

    // Map the data to columns in the Excel file
    public function map($user): array
    {
        return [
            $user->user_id,  // Add user_id here
            $user->username,
            $user->employee_id,
            $user->dayoff_count ?? 0,
            $user->leave_count ?? 0,
            $user->total_absentees ?? 0,
            round($user->absentee_fine ?? 0, 3) . '%',
            round($user->late_fine ?? 0, 3) . '%',
            round(($user->absentee_fine + $user->late_fine) ?? 0, 3) . '%',
        ];
    }

    // Style the header and data cells
    public function styles(Worksheet $worksheet)
    {
        // Apply bold font for headers (first row)
        $worksheet->getStyle('A1:I1')->getFont()->setBold(true);

        // Set header row height to 33.75
        $worksheet->getRowDimension(1)->setRowHeight(33.75);


        // Align all cells to the center
        $worksheet->getStyle('A1:I' . (count($this->users) + 1))
            ->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [
            // Additional styling for header row (optional)
            'A1:I1' => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}