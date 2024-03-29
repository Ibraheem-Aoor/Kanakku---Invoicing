<?php
// Implementation taken from nova-backup-tool - https://github.com/spatie/nova-backup-tool/

namespace Kanakku\Http\Controllers\V1\Backup;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\BackupDestination\BackupDestination;
use Spatie\Backup\Helpers\Format;
use Kanakku\Jobs\CreateBackupJob;
use Kanakku\Rules\Backup\BackupDisk;
use Kanakku\Rules\Backup\PathToZip;
use Illuminate\Http\JsonResponse;

class BackupsController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $configuredBackupDisks = config('backup.backup.destination.disks');

        try {
            $backupDestination = BackupDestination::create(config('filesystems.default'), config('backup.backup.name'));

            $backups = Cache::remember("backups-{$request->file_disk_id}", now()->addSeconds(4), function () use ($backupDestination) {
                return $backupDestination
                    ->backups()
                    ->map(function (Backup $backup) {
                        return [
                            'path' => $backup->path(),
                            'created_at' => $backup->date()->format('Y-m-d H:i:s'),
                            'size' => Format::humanReadableSize($backup->size()),
                        ];
                    })
                    ->toArray();
            });

            return response()->json([
                'backups' => $backups,
                'disks' => $configuredBackupDisks
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'backups' => [],
                'error' => 'invalid_disk_credentials',
                'error_message' => $e->getMessage(),
                'disks' => $configuredBackupDisks
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        dispatch(new CreateBackupJob($request->all()))->onQueue(config('backup.queue.name'));

        return $this->respondSuccess();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function destroy($disk, Request $request)
    {
        $validated = $request->validate([
            'path' => ['required', new PathToZip()],
        ]);

        $backupDestination = BackupDestination::create(config('filesystems.default'), config('backup.backup.name'));

        $backupDestination
            ->backups()
            ->first(function (Backup $backup) use ($validated) {
                return $backup->path() === $validated['path'];
            })
            ->delete();

        return $this->respondSuccess();
    }
}
