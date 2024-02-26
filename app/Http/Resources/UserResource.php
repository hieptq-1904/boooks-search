<?php

namespace App\Http\Resources;

use App\Enums\User;
use App\Enums\UserRole;

class UserResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'tel' => $this->tel,
            'registration_date' => $this->created_at,
            'status' => [
                'id' => $this->status,
                'name' => User::status($this->status),
            ],
        ];

        if (isset($this->access_token)) {
            $data['access_token'] = $this->access_token;
            $data['expired_at'] = $this->expired_at;
        }

        if ($this->hasRole(UserRole::ADMIN)) {
            $data['is_super_admin'] = true;
        }

        return $data;
    }
}
