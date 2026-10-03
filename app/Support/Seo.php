<?php

namespace App\Support;

use Illuminate\Support\Str;

class Seo
{
    public static function url(?string $path = null): string
    {
        $base = rtrim(config('app.url'), '/');
        $path ??= request()->getPathInfo();

        return $base.($path === '/' ? '' : '/'.ltrim($path, '/'));
    }

    public static function description(array $site, ?string $description = null): string
    {
        $name = $site['site_name'] ?? config('app.name');
        $description ??= match (request()->route()?->getName()) {
            'about' => "Kenali {$name}, software house untuk pengembangan aplikasi web, mobile, dan sistem bisnis. Pelajari profil dan cara kerja kami.",
            'services.index' => "Layanan {$name}: pengembangan aplikasi web, mobile, sistem ERP, UI/UX, cloud, dan konsultasi IT sesuai kebutuhan bisnis.",
            'projects.index' => "Portofolio {$name}. Lihat proyek pengembangan aplikasi, kebutuhan bisnis, dan teknologi yang digunakan.",
            'contact' => "Hubungi {$name} untuk membahas kebutuhan aplikasi, fitur prioritas, integrasi, serta rencana pengembangan sistem bisnis Anda.",
            default => $site['meta_description'] ?? "{$name}, software house untuk pengembangan aplikasi web, mobile, dan sistem bisnis.",
        };

        return Str::limit(Str::squish(html_entity_decode(strip_tags($description), ENT_QUOTES, 'UTF-8')), 160, '');
    }

    public static function structuredData(array $site, string $title, string $description, string $logo, ?string $image = null): array
    {
        $home = self::url('/');
        $canonical = self::url();
        $organization = [
            '@type' => 'Organization', '@id' => $home.'/#organization',
            'name' => $site['site_name'] ?? config('app.name'), 'url' => $home,
            'logo' => self::absoluteAsset($logo),
        ];
        foreach (['email', 'phone' => 'telephone', 'address'] as $key => $field) {
            if (is_int($key)) {
                $key = $field;
            }
            if (filled($site[$key] ?? null)) {
                $organization[$field] = $site[$key];
            }
        }
        $socials = array_values(array_filter([$site['instagram'] ?? null, $site['linkedin'] ?? null], fn ($url) => $url && filter_var($url, FILTER_VALIDATE_URL)));
        if ($socials) {
            $organization['sameAs'] = $socials;
        }
        $page = [
            '@type' => 'WebPage', '@id' => $canonical.'#webpage', 'url' => $canonical,
            'name' => $title, 'description' => $description, 'inLanguage' => 'id-ID',
            'isPartOf' => ['@id' => $home.'/#website'], 'about' => ['@id' => $home.'/#organization'],
        ];
        if ($image) {
            $page['primaryImageOfPage'] = ['@type' => 'ImageObject', 'url' => self::absoluteAsset($image)];
        }

        return ['@context' => 'https://schema.org', '@graph' => [
            $organization,
            ['@type' => 'WebSite', '@id' => $home.'/#website', 'url' => $home, 'name' => $organization['name'], 'publisher' => ['@id' => $organization['@id']]],
            $page,
        ]];
    }

    public static function absoluteAsset(string $url): string
    {
        if (preg_match('#^https?://#i', $url)) {
            $siteHost = parse_url(config('app.url'), PHP_URL_HOST);
            if (parse_url($url, PHP_URL_HOST) === $siteHost) {
                return self::url(parse_url($url, PHP_URL_PATH) ?: '/');
            }

            return $url;
        }

        return self::url('/'.ltrim($url, '/'));
    }
}
