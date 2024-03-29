<?php

namespace App\Http\Resources;

use App\Models\Session;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'online' => false,
            'session' => $this->session_details($this->id)
        ];
    }
    private function session_details($id)
    {
        $session = Session::whereIn('user_id', [auth()->id(), $id])->whereIn('psikolog_id', [auth()->id(), $id])->first();
        return new SessionResource($session);
    }
}
