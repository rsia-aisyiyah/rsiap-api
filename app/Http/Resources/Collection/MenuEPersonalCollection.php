<?php

namespace App\Http\Resources\Collection;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MenuEPersonalCollection extends ResourceCollection
{
    public static $wrap = 'data';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'success' => empty($this->collection) ? false : true,
            'message' => empty($this->collection) ? 'belum ada menu' : 'berhasil menampilkan menu',
            'data' => $this->collection,
        ];
    }
}
