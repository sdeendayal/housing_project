<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class GlobalGoogleTranslateMiddleware
{
    /**
     * Handle an incoming request and automatically inject Google Translate & layout protection.
     */
    public function handle(Request $request, Closure $next): SymfonyResponse
    {
        $response = $next($request);

        // 1. Only process standard responses that support content modification
        if (! method_exists($response, 'getContent') || ! method_exists($response, 'setContent')) {
            return $response;
        }

        // 2. Skip API calls, JSON, redirects, or non-HTML responses
        if ($request->is('api/*') || $request->expectsJson() || $response->isRedirection()) {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');
        if ($contentType !== '' && ! str_contains($contentType, 'text/html')) {
            return $response;
        }

        $content = $response->getContent();
        if (! is_string($content) || empty($content)) {
            return $response;
        }

        // 3. Must be a complete HTML document containing </head> and </body>
        if (! str_contains($content, '</head>') || ! str_contains($content, '</body>')) {
            return $response;
        }

        try {
            // Render the universal CSS & protection snippet for <head>
            $headSnippet = view('partials.google-translate-head')->render();

            // Render the widget initialization & tidy script for <body>
            $bodySnippet = view('partials.google-translate')->render();

            // 4. Auto-detect if the page already has a language switcher element
            // If NOT present, automatically provide a clean, elegant floating badge in top-right
            $floatingWidget = '';
            if (! str_contains($content, 'id="google_translate_element"')) {
                $floatingWidget = '
<div id="google_translate_floating_container" class="notranslate" translate="no" style="position:fixed;top:10px;right:15px;z-index:999999;display:inline-flex;align-items:center;background:rgba(255,255,255,0.96);padding:3px 8px;border-radius:8px;box-shadow:0 4px 15px rgba(0,0,0,0.12);border:1px solid #cbd5e1;">
    <div id="google_translate_element"></div>
</div>';
            }

            // 5. Inject before </head>
            $headPos = strripos($content, '</head>');
            if ($headPos !== false) {
                $content = substr_replace($content, $headSnippet . "\n</head>", $headPos, strlen('</head>'));
            }

            // 6. Inject floating container (if needed) & body script before </body>
            $bodyPos = strripos($content, '</body>');
            if ($bodyPos !== false) {
                $injection = $floatingWidget . "\n" . $bodySnippet . "\n</body>";
                $content = substr_replace($content, $injection, $bodyPos, strlen('</body>'));
            }

            $response->setContent($content);
            $response->headers->remove('Content-Length');
        } catch (\Throwable $e) {
            // Failsafe: log any render error without breaking the user response
            report($e);
        }

        return $response;
    }
}
