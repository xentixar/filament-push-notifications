<?php

namespace Xentixar\FilamentPushNotifications\Controllers;

use App\Models\User;
use Sockeon\Sockeon\Controllers\SocketController;
use Sockeon\Sockeon\Http\Attributes\HttpRoute;
use Sockeon\Sockeon\Http\Request;
use Sockeon\Sockeon\Http\Response;
use Sockeon\Sockeon\WebSocket\Attributes\OnConnect;

class SockeonAuthController extends SocketController
{
    #[OnConnect]
    public function onConnect(int $clientId): void
    {
        $this->emit($clientId, 'sockeon.connected', [
            'clientId' => $clientId,
            'time' => time(),
        ]);
    }

    #[HttpRoute('POST', '/sockeon/auth')]
    public function auth(Request $request): Response
    {
        $validated = $request->validate([
            'clientId' => 'required|integer',
            'userId' => 'required|integer',
        ]);

        if (!$validated) {
            return Response::json([
                'message' => 'Invalid request',
                'errors' => $request->getValidationErrors(),
                'data' => null,
                'status' => false,
            ], 400);
        }

        $clientId = $request->getValidatedData()['clientId'];
        $userId = $request->getValidatedData()['userId'];

        $user = User::query()->find($userId);

        if (!$user) {
            return Response::json([
                'message' => 'User not found',
                'errors' => [],
                'data' => null,
                'status' => false,
            ], 404);
        }

        $this->joinRoom($clientId, 'user.' . $user->id);

        return Response::json([
            'message' => 'Auth successful',
            'data' => null,
            'status' => true,
        ], 200);
    }
}
