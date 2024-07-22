<?php

namespace App\Controllers;

use App\Models\BooksModel;

class Books extends BaseController
{
    protected $bukuModel;
    public function __construct()
    {
        $this->bukuModel = new BooksModel();
    }
    public function index()
    {
        $buku = $this->bukuModel->findAll();
        $data = [
            'title' => 'Daftar Buku',
            'buku' => $buku
        ];

        return view('books/index', $data);
    }

    public function detail($slug)
    {

        $data = [
            'title' => 'Detail Buku',
            'buku' => $this->bukuModel->getBuku($slug)
        ];

        if (empty($data['buku'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Judul Buku' . $slug . 'Tidak ditemukan');
        }

        return view('books/detail', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Form Tambah Buku',
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
        ];
        return view('books/create', $data);
    }

    public function delete($id)
    {
        //cari nama gambar
        $buku = $this->bukuModel->find($id);
        //cek jika file gambar default
        if ($buku['sampul'] != 'no-cover.jpg')
            //hapus gambar
            unlink('img/' . $buku['sampul']);

        $this->bukuModel->delete($id);
        session()->setFlashdata('pesan', 'Data berhasil dihapus');
        return redirect()->to('/books');
    }

    public function save()
    {
        //validasi Input
        if (
            !$this->validate([
                'judul' => [
                    'rules' => 'required|is_unique[books.judul]',
                    'errors' => [
                        'required' => '{field} buku harus diisi',
                        'is_unique' => '{field} buku sudah dimasukkan'
                    ]
                ],
                'penulis' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} buku harus diisi'
                    ]
                ],
                'penerbit' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} buku harus diisi',
                    ]
                ],
                'sampul' => [
                    'rules' => 'max_size[sampul,1024]|mime_in[sampul,image/png,image/jpeg]|is_image[sampul]',
                    'errors' => [
                        'max_size' => 'Ukuran gambar terlalu besar (maksimal 1MB)',
                        'mime_in' => 'Pastikan format file (jpg, jpeg, png)',
                        'is_image' => 'File Yang Anda unggah bukan gambar'
                    ]
                ]
            ])
        ) {
            session()->setFlashdata('validation', \Config\Services::validation());
            return redirect()->to('/books/create')->withInput();
        }

        $gambarSampul = $this->request->getFile('sampul');
        //$namaSampul = $gambarSampul->getName();
        //$namaSampul = $gambarSampul->getRandomName();
        //$gambarSampul->move('img');

        //cek apakah ada file yang diunggah
        if ($gambarSampul->getError() == 4) {
            $namaSampul = 'no-cover.jpg';
        } else {
            //generate nama gambar
            $namaSampul = $gambarSampul->getRandomName();
            //pindah file gambar ke folder img
            $gambarSampul->move('img', $namaSampul);
            //ambil nama file gambar
            //$namaSampul = $gambarSampul->getName();
        }

        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->bukuModel->save([
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul
        ]);

        session()->setFlashdata('pesan', 'Data berhasil ditambahkan');

        return redirect()->to('/books');
    }

    public function edit($slug)
    {
        //session();

        $data = [
            'title' => 'Form Edit Data Buku',
            'validation' => session()->getFlashdata('validation') ?? \Config\Services::validation(),
            'buku' => $this->bukuModel->getBuku($slug)
        ];

        return view('books/edit', $data);
    }

    public function update($id)
    {
        //fungsi cek judul buku yang ada
        $bukuLama = $this->bukuModel->getBuku($this->request->getVar('slug'));
        if ($bukuLama['judul'] == $this->request->getVar('judul')) {
            $rule_judul = 'required';
        } else {
            $rule_judul = 'required|is_unique[books.judul]';
        }
        //validasi Input
        if (
            !$this->validate([
                'judul' => [
                    'rules' => $rule_judul,
                    'errors' => [
                        'required' => '{field} buku harus diisi',
                        'is_unique' => '{field} buku sudah dimasukkan'
                    ]
                ],
                'penulis' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} buku harus diisi',
                        'is_unique' => '{field} sudah dimasukkan'
                    ]
                ],
                'penerbit' => [
                    'rules' => 'required',
                    'errors' => [
                        'required' => '{field} buku harus diisi',
                        'is_unique' => '{field} sudah dimasukkan'
                    ]
                ],
                'sampul' => [
                    'rules' => 'max_size[sampul,1024]|mime_in[sampul,image/png,image/jpeg]|is_image[sampul]',
                    'errors' => [
                        'max_size' => 'Ukuran gambar terlalu besar (maksimal 1MB)',
                        'mime_in' => 'Pastikan format file (jpg, jpeg, png)',
                        'is_image' => 'File Yang Anda unggah bukan gambar'
                    ]
                ]
            ])
        ) {
            session()->setFlashdata('validation', \Config\Services::validation());
            return redirect()->to('/books/edit/' . $this->request->getVar('slug'))->withInput();
        }

        $gambarSampul = $this->request->getFile('sampul');
        //cek gambar, apakah tetap gambar lama
        if ($gambarSampul->getError() == 4) {
            $namaSampul = $this->request->getVar('sampulLama');
        } else {
            //generate nama gambar
            $namaSampul = $gambarSampul->getRandomName();
            //pindahkan gambar
            $gambarSampul->move('img', $namaSampul);
            //hapus file
            unlink('img/' . $this->request->getVar('sampulLama'));
        }

        $slug = url_title($this->request->getVar('judul'), '-', true);
        $this->bukuModel->save([
            'id' => $id,
            'judul' => $this->request->getVar('judul'),
            'slug' => $slug,
            'penulis' => $this->request->getVar('penulis'),
            'penerbit' => $this->request->getVar('penerbit'),
            'sampul' => $namaSampul

        ]);

        session()->setFlashdata('pesan', 'Data berhasil diubah');

        return redirect()->to('/books');
    }


}