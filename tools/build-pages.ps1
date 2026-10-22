# Wraps each partials/<page>.main.html in the shared head/header/footer taken from index.html.
# Usage: powershell -File tools\build-pages.ps1
$root = Split-Path -Parent (Split-Path -Parent $MyInvocation.MyCommand.Path)
$html = Join-Path $root "html"
$utf8 = New-Object Text.UTF8Encoding $false
$index = [IO.File]::ReadAllText((Join-Path $html "index.html"), $utf8)

$top = $index.Substring(0, $index.IndexOf("<main>"))
$bottom = $index.Substring($index.IndexOf("</main>") + "</main>".Length)

# Keep this file ASCII-only (Windows PowerShell 5.1 reads BOM-less files as ANSI); use HTML entities.
$pages = @(
  @{ file = 'about.html'; title = 'About Us | DMC Nuts &#8212; Deepak Mange &amp; Co.'; desc = 'Deepak Mange &amp; Co. imports, processes and distributes premium almonds from Navi Mumbai''s APMC Market since 2018.'; nav = 'about.html' },
  @{ file = 'shop.html'; title = 'Almond Varieties | Wholesale Almond Bags | DMC Nuts'; desc = 'Choose from nine graded almond varieties in 15 Kg and 30 Kg bags. Wholesale prices on request.'; nav = 'shop.html' },
  @{ file = 'product.html'; title = 'Orange Bag Almonds 15 Kg | Wholesale | DMC Nuts'; desc = 'Orange Bag almonds, 15 Kg &#8212; grain 100&#8211;105, count 29&#8211;30. Sorted, graded and hygienically packed. Call for today''s wholesale rate.'; nav = 'shop.html' }
)

foreach ($p in $pages) {
  $partial = Join-Path $html ("partials\" + ($p.file -replace '\.html$', '.main.html'))
  if (-not (Test-Path $partial)) { continue }
  $main = [IO.File]::ReadAllText($partial, $utf8)
  $t = $top
  $t = [regex]::Replace($t, "<title>.*?</title>", "<title>$($p.title)</title>")
  $t = [regex]::Replace($t, '<meta name="description" content=".*?" />', "<meta name=""description"" content=""$($p.desc)"" />")
  $t = $t.Replace('<a href="index.html" class="is-current">Home</a>', '<a href="index.html">Home</a>')
  $t = $t.Replace("<a href=""$($p.nav)"">", "<a href=""$($p.nav)"" class=""is-current"">")
  # The mobile drawer shares hrefs with the main nav; keep it unmarked.
  $drawerStart = $t.IndexOf('<nav class="dmc-drawer"')
  $t = $t.Substring(0, $drawerStart) + $t.Substring($drawerStart).Replace(' class="is-current"', '')
  $b = $bottom
  # Three.js is only needed where a 3D scene exists (home hero, about particles).
  if ($main -notmatch 'data-dmc-(nuts|particles)') {
    $b = [regex]::Replace($b, '\s*<script src="[^"]*three[^"]*"></script>', '')
    $b = [regex]::Replace($b, '\s*<script src="assets/js/dmc-three.js"></script>', '')
  }
  [IO.File]::WriteAllText((Join-Path $html $p.file), $t + "<main>" + $main + "</main>" + $b, $utf8)
  "built $($p.file)"
}
