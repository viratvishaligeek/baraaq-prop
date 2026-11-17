<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'ul', 'ol', 'li',
        'a', 'h2', 'h3', 'h4', 'blockquote', 'span',
    ];

    private const DROP_TAGS = ['script', 'style', 'iframe', 'object', 'embed', 'svg', 'math'];

    public static function sanitize(?string $html): string
    {
        if ($html === null || trim($html) === '') {
            return '';
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="cms-safe-root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('cms-safe-root');
        if (! $root) {
            return e(strip_tags($html));
        }

        self::cleanNode($root);

        $safe = '';
        foreach ($root->childNodes as $child) {
            $safe .= $document->saveHTML($child);
        }

        return $safe;
    }

    private static function cleanNode(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if (! $child instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);
            if (in_array($tag, self::DROP_TAGS, true)) {
                $node->removeChild($child);
                continue;
            }

            if (! in_array($tag, self::ALLOWED_TAGS, true)) {
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            foreach (iterator_to_array($child->attributes) as $attribute) {
                $name = strtolower($attribute->name);
                $allowed = $tag === 'a'
                    ? in_array($name, ['href', 'title', 'target', 'rel'], true)
                    : ($tag === 'span' && $name === 'class');

                if (! $allowed) {
                    $child->removeAttribute($attribute->name);
                    continue;
                }

                if ($name === 'href' && ! preg_match('~^(https?://|mailto:|tel:|/|#)~i', trim($attribute->value))) {
                    $child->removeAttribute('href');
                }

                if ($name === 'class' && ! preg_match('/^[a-zA-Z0-9 _-]{1,120}$/', $attribute->value)) {
                    $child->removeAttribute('class');
                }
            }

            if ($tag === 'a' && strtolower($child->getAttribute('target')) === '_blank') {
                $child->setAttribute('rel', 'noopener noreferrer');
            }

            self::cleanNode($child);
        }
    }
}
