<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Announcement extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Announcements'
        ];

        return view('announcements/index', $data);
    }
}
