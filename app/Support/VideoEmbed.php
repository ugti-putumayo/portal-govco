<?php
namespace App\Support;

class VideoEmbed
{
    public static function parse(?string $url, string $title = 'Video'): ?array
    {
        if (empty($url)) return null;

        $parts = parse_url(trim($url));
        $host  = strtolower($parts['host'] ?? '');
        $host  = preg_replace('~^(www\.|m\.)~', '', $host);
        $path  = $parts['path'] ?? '';

        if ($host === 'youtube.com') {
            parse_str($parts['query'] ?? '', $query);
            $id = $query['v'] ?? null;

            if (! $id && preg_match('~^/(shorts|embed)/([A-Za-z0-9_-]{11})~', $path, $m)) {
                $id = $m[2];
            }

            if ($id && preg_match('~^[A-Za-z0-9_-]{11}$~', $id)) {
                return [
                    'provider' => 'youtube',
                    'src'      => "https://www.youtube-nocookie.com/embed/{$id}",
                    'title'    => $title,
                ];
            }
        }

        if ($host === 'youtu.be') {
            $id = trim($path, '/');
            if ($id && preg_match('~^[A-Za-z0-9_-]{11}$~', $id)) {
                return [
                    'provider' => 'youtube',
                    'src'      => "https://www.youtube-nocookie.com/embed/{$id}",
                    'title'    => $title,
                ];
            }
        }

        if ($host === 'facebook.com' || $host === 'fb.watch') {
            $src = 'https://www.facebook.com/plugins/video.php?href='
                 . urlencode($url)
                 . '&show_text=0&height=314';
            return [
                'provider' => 'facebook',
                'src'      => $src,
                'title'    => $title,
            ];
        }

        return null;
    }
}