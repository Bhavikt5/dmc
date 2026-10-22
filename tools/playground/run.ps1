# Disposable WordPress test site (WordPress Playground) with WooCommerce, Elementor, Hello Elementor,
# the dmc-revamp plugin, the 9 products from the CSV and the Home/About Elementor templates.
# Usage: powershell -ExecutionPolicy Bypass -File tools\playground\run.ps1   then open http://127.0.0.1:9400
$root = Split-Path -Parent (Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path))
$data = Join-Path $root 'tools\playground\data'
New-Item -ItemType Directory -Force (Join-Path $data 'img') | Out-Null
Copy-Item (Join-Path $root 'tools\playground\*.php') $data -Force
Copy-Item (Join-Path $root 'wordpress\elementor-templates\*.json') $data -Force
Copy-Item (Join-Path $root 'wordpress\dmc-products-import.csv') $data -Force
Copy-Item (Join-Path $root 'html\assets\img\products\*.jpg') (Join-Path $data 'img') -Force
Set-Location $root
npx -y @wp-playground/cli@3.1.40 server --port=9400 --blueprint=tools/playground/blueprint.json --mount=wordpress/dmc-revamp:/wordpress/wp-content/plugins/dmc-revamp --mount=tools/playground/data:/wordpress/wp-content/dmc-test
