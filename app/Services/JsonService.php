<?php

namespace App\Services;

class JsonService
{
    public function get(string $message, bool $success, $data = null, int $status = 200)
    {
        if ($data === null) {
            return response()->json([
                'message' => $message,
                'success' => $success,
            ], $status);
        }

        return response()->json([
            'message' => $message,
            'success' => $success,
            'data' => $data,
        ], $status);
    }
}
