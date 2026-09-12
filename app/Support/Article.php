<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Prepares post body HTML for rendering.
 *
 * Gives every heading a stable id and returns the table of contents built
 * from the same pass, so the links and the anchors can never disagree.
 */
class Article
{
    /**
     * @param  list<array{id: string, text: string, level: int}>  $headings
     */
    public function __construct(
        public readonly string $html,
        public readonly array $headings,
        public readonly int $readingMinutes,
    ) {}

    public static function fromHtml(?string $html): self
    {
        $html = trim((string) $html);

        if ($html === '') {
            return new self('', [], 0);
        }

        $headings = [];
        $used = [];

        $processed = preg_replace_callback(
            '/<h([23])(\s[^>]*)?>(.*?)<\/h\1>/is',
            function (array $match) use (&$headings, &$used): string {
                $level = (int) $match[1];
                $attributes = $match[2] ?? '';
                $inner = $match[3];
                $text = trim(html_entity_decode(strip_tags($inner), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

                if ($text === '') {
                    return $match[0];
                }

                // Two headings can legitimately share wording, so ids are
                // de-duplicated rather than assumed unique.
                $id = Str::slug($text) ?: 'section';
                $base = $id;
                $suffix = 2;

                while (isset($used[$id])) {
                    $id = "{$base}-{$suffix}";
                    $suffix++;
                }

                $used[$id] = true;
                $headings[] = ['id' => $id, 'text' => $text, 'level' => $level];

                // An author-supplied id is left alone; only add one when
                // the heading has none.
                if (preg_match('/\sid\s*=/i', $attributes)) {
                    return $match[0];
                }

                return "<h{$level}{$attributes} id=\"{$id}\">{$inner}</h{$level}>";
            },
            $html,
        );

        return new self($processed ?? $html, $headings, static::readingMinutes($html));
    }

    /** Rounded up, and never zero for a post that has any content. */
    public static function readingMinutes(string $html): int
    {
        $words = str_word_count(strip_tags($html));

        return max(1, (int) ceil($words / 200));
    }

    public function hasTableOfContents(): bool
    {
        // One or two headings is a list, not a table of contents.
        return count($this->headings) >= 3;
    }
}
