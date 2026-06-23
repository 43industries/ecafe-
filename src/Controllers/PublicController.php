<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Response;
use App\Models\AnnouncementModel;
use App\Models\MenuModel;

class PublicController
{
    public function home(): void
    {
        $menu = new MenuModel();
        $announcements = new AnnouncementModel();
        Response::view('public/home', [
            'title' => 'Home',
            'specials' => $menu->getSpecials(6),
            'announcements' => $announcements->getActive('student'),
            'popular' => $menu->getPopular(4),
        ]);
    }

    public function about(): void
    {
        Response::view('public/about', ['title' => 'About Us']);
    }

    public function menu(): void
    {
        $menu = new MenuModel();
        $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
        $search = $_GET['q'] ?? null;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        Response::view('public/menu', [
            'title' => 'Menu',
            'categories' => $menu->getCategories(),
            'items' => $menu->search($search, $categoryId, $perPage, $offset),
            'total' => $menu->countSearch($search, $categoryId),
            'page' => $page,
            'perPage' => $perPage,
            'search' => $search,
            'categoryId' => $categoryId,
        ]);
    }

    public function contact(): void
    {
        Response::view('public/contact', ['title' => 'Contact']);
    }

    public function contactSubmit(): void
    {
        Response::json(['success' => true, 'message' => 'Thank you! We will get back to you soon.']);
    }
}
