<?php namespace App\Controllers;
class Page extends BaseController {
public function about()
{
    echo "about page";
}
public function contact()
{
    echo "contact page";
}
public function faqs()
{
    echo "Faqs page";
}
public function tos()
{
    echo "Halaman Term of Service";
}
public function biodata()
    {
        echo "Nama       : Suhalif Hosaini";
        echo "<br>NIM    : 4120039";
        echo "<br>Alamat : sumenep, kangean";
        echo "<br>Hobi   : baca buku";
        echo "<br>E-mail : suhalifhosaini14@gmail.com";
    }
}