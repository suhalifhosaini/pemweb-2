<?php

namespace App\Controllers;

class Pages extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Selamat Datang | Website Anda',
            'tes' => ['satu', 'dua', 'tiga']
        ];

        return view('pages/home', $data);
    }

    public function about()
    {
        $data = [
            'title' => 'Selamat Datang | Unipdu Press',
        ];
        return view('pages/about', $data);
    }

    public function contact()
    {
        $data = [
            'title' => 'contact',
            'addres' =>
            [
                [
                    'tipe' => 'Rumah',
                    'alamat' => 'Arjasa, Sumenep',
                    'kota' => 'Sumenep'
                ],
                [
                    'tipe' => 'Kampus',
                    'alamat' => 'Rejoso, Peterongan',
                    'kota' => 'Jombang'
                ]
            ]
        ];
        return view('pages/contact', $data);
    }
}
