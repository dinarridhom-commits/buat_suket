<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\SecurityLog;
use App\Models\SuketAbsen;
use App\Models\SuratUndangan;
use App\Models\SuratUndanganPenerima;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    private const TYPES = [
        'undangan' => [
            'model' => SuratUndangan::class,
            'label' => 'arsip surat undangan',
        ],
        'suket' => [
            'model' => SuketAbsen::class,
            'label' => 'arsip suket lupa absen',
        ],
        'pegawai' => [
            'model' => Pegawai::class,
            'label' => 'data pegawai',
        ],
    ];

    private function resolveType(string $type): array
    {
        if (!isset(self::TYPES[$type])) {
            abort(404);
        }
        return self::TYPES[$type];
    }

    private function wipeRelated(string $type, array $ids): void
    {
        if ($type === 'undangan' && !empty($ids)) {
            SuratUndanganPenerima::whereIn('surat_undangan_id', $ids)->delete();
        }
    }

    public function index(Request $request)
    {
        $counts = [
            'undangan' => SuratUndangan::onlyTrashed()->count(),
            'suket'    => SuketAbsen::onlyTrashed()->count(),
            'pegawai'  => Pegawai::onlyTrashed()->count(),
        ];

        $tab = $request->input('tab');
        if (!isset(self::TYPES[$tab])) {
            $tab = 'undangan';
            if ($counts['undangan'] === 0) {
                foreach ($counts as $key => $cnt) {
                    if ($cnt > 0) {
                        $tab = $key;
                        break;
                    }
                }
            }
        }

        $perPage    = $this->resolvePerPage($request, [10, 15, 25, 50, 100], 10);
        $modelClass = self::TYPES[$tab]['model'];
        $query      = $modelClass::onlyTrashed()->latest('deleted_at');

        if ($tab === 'undangan') {
            $query->with('penerimas');
        }

        $items = $query->paginate($perPage)->withQueryString();

        return view('trash.index', compact('tab', 'items', 'counts', 'perPage'));
    }

    public function restore(string $type, int $id)
    {
        $cfg  = $this->resolveType($type);
        $item = $cfg['model']::onlyTrashed()->findOrFail($id);
        $item->restore();

        return redirect()->route('trash.index', ['tab' => $type])
            ->with('success', ucfirst($cfg['label']) . ' berhasil dipulihkan.');
    }

    public function forceDelete(string $type, int $id)
    {
        $cfg  = $this->resolveType($type);
        $item = $cfg['model']::onlyTrashed()->findOrFail($id);

        $this->wipeRelated($type, [$item->id]);
        $item->forceDelete();

        SecurityLog::record('trash_force_delete', "Menghapus permanen 1 {$cfg['label']} (ID #{$id}).", 'warning');

        return redirect()->route('trash.index', ['tab' => $type])
            ->with('success', ucfirst($cfg['label']) . ' berhasil dihapus permanen.');
    }

    public function bulk(Request $request, string $type)
    {
        $cfg = $this->resolveType($type);

        $validated = $request->validate([
            'ids'    => 'required|array|min:1',
            'ids.*'  => 'integer',
            'action' => 'required|in:restore,force_delete',
        ], [
            'ids.required' => 'Pilih minimal satu data terlebih dahulu.',
            'ids.min'      => 'Pilih minimal satu data terlebih dahulu.',
        ]);

        $ids = array_values(array_unique(array_map('intval', $validated['ids'])));

        if ($validated['action'] === 'restore') {
            $count = $cfg['model']::onlyTrashed()->whereIn('id', $ids)->restore();
            return redirect()->route('trash.index', ['tab' => $type])
                ->with('success', "{$count} {$cfg['label']} berhasil dipulihkan.");
        }

        $this->wipeRelated($type, $ids);
        $count = $cfg['model']::onlyTrashed()->whereIn('id', $ids)->forceDelete();

        SecurityLog::record('trash_force_delete', "Menghapus permanen massal {$count} {$cfg['label']}.", 'warning', $request);

        return redirect()->route('trash.index', ['tab' => $type])
            ->with('success', "{$count} {$cfg['label']} berhasil dihapus permanen.");
    }

    public function empty(string $type)
    {
        $cfg   = $this->resolveType($type);
        $ids   = $cfg['model']::onlyTrashed()->pluck('id')->all();
        $count = count($ids);

        if ($count > 0) {
            $this->wipeRelated($type, $ids);
            $cfg['model']::onlyTrashed()->whereIn('id', $ids)->forceDelete();
            SecurityLog::record('trash_emptied', "Mengosongkan {$count} {$cfg['label']} secara permanen dari Riwayat Penghapusan.", 'warning');
        }

        return redirect()->route('trash.index', ['tab' => $type])
            ->with('success', "{$count} {$cfg['label']} di Riwayat Penghapusan berhasil dihapus permanen.");
    }
}
