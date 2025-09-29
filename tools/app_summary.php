<?php
// Simple, human-friendly PHP summary page for the IBA POS application
// Location: tools/app_summary.php
// Usage: open in browser or run `php -S localhost:8001 -t .` from project root and visit http://localhost:8001/tools/app_summary.php

header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>IBA POS - Ringkasan Aplikasi</title>
  <style>
    body{font-family:Inter,Segoe UI,Helvetica,Arial,sans-serif;margin:24px;color:#111}
    h1{color:#0b5d8a}
    pre{background:#f6f8fa;padding:12px;border-radius:6px;overflow:auto}
    .section{margin-bottom:20px}
    img.erd{max-width:100%;border:1px solid #ddd}
    table{border-collapse:collapse;width:100%}
    td,th{border:1px solid #eee;padding:8px;text-align:left}
  .erd-toolbar{margin-bottom:8px;display:flex;gap:8px;align-items:center}
  .erd-toolbar button{padding:6px 10px;border-radius:6px;border:1px solid #cbd5e0;background:white;cursor:pointer}
  .erd-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px}
  .card{border:1px solid #e2e8f0;border-radius:8px;padding:10px;background:white;box-shadow:0 1px 2px rgba(2,6,23,0.03)}
  .card h4{margin:0 0 6px 0;color:#2b6cb0}
  .col-list{font-family:monospace;font-size:13px;line-height:1.4;color:#111}
  .relations{margin-top:12px;border-top:1px solid #f1f5f9;padding-top:12px}
  .rel-item{padding:6px 0;border-bottom:1px dashed #eef2f7}
  .rel-item small{color:#475569}
  .export-btn{margin-left:auto}
  .schema-details{display:none;margin-top:12px;padding:12px;border-radius:6px;background:#fff;box-shadow:0 1px 2px rgba(0,0,0,0.04)}
  </style>
</head>
<body>
  <h1>IBA POS — Ringkasan Singkat (Human-friendly PHP output)</h1>

  <?php
  // Core metadata as an associative array
  $summary = [
    'name' => 'IBA POS',
    'description' => 'Point of Sale web application untuk multi-lokasi dengan inventory, purchases, sales, dan printing struk thermal.',
    'tech' => [
      'framework' => 'Laravel 12 (PHP 8.2+)',
      'frontend' => 'Alpine.js + Tailwind CSS (Vite)',
      'auth' => 'Spatie Laravel Permission (RBAC) + Laravel Sanctum',
      'database' => 'MySQL (XAMPP) in production; PHPUnit uses SQLite in-memory for tests'
    ],
    'architecture' => [
      'pattern' => 'MVC + Service layer (InventoryService, SalesPostingService, PurchasePostingService, InvoiceGenerator)',
      'concurrency' => 'DB transactions + row-level locking for invoice counters and stock updates',
      'audit' => 'stock_ledger table records every stock change'
    ],
    'key_features' => [
      'POS core (product search, cart, checkout, draft sales)',
      'Multi-location stock and scoping (source_location_id on sale_items)',
      'Average costing per location (stocks.avg_cost) and COGS calculation',
      'Purchases with landed cost allocation (weight-based + largest remainder)',
      'Stock mutasi (inter-location transfer) with atomic ledger entries',
      'Receipt printing (58mm/80mm) via postMessage to a printable template',
      'RBAC using Spatie with seeded roles & permissions',
      'Developer Mode (local-only auto-login & permission bypass) - use carefully'
    ],
    'db_highlights' => [
      'stocks' => 'qty, avg_cost per (product, location)',
      'stock_ledger' => 'ref_type/ref_id, qty_change, balance_after, cost_per_unit_at_time',
      'sales' => 'draft/posted/void lifecycle, sales_payments multi-payment',
      'purchases' => 'draft/received/posted/void, freight/loading/unloading costs'
    ],
    'important_routes' => [
      '/pos' => 'Main POS UI',
      '/products' => 'Products CRUD + live search',
      '/api/products/search' => 'AJAX search (no middleware by design)',
      '/api/locations' => 'List locations for current user (includes phone for receipts)',
      '/admin/users/{id}/restore' => 'Restore soft-deleted user (this repo includes a feature test)'
    ],
    'dev_workflow' => [
      'commands' => [
        'composer install',
        'php artisan migrate',
        'php artisan test'
      ],
      'notes' => 'Use PowerShell on Windows as project docs suggest; phpunit.xml is configured to use sqlite in-memory for tests.'
    ],
    'security_notes' => [
      'developer_mode' => 'When enabled (local), it may auto-login and bypass permissions. Do not enable in production.',
      'permissions' => 'Spatie permission middleware must be the original classes to avoid 500 errors'
    ]
  ];

  // Print human-readable sections
  function render_section($title, $content) {
    echo "<div class=\"section\"><h2>".htmlspecialchars($title)."</h2>";
    if (is_array($content)) {
      echo "<pre>".htmlspecialchars(print_r($content, true))."</pre>";
    } else {
      echo "<p>".nl2br(htmlspecialchars($content))."</p>";
    }
    echo "</div>";
  }

  render_section('Ringkasan Aplikasi', $summary['description']);
  render_section('Metadata & Teknologi', $summary['tech']);
  render_section('Arsitektur & Pola', $summary['architecture']);
  render_section('Fitur Utama', $summary['key_features']);
  render_section('Database — Highlight tabel penting', $summary['db_highlights']);
  render_section('Rute Penting', $summary['important_routes']);
  render_section('Alur Pengembangan Singkat', $summary['dev_workflow']);
  render_section('Catatan Keamanan', $summary['security_notes']);

  echo '<div class="section"><h2>ERD — Diagram (card view)</h2>';

  // Attempt to generate a 100% accurate schema by booting Laravel, running migrations in an in-memory SQLite, and introspecting.
  $schema = [];
  $accurate = false;
  try {
    // ensure environment uses in-memory sqlite for this request
    putenv('DB_CONNECTION=sqlite');
    putenv('DB_DATABASE=:memory:');
    putenv('APP_ENV=testing');

    // bootstrap the framework
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    // run migrations in-memory
    $kernel->call('migrate', ['--force' => true]);

    // introspect sqlite tables
    $db = $app->make(Illuminate\Database\DatabaseManager::class);
    $conn = $db->connection();
    $tables = $conn->select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
    foreach ($tables as $trow) {
      $t = $trow->name;
      $schema[$t] = ['columns' => [], 'pks' => [], 'fks' => []];
      // columns
      $cols = $conn->select("PRAGMA table_info('" . $t . "')");
      foreach ($cols as $col) {
        // cid, name, type, notnull, dflt_value, pk
        $cname = $col->name;
        $ctype = $col->type ?: 'text';
        $nullable = ($col->notnull == 0);
        $pk = ($col->pk == 1);
        $schema[$t]['columns'][$cname] = ['type' => $ctype, 'nullable' => $nullable, 'pk' => $pk, 'fk' => null];
        if ($pk) $schema[$t]['pks'][] = $cname;
      }
      // foreign keys
      $fks = $conn->select("PRAGMA foreign_key_list('" . $t . "')");
      foreach ($fks as $fk) {
        // id, seq, table, from, to, on_update, on_delete, match
        $col = $fk->from;
        $refTable = $fk->table;
        $refCol = $fk->to;
        $schema[$t]['fks'][] = [$col, $refTable, $refCol];
        if (isset($schema[$t]['columns'][$col])) {
          $schema[$t]['columns'][$col]['fk'] = $refTable . '.' . $refCol;
        }
      }
    }
    $accurate = true;
  } catch (Throwable $e) {
    // fallback to regex parsing below
    $accurate = false;
  }

  if (!$accurate) {
    // fallback: regex-based best-effort parsing (existing implementation)
    $migrationsPath = __DIR__ . '/../database/migrations';
    foreach (glob($migrationsPath . '/*.php') as $mfile) {
      $txt = file_get_contents($mfile);
      if (preg_match("/Schema::create\(\s*'([^']+)'/", $txt, $m)) {
        $table = $m[1];
      } elseif (preg_match("/Schema::table\(\s*'([^']+)'/", $txt, $m2)) {
        $table = $m2[1];
      } else {
        continue;
      }
      if (!isset($schema[$table])) $schema[$table] = ['columns' => [], 'pks' => [], 'fks' => []];
      if (preg_match_all("/\$table->\s*([a-zA-Z_]+)\s*\(\s*'([^']+)'\s*\)([^;]*);/U", $txt, $cols2, PREG_SET_ORDER)) {
        foreach ($cols2 as $c) {
          $method = $c[1]; $cname = $c[2]; $rest = $c[3];
          $nullable = (strpos($rest, '->nullable') !== false) ? true : false;
          $isPk = in_array($cname, $schema[$table]['pks'], true) || ($method === 'id');
          $schema[$table]['columns'][$cname] = ['type' => $method, 'nullable' => $nullable, 'pk' => $isPk, 'fk' => null];
        }
      }
      // detect primary and foreign keys & morphs & timestamps as before (kept minimal)
      if (strpos($txt, 'softDeletes(') !== false || strpos($txt, 'softDeletes()') !== false) {
        $schema[$table]['columns']['deleted_at'] = ['type' => 'timestamp', 'nullable' => true, 'pk' => false, 'fk' => null];
      }
      if (strpos($txt, 'timestamps(') !== false || strpos($txt, 'timestamps()') !== false) {
        $schema[$table]['columns']['created_at'] = ['type' => 'timestamp', 'nullable' => false, 'pk' => false, 'fk' => null];
        $schema[$table]['columns']['updated_at'] = ['type' => 'timestamp', 'nullable' => false, 'pk' => false, 'fk' => null];
      }
    }
  }

  // Render toolbar and export button
  echo '<div class="erd-toolbar">';
  echo '<div><strong>ERD (cards)</strong></div>';
  echo '<button id="toggle-details">Toggle details (tables/columns)</button>';
  echo '<button id="export-png" class="export-btn">Export ERD as PNG</button>';
  echo '</div>';

  // Render card grid
  echo '<div id="erd-area">';
  echo '<div class="erd-grid">';
  foreach ($schema as $t => $meta) {
    echo '<div class="card" data-table="' . htmlspecialchars($t) . '">';
    echo '<h4>' . htmlspecialchars($t) . '</h4>';
    echo '<div class="col-list">';
    echo '<ul>';
    foreach ($meta['columns'] as $cname => $info) {
      $badges = [];
      if (!empty($info['pk'])) $badges[] = 'PK';
      if (!empty($info['fk'])) $badges[] = 'FK->' . $info['fk'];
      if (!empty($info['nullable'])) $badges[] = 'nullable';
      $badgeText = $badges ? ' (' . implode(', ', $badges) . ')' : '';
      echo '<li>' . htmlspecialchars($cname) . ' : ' . htmlspecialchars($info['type']) . htmlspecialchars($badgeText) . '</li>';
    }
    echo '</ul>';
    echo '</div>';
    echo '</div>';
  }
  echo '</div>';

  // Simple relations list hint (best-effort from known FK patterns)
  echo '<div class="relations">';
  echo '<h3>Relations (best-effort)</h3>';
  // We'll infer common FKs by searching for '_id' columns in schema
  foreach ($schema as $t => $cols) {
    foreach ($cols as $c) {
      if (preg_match('/^([a-z0-9_]+) : /', $c, $mcol)) {
        $colname = $mcol[1];
        if (substr($colname, -3) === '_id') {
          $ref = substr($colname, 0, -3);
          echo '<div class="rel-item"><strong>' . htmlspecialchars($t) . '.' . htmlspecialchars($colname) . '</strong> → <em>' . htmlspecialchars($ref) . '.id</em> <br><small>inferred foreign key</small></div>';
        }
      }
    }
  }
  echo '</div>';

  echo '</div>'; // end erd-area
  // Render a hidden schema-details panel (toggleable)
  echo '<div id="schema-details" class="schema-details">';
  echo '<h3>Schema — Tables & Columns (generated)</h3>';
  echo '<table><thead><tr><th style="width:220px">Table</th><th>Columns (name : type — flags)</th></tr></thead><tbody>';
  foreach ($schema as $t => $meta) {
    $colsText = [];
    foreach ($meta['columns'] as $cname => $info) {
      $flags = [];
      if (!empty($info['pk'])) $flags[] = 'PK';
      if (!empty($info['fk'])) $flags[] = 'FK->' . $info['fk'];
      if (!empty($info['nullable'])) $flags[] = 'nullable';
      $colsText[] = $cname . ' : ' . $info['type'] . ($flags ? ' (' . implode(', ', $flags) . ')' : '');
    }
    echo '<tr><td><strong>' . htmlspecialchars($t) . '</strong></td><td>' . htmlspecialchars(implode(', ', $colsText)) . '</td></tr>';
  }
  echo '</tbody></table>';
  echo '</div>';

  echo '</div>';
  ?>

  <div class="section">
    <h2>How to open this PHP summary</h2>
    <p>Two quick options:</p>
    <ol>
      <li>Run PHP built-in server from project root: <pre>php -S localhost:8001 -t .</pre> Then open <a href="http://localhost:8001/tools/app_summary.php" target="_blank">http://localhost:8001/tools/app_summary.php</a></li>
      <li>Open this file in your editor to read the PHP source: <code>tools/app_summary.php</code></li>
    </ol>
  </div>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" referrerpolicy="no-referrer"></script>
<script>
  (function(){
    const toggle = document.getElementById('toggle-details');
    const details = document.getElementById('schema-details');
    const exportBtn = document.getElementById('export-png');
    const erdArea = document.getElementById('erd-area');
    if(toggle && details) toggle.addEventListener('click', function(){ details.style.display = (details.style.display === 'block') ? 'none' : 'block'; });
    if(exportBtn && erdArea) exportBtn.addEventListener('click', function(){
      // ensure details visible for export
      if(details) details.style.display = 'block';
      html2canvas(erdArea, {backgroundColor: null, scale: 2}).then(function(canvas){
        const a = document.createElement('a');
        a.href = canvas.toDataURL('image/png');
        a.download = 'ibapos-erd.png';
        a.click();
      }).catch(function(err){ alert('Export failed: ' + err); });
    });
  })();
</script>
</html>
