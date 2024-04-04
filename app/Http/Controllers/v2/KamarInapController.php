<?php

namespace App\Http\Controllers\v2;

use Orion\Concerns\DisableAuthorization;

class KamarInapController extends \Orion\Http\Controllers\Controller
{
    use DisableAuthorization;

    protected $model = \App\Models\KamarInap::class;

    /**
     * Default pagination limit.
     *
     * @return int
     */
    public function limit(): int
    {
        return 10;
    }


    /**
     * The name of the field used to fetch parent resource from the database.
     *
     * @return string
     */
    protected function parentKeyName(): string
    {
        return 'no_rawat';
    }

    /**
     * The name of the field used to fetch a resource from the database.
     *
     * @return string
     */
    protected function keyName(): string
    {
        return 'no_rawat';
    }

    /**
     * The attributes that are used for searching.
     *
     * @return array
     */
    public function searchableBy(): array
    {
        return ['no_rawat', 'kd_kamar', 'lama', 'stts_pulang'];
    }

    /**
     * The attributes that are used for filtering.
     *
     * @return array
     */
    public function filterableBy(): array
    {
        return ['tgl_masuk', 'tgl_keluar', 'stts_pulang'];
    }

    /**
     * The attributes that are used for sorting.
     *
     * @return array
     */
    public function sortableBy(): array
    {
        return ['no_rawat', 'tgl_masuk', 'tgl_keluar'];
    }

    /**
     * The relations and fields that are allowed to be aggregated on a resource.
     *
     * @return array
     */
    public function aggregates(): array
    {
        return [];
    }

    /**
     * The relations that are allowed to be included together with a resource.
     *
     * @return array
     */
    public function includes(): array
    {
        return ['sep', 'pasien', 'kamar', 'ranapGabung', 'regPeriksa'];
    }
}
