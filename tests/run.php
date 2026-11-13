<?php

declare(strict_types=1);

/**
 * Stage 1 test runner (plain PHP, no PHPUnit needed).
 * Run from the project root:   php tests/run.php
 * Exit code is 0 when every test passes, 1 otherwise.
 * PHPUnit is introduced in Stage 11.
 */

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Env;
use App\Core\Request;
use App\Core\Response;
use App\Core\Router;

$passed = 0;
$failed = 0;

function check(string $name, bool $condition): void
{
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  [PASS] $name\n";
    } else {
        $failed++;
        echo "  [FAIL] $name\n";
    }
}

echo "Env parser\n";
$env = Env::parse("# comment\nAPP_NAME=StockPilot\nDB_PASSWORD=\nQUOTED=\"hello world\"\nINLINE=value # trailing comment\nURL=http://a.b/?x=1\n");
check('reads simple values', $env['APP_NAME'] === 'StockPilot');
check('keeps empty values', array_key_exists('DB_PASSWORD', $env) && $env['DB_PASSWORD'] === '');
check('handles quoted values', $env['QUOTED'] === 'hello world');
check('strips inline comments', $env['INLINE'] === 'value');
check('keeps = inside values', $env['URL'] === 'http://a.b/?x=1');
check('ignores comment lines', !isset($env['# comment']));

echo "\nBase path detection\n";
check('vhost / php -S (root)', Request::detectBasePath('/health', '/index.php') === '');
check('XAMPP project folder', Request::detectBasePath('/stockpilot-360/health', '/stockpilot-360/public/index.php') === '/stockpilot-360');
check('direct /public access', Request::detectBasePath('/stockpilot-360/public/health', '/stockpilot-360/public/index.php') === '/stockpilot-360/public');

echo "\nRouter\n";
$router = new Router();
$router->get('/', static fn (Request $r, array $p): Response => Response::html('home'));
$router->get('/products/{id}', static fn (Request $r, array $p): Response => Response::html('product ' . $p['id']));
$router->post('/login', static fn (Request $r, array $p): Response => Response::html('logged in'));

$home = $router->dispatch(new Request('GET', '/'));
check('GET / returns 200', $home->status() === 200 && $home->body() === 'home');

$product = $router->dispatch(new Request('GET', '/products/42'));
check('captures {id} parameter', $product->body() === 'product 42');

$slash = $router->dispatch(new Request('GET', '/products/42/'));
check('ignores trailing slash', $slash->status() === 200);

$missing = $router->dispatch(new Request('GET', '/nope'));
check('unknown path returns 404', $missing->status() === 404);

$wrong = $router->dispatch(new Request('GET', '/login'));
check('wrong method returns 405', $wrong->status() === 405);
check('405 sends Allow header', $wrong->header('Allow') === 'POST');

$head = $router->dispatch(new Request('HEAD', '/'));
check('HEAD is served by the GET route', $head->status() === 200);

echo "\nResponse\n";
$json = Response::json(['ok' => true], 201);
check('json sets status', $json->status() === 201);
check('json sets content type', str_starts_with((string) $json->header('Content-Type'), 'application/json'));
check('json body decodes', json_decode($json->body(), true) === ['ok' => true]);

echo "\nHelpers\n";
check('e() escapes HTML', e('<script>"x"</script>') === '&lt;script&gt;&quot;x&quot;&lt;/script&gt;');

echo "\n$passed passed, $failed failed\n";
exit($failed === 0 ? 0 : 1);
