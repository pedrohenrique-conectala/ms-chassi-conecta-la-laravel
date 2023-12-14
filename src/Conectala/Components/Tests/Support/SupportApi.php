<?php

namespace Conectala\Components\Tests\Support;

trait SupportApi
{
    public string $userToken = '';

//    public function getUserToken(): string
//    {
//        if ($this->userToken) {
//            return $this->userToken;
//        }

//        $user = User::first();
//        $token = $user->createToken($user->email);

//        return $this->userToken = $token->plainTextToken;
//    }

    public function generateHeadersAuthorized(): array
    {
//        return ['Authorization' => "Bearer {$this->getUserToken()}"];
        return [];
    }
}

