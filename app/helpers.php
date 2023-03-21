<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Inertia\Inertia;

enum RenderType: string {
    case BLADE = 'blade';
    case INERTIA = 'inertia' ;
}

function setRenderType(): void
{
    $sessionRenderType = getRenderType();
    $newRenderType = ($sessionRenderType === RenderType::BLADE->value) ?
        RenderType::INERTIA->value : RenderType::BLADE->value;

    $_SESSION['renderType'] = $newRenderType;
}

function renderViewDynamic($viewName, $data = []): \Illuminate\Contracts\View\View|\Inertia\Response
{
    $viewData = [...$data, ...getContent($viewName)];

    $sessionRenderType = getRenderType();
    if ($sessionRenderType === RenderType::BLADE->value) {
        return View::make($viewName, $viewData);
    } else {
        $inertiaName = Str::replace('.', '/', $viewName);
        $inertiaName = ucwords($inertiaName, '/');
        return Inertia::render($inertiaName, $viewData);
    }
}

function getMenus(): array
{
    $menus = [
        ['title' => 'Main',     'url' => '/'],
        ['title' => 'Blog',     'url' => '/blog'],
        ['title' => 'Chat',     'url' => '/chat'],
        ['title' => 'Profile',  'url' => '/about'],
    ];

    return $menus;
}

function getContent(string $view): array
{
    $pagesContent = [
        'pages.home' => [
            'title' => 'Tymur Mardas, Main page',
            'content' => fn() => File::get(resource_path('data/home_content.html'))],
        'pages.chat' => [
            'title' => 'Chat with Tymur Mardas',
            'content' => null],
        'pages.profile' => [
            'title' => 'Tymur Mardas\' Profile',
            'content' => fn() => File::get(resource_path('data/profile_content.html'))],
    ];

    return $pagesContent[$view] ?? [];
}

function getRenderType(): string
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['renderType'] ?? RenderType::BLADE->value;
}

function checkMenuIsActive($menuUrl): bool
{
    if ($menuUrl === '/') {
        return Request::getPathInfo() === $menuUrl;
    } else {
        return Str::startsWith(Request::getPathInfo(), $menuUrl);
    }
}
function postDateFormat($date): string
{
    $days = \Carbon\Carbon::parse($date)->diffInDays(\Carbon\Carbon::now());

    $daysFormatted = match (true) {
        $days >= 1000 => 'very old',
        $days >= 650 => 'almost two years ago',
        $days >= 365 => 'a year ago or so',
        $days >= 300 => 'almost a year ago',
        $days >= 186 => 'half a year ago',
        $days >= 122 => 'several month ago',
        $days >= 66 => 'few months ago',
        $days >= 35 => 'a month ago',
        $days >= 21 => 'several weeks ago',
        $days >= 14 => 'few weeks ago',
        $days >= 7 => 'a week ago',
        $days >= 2 => 'few days ago',
        $days >= 0 => 'new',
        default => '?',
    };

    return $daysFormatted;
}
