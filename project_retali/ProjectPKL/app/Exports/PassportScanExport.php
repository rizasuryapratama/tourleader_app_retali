<?php
namespace App\Exports;

use App\Models\PassportScan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PassportScanExport implements FromView
{
    protected ?string $tanggal;

    public function __construct(?string $tanggal = null)
    {
        $this->tanggal = $tanggal;
    }

    public function view(): View
    {
        $query = PassportScan::with('tourleader:id,name')
            ->orderByDesc('scanned_at');

        if ($this->tanggal) {
            $query->whereDate('scanned_at', $this->tanggal);
        }

        return view('admin.scan_paspor.export', [
            'scans' => $query->get()
        ]);
    }
}
